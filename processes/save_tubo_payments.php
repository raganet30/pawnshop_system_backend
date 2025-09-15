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



    // --- Get POST data ---
    $pawn_id = $_POST['pawn_id'] ?? null;
    $branch_id = $_POST['branch_id'] ?? $_SESSION['user']['branch_id'];
    $payment_date = $_POST['payment_date'] ?? null;
    $months_covered = isset($_POST['months_covered']) ? intval($_POST['months_covered']) : 1;
    $period_start = $_POST['period_start'] ?? null;
    $period_end = $_POST['period_end'] ?? null;
    $interest_amount = isset($_POST['interest_amount']) ? floatval($_POST['interest_amount']) : 0;
    $new_due_date = $_POST['new_due_date'] ?? null;
    $notes = $_POST['notes'] ?? "";
    $user_id = $_SESSION['user']['id'];

    // --- Validate required fields ---
    if (!$pawn_id || !$payment_date || !$period_start || !$period_end || $interest_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Missing required fields or invalid interest amount."]);
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

    $amount_pawned = floatval($pawn['amount_pawned']);
    $pawn_interest_rate = floatval($pawn['interest_rate'] ?? 0); // decimal

    // --- Insert into tubo_payments ---
    $payment_type = 'renewal'; // tubo payments considered as renewal

    $stmt = $pdo->prepare("
        INSERT INTO tubo_payments
        (pawn_id, payment_type, branch_id, date_paid, period_start, period_end, months_covered, new_due_date, interest_rate, interest_amount, cashier_id, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $pawn_id,
        $payment_type,
        $branch_id,
        $payment_date,
        $period_start,
        $period_end,
        $months_covered,
        $new_due_date,
        $pawn_interest_rate,
        $interest_amount,
        $user_id,
        $notes
    ]);

    $tubo_id = $pdo->lastInsertId();

    // --- Update pawned_items: set new due date and has_tubo_payments=1 ---
    $stmt = $pdo->prepare("
        UPDATE pawned_items 
        SET current_due_date = :new_due_date, has_tubo_payments = 1, updated_by = :user_id, updated_at = NOW() 
        WHERE pawn_id = :pawn_id
    ");
    $stmt->execute([
        ':new_due_date' => $new_due_date,
        ':user_id' => $user_id,
        ':pawn_id' => $pawn_id
    ]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $interest_amount, 'add');

    // --- Insert ledger entry ---
    $ledgerNotes = "Tubo payment of ₱" . number_format($interest_amount, 2) . " for Pawn ID #$pawn_id. Period: $period_start to $period_end";
    insertCashLedger(
        $pdo,
        $branch_id,
        "tubo_payment",
        "in",
        $interest_amount,
        "tubo_payments",
        $tubo_id,
        "Tubo Payment (Pawn ID #$pawn_id)",
        $ledgerNotes,
        $user_id
    );

    // --- Audit log ---
    $log_desc = sprintf(
        "Tubo payment recorded. Pawn Item: %s, Interest Amount: ₱%s, Period: %s to %s, Months Covered: %d, New Due Date: %s",
        $pawn['unit_description'],
        number_format($interest_amount, 2),
        $period_start,
        $period_end,
        $months_covered,
        $new_due_date
    );
    logAudit($pdo, $user_id, $branch_id, 'Tubo Payment', $log_desc);

    echo json_encode([
        "status" => "success",
        "message" => "Cash on Hand +₱" . number_format($interest_amount, 2) . "<br>New due date: $new_due_date",
        "pawn_id" => $pawn_id,
        "tubo_id" => $tubo_id
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
