<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) throw new Exception("Unauthorized");

    $pawn_id = $_POST['pawn_id'] ?? null;
    $branch_id = $_SESSION['user']['branch_id'];
    $user_id = $_SESSION['user']['id'];
    $notes = $_POST['notes'] ?? "";

    if (!$pawn_id) throw new Exception("Invalid pawn ID.");

    // Fetch pawned item
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pawn) throw new Exception("Pawn record not found or already claimed.");

    $datePawned = new DateTime($pawn['date_pawned']);
    $claimDate = new DateTime();

    // Get branch interest rate (fallback to pawn rate)
    $stmt = $pdo->prepare("SELECT interest_rate FROM branches WHERE branch_id = ?");
    $stmt->execute([$pawn['branch_id']]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);
    $interest_rate = isset($branch['interest_rate']) ? floatval($branch['interest_rate']) : floatval($pawn['interest_rate'] ?? 0.06);

    $principal = floatval($pawn['amount_pawned']);

    // --- Determine last prepaid interest (from tubo_payments) ---
    $stmt = $pdo->prepare("SELECT MAX(period_end) AS last_period_end FROM tubo_payments WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastCovered = $row['last_period_end'] ? new DateTime($row['last_period_end']) : (clone $datePawned)->modify('+1 month');

    // --- Calculate unpaid months (always min 1 month) ---
    $diffDays = $lastCovered->diff($claimDate)->days;
    $unpaidMonths = max(1, ceil($diffDays / 30));
    $interest_amount = ($unpaidMonths > 0) ? round($principal * $interest_rate * $unpaidMonths, 2) : 0.00;

    // Penalty
    $penalty = isset($_POST['claimPenalty']) && $_POST['claimPenalty'] !== '' ? floatval($_POST['claimPenalty']) : 0.00;

    $total_paid = round($principal + $interest_amount + $penalty, 2);

    // --- Save claimant photo ---
    if (empty($_POST['claimantPhoto'])) throw new Exception("Claimant photo is required.");

    $photoData = $_POST['claimantPhoto'];
    if (strpos($photoData, 'base64,') !== false) $photoData = substr($photoData, strpos($photoData, 'base64,') + 7);
    $photoData = str_replace(' ', '+', $photoData);
    $photoBinary = base64_decode($photoData);

    $fileName = "claimant_{$pawn_id}_" . time() . ".png";
    $uploadDir = "../uploads/claimants/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filePath = $uploadDir . $fileName;
    file_put_contents($filePath, $photoBinary);
    $photoPathForDb = "uploads/claimants/" . $fileName;

    // --- Begin transaction ---
    $pdo->beginTransaction();

    // Insert into claims
    $stmt = $pdo->prepare("
        INSERT INTO claims
        (pawn_id, branch_id, date_claimed, interest_rate, interest_amount, principal_amount, penalty_amount, total_paid, cashier_id, notes, photo_path, created_at)
        VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pawn_id,
        $pawn['branch_id'],
        $interest_rate,
        $interest_amount,
        $principal,
        $penalty,
        $total_paid,
        $user_id,
        $notes,
        $photoPathForDb
    ]);

    // Update pawned item as claimed
    $stmt = $pdo->prepare("UPDATE pawned_items SET status='claimed', WHERE pawn_id=?");
    $stmt->execute([$pawn_id]);

    // Update branch cash on hand
    if ($total_paid > 0) updateCOH($pdo, $branch_id, $total_paid, 'add');

    // Insert cash ledger
    if ($total_paid > 0) {
        insertCashLedger(
            $pdo,
            $branch_id,
            "claim",
            "in",
            $total_paid,
            "claims",
            $pawn_id,
            "Claim (ID #{$pawn_id})",
            "Pawn ID #{$pawn_id} claimed; principal + unpaid interest + penalty",
            $user_id
        );
    }

    // Update partial payments
    $stmt = $pdo->prepare("UPDATE partial_payments SET status='settled' WHERE pawn_id=? AND status='active'");
    $stmt->execute([$pawn_id]);
    $stmt = $pdo->prepare("UPDATE partial_payments SET remaining_principal=0 WHERE pawn_id=? ORDER BY pp_id DESC LIMIT 1");
    $stmt->execute([$pawn_id]);

    // Audit log
    logAudit(
        $pdo,
        $user_id,
        $pawn['branch_id'],
        'Claim Pawned Item',
        "Claimed pawn ID: {$pawn_id}, Principal: ₱" . number_format($principal,2) . 
        ", Interest: ₱" . number_format($interest_amount,2) .
        ", Penalty: ₱" . number_format($penalty,2) .
        ", Total Paid: ₱" . number_format($total_paid,2)
    );

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Pawn item successfully claimed! Cash on Hand +₱" . number_format($total_paid,2),
        "pawn_id" => $pawn_id,
        "interest_charged" => number_format($interest_amount,2),
        "months_charged" => $unpaidMonths
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
