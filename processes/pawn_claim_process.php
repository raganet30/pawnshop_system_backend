<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

try {
    // --- Authentication ---
    if (!isset($_SESSION['user'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }

    $pawn_id = $_POST['pawn_id'] ?? null;
    $branch_id = $_SESSION['user']['branch_id'];
    $user_id = $_SESSION['user']['id'];
    $notes = $_POST['claimNotes'] ?? "";

    if (!$pawn_id) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID."]);
        exit;
    }

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawn record not found or already claimed."]);
        exit;
    }

    // --- Calculate months pawned ---
    $datePawned = new DateTime($pawn['date_pawned']);
    $now = new DateTime();
    $daysDiff = $datePawned->diff($now)->days;

    // min 1 month, ceil by 30 days
    $monthsPawned = max(1, ceil($daysDiff / 31));

    // --- Get branch interest rate ---
    $stmt = $pdo->prepare("SELECT interest_rate, cash_on_hand FROM branches WHERE branch_id = ?");
    $stmt->execute([$pawn['branch_id']]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    $interest_rate = floatval($branch['interest_rate'] ?? 0.06);
    $principal = floatval($pawn['amount_pawned']);

    // --- Check tubo payments (prepaid interest) ---
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(months_covered), 0) AS prepaid_months
        FROM tubo_payments
        WHERE pawn_id = ?
    ");
    $stmt->execute([$pawn_id]);
    $tubo = $stmt->fetch(PDO::FETCH_ASSOC);






    $prepaidMonths = intval($tubo['prepaid_months'] ?? 0);

    // --- Net months to pay now ---
    $netMonths = max(0, $monthsPawned - $prepaidMonths);

    // --- Determine current cover period ---
    $currentPeriodStart = clone $datePawned;
    $currentPeriodStart->modify('+' . ($monthsPawned - 1) . ' months');

    $currentPeriodEnd = clone $datePawned;
    $currentPeriodEnd->modify('+' . $monthsPawned . ' months');

    // --- Check partial payments if interest already paid within this cover period ---
    $stmt = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM partial_payments
    WHERE pawn_id = ?
      AND interest_paid > 0
      AND status = 'active'
      AND DATE(created_at) BETWEEN ? AND ?
");
    $stmt->execute([$pawn_id, $currentPeriodStart->format('Y-m-d'), $currentPeriodEnd->format('Y-m-d')]);
    $partialInterest = $stmt->fetch(PDO::FETCH_ASSOC);

    $interestAlreadyPaid = intval($partialInterest['cnt'] ?? 0) > 0;

    // --- Compute interest only if not prepaid and no partial interest in cover period ---
    if ($interestAlreadyPaid) {
        $interest_amount = 0.00;
    } else {
        $interest_amount = round($principal * $interest_rate * $netMonths, 2);
    }

    // --- Save claimant photo ---
    $photoPathForDb = null;
    if (!empty($_POST['claimantPhoto'])) {
        $photoData = $_POST['claimantPhoto'];
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $photoBinary = base64_decode($photoData);

        $fileName = "claimant_" . $pawn_id . "_" . time() . ".png";
        $uploadDir = "../uploads/claimants/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $fileName;
        file_put_contents($filePath, $photoBinary);
        $photoPathForDb = "uploads/claimants/" . $fileName;
    } else {
        echo json_encode(["status" => "error", "message" => "Claimant photo is required."]);
        exit;
    }

    // --- Penalty (if any) ---
    $penalty = isset($_POST['claimPenalty']) && $_POST['claimPenalty'] !== '' ? floatval($_POST['claimPenalty']) : 0.00;

   
  
    // --- Final total ---
    $total_paid = $principal + $interest_amount + $penalty;

    // --- Insert into claims ---
    $stmt = $pdo->prepare("
        INSERT INTO claims 
        (pawn_id, branch_id, date_claimed, months, interest_rate, interest_amount, principal_amount, penalty_amount, total_paid, cashier_id, notes, photo_path, created_at)
        VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pawn_id,               // pawn_id
        $pawn['branch_id'],     // branch_id
        $monthsPawned,          // months pawned (raw months)
        $interest_rate,         // interest_rate
        $interest_amount,       // interest_amount (after tubo adjustment)
        $principal,             // principal_amount
        $penalty,               // penalty_amount
        $total_paid,            // total_paid
        $user_id,               // cashier_id
        $notes,                 // notes
        $photoPathForDb         // photo_path
    ]);

    $claim_id = $pdo->lastInsertId();

    // --- Update pawned item ---
    $stmt = $pdo->prepare("UPDATE pawned_items SET status = 'claimed' WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $total_paid, 'add');

    // --- Insert into cash ledger ---
    if ($total_paid > 0) {
        $description = "Claim (ID #$claim_id)";
        $ledgerNotes = "Pawn ID #$pawn_id claimed with interest + penalty (if any)";

        insertCashLedger(
            $pdo,
            $branch_id,
            "claim",     // txn_type
            "in",        // direction
            $total_paid,
            "claims",    // ref_table
            $pawn_id,
            $description,
            $ledgerNotes,
            $user_id
        );
    }

    // --- Insert into audit_logs ---
    $description = sprintf(
        "Claimed pawn ID: %d, Unit: %s, Total Amount Paid: ₱%s",
        $pawn_id,
        $pawn['unit_description'],
        number_format($total_paid, 2)
    );

    logAudit(
        $pdo,
        $user_id,
        $pawn['branch_id'],
        'Claim Pawned Item',
        $description
    );

    // --- Settle partial payments ---
    $stmt = $pdo->prepare("UPDATE partial_payments SET status = 'settled' WHERE pawn_id = ? AND status = 'active'");
    $stmt->execute([$pawn_id]);

    $stmt = $pdo->prepare("UPDATE partial_payments SET remaining_principal = 0 WHERE pawn_id = ? ORDER BY pp_id DESC LIMIT 1");
    $stmt->execute([$pawn_id]);

    echo json_encode([
        "status" => "success",
        "message" => "Pawn item successfully claimed!<br>Cash on Hand +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
