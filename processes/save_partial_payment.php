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
    $interest_due = isset($_POST['interest_due']) ? floatval($_POST['interest_due']) : 0;
    $total_paid = isset($_POST['total_payable']) ? floatval($_POST['total_payable']) : $partial_amount + $interest_due;
    $ppDatePaid = $_POST['ppDatePaid'] ?? date('Y-m-d');
    $notes = $_POST['notes'] ?? "";
    $user_id = $_SESSION['user']['id'];
    $branch_id = $_SESSION['user']['branch_id'];

    if (!$pawn_id || $partial_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID or amount."]);
        exit;
    }

    $paymentDate = new DateTime($ppDatePaid);

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawned item not found or already closed."]);
        exit;
    }

    $current_principal = floatval($pawn['amount_pawned']);
    $current_due_date = new DateTime($pawn['current_due_date']);

    // --- Compute new principal ---
    $new_principal = $current_principal - $partial_amount;
    if ($new_principal < 0) $new_principal = 0;

    // --- Insert partial payment ---
    $stmt = $pdo->prepare("
        INSERT INTO partial_payments
        (pawn_id, branch_id, amount_paid, interest_paid, principal_paid, remaining_principal, user_id, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $total_paid,
        $interest_due,
        $partial_amount,
        $new_principal,
        $user_id,
        $notes,
        $paymentDate->format('Y-m-d H:i:s')
    ]);
    $pp_id = $pdo->lastInsertId();

    // --- Update current_due_date if tubo payments exist within period ---
    $stmtTubo = $pdo->prepare("SELECT * FROM tubo_payments WHERE pawn_id = ? ORDER BY date_paid DESC");
    $stmtTubo->execute([$pawn_id]);
    $tuboHistory = $stmtTubo->fetchAll(PDO::FETCH_ASSOC);

    $updatedDueDate = $current_due_date;
    foreach ($tuboHistory as $t) {
        $period_start = new DateTime($t['period_start']);
        $period_end = new DateTime($t['period_end']);
        if ($paymentDate >= $period_start && $paymentDate <= $period_end) {
            $updatedDueDate = $period_end;
            break;
        }
    }

    // --- Update pawned_items ---
    $stmt = $pdo->prepare("
        UPDATE pawned_items 
        SET amount_pawned = ?, current_due_date = ?, has_partial_payments = 1, updated_by = ?, updated_at = NOW()
        WHERE pawn_id = ?
    ");
    $stmt->execute([
        $new_principal,
        $updatedDueDate->format('Y-m-d'),
        $user_id,
        $pawn_id
    ]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $total_paid, 'add');

    // --- Ledger entry ---
    $ledgerNotes = "Partial payment: Principal ₱" . number_format($partial_amount, 2) . " | Interest ₱" . number_format($interest_due, 2);
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
        "Partial payment recorded. Pawn ID: %d, Total Amount Paid: ₱%s (Partial ₱%s + Interest ₱%s), New Principal: ₱%s",
        $pawn_id,
        number_format($total_paid, 2),
        number_format($partial_amount, 2),
        number_format($interest_due, 2),
        number_format($new_principal, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment', $log_desc);

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment of ₱" . number_format($partial_amount, 2) . " saved!<br>
                      Interest: ₱" . number_format($interest_due, 2) . "<br>
                      Remaining Principal: ₱" . number_format($new_principal, 2) . "<br>
                      Cash On Hand: +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
