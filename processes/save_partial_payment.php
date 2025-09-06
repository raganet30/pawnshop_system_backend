<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php"; // updateCOH(), insertCashLedger(), logAudit()

header('Content-Type: application/json');

try {
    // --- Authentication ---
    if (!isset($_SESSION['user'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }

    $pawn_id = $_POST['pawn_id'] ?? null;
    $partial_amount = isset($_POST['partial_amount']) ? floatval($_POST['partial_amount']) : 0;
    $notes = $_POST['ppNotes'] ?? "";
    $user_id = $_SESSION['user']['id'];
    $branch_id = $_SESSION['user']['branch_id'];

    if (!$pawn_id || $partial_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID or amount."]);
        exit;
    }

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawned item not found or already closed."]);
        exit;
    }

    $current_principal = floatval($pawn['amount_pawned']);
    $interest_rate = floatval($pawn['interest_rate'] ?? 6) / 100; // convert to decimal

    // --- Calculate months pawned (min 1 month, assume 31-day month) ---
    $datePawned = new DateTime($pawn['date_pawned']);
    $today = new DateTime();
    $days_diff = $datePawned->diff($today)->days;
    $months = max(1, ceil($days_diff / 31));

    // --- Default interest ---
    $interest_due = round($current_principal * $interest_rate * $months, 2);

    // --- Fetch previous partial payments ---
    $stmtPartial = $pdo->prepare("
        SELECT created_at 
        FROM partial_payments 
        WHERE pawn_id = ? 
        ORDER BY created_at DESC
    ");
    $stmtPartial->execute([$pawn_id]);
    $partialHistory = $stmtPartial->fetchAll(PDO::FETCH_ASSOC);

    // --- Check if interest already prepaid in current period ---
    $prepaid = false;
    if (!empty($partialHistory)) {
        foreach ($partialHistory as $pp) {
            $ppDate = new DateTime($pp['created_at']);

            $periodStart = clone $datePawned;
            $periodStart->modify('+' . ($months - 1) . ' months');

            $periodEnd = clone $datePawned;
            $periodEnd->modify('+' . $months . ' months');

            if ($ppDate >= $periodStart && $ppDate <= $periodEnd) {
                $prepaid = true;
                break;
            }
        }
    }

    if ($prepaid) {
        $interest_due = 0;
    }

    // --- Allocate partial payment ---
    $principal_paid = $partial_amount;
    $new_principal = $current_principal - $principal_paid;
    if ($new_principal < 0) $new_principal = 0;

    $total_paid = $principal_paid + $interest_due;

    // --- Insert into partial_payments ---
    $stmt = $pdo->prepare("
        INSERT INTO partial_payments
        (pawn_id, branch_id, amount_paid, interest_paid, principal_paid, remaining_principal, user_id, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $total_paid,
        $interest_due,
        $principal_paid,
        $new_principal,
        $user_id,
        $notes
    ]);
    $pp_id = $pdo->lastInsertId();

    // --- Update pawned_items principal ---
    $stmt = $pdo->prepare("
        UPDATE pawned_items 
        SET amount_pawned = ?, has_partial_payments = 1, updated_by = ?, updated_at = NOW() 
        WHERE pawn_id = ?
    ");
    $stmt->execute([$new_principal, $user_id, $pawn_id]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $total_paid, 'add');

    // --- Ledger entry ---
    $ledgerNotes = "Partial payment: Principal ₱" . number_format($principal_paid, 2) .
                   " | Interest ₱" . number_format($interest_due, 2);
    insertCashLedger(
        $pdo,
        $branch_id,
        "partial_payment",
        "in",
        $total_paid,
        "partial_payments",
        $pp_id,
        "Partial Payment (Pawn ID #$pawn_id)",
        $ledgerNotes,
        $user_id
    );

    // --- Audit log ---
    $log_desc = sprintf(
        "Partial payment recorded. Pawn ID: %d, Total Amount Paid: ₱%s (Principal:₱%s Interest:₱%s), New Principal: ₱%s",
        $pawn_id,
        number_format($total_paid, 2),
        number_format($principal_paid, 2),
        number_format($interest_due, 2),
        number_format($new_principal, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment', $log_desc);

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment of ₱" . number_format($partial_amount, 2) . " saved!<br>Remaining Principal: ₱" . number_format($new_principal, 2) . "<br>Cash On Hand: +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal,
        "interest_due" => $interest_due
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
