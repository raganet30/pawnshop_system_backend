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
    $notes = $_POST['notes'] ?? "";
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
    $interest_rate = floatval($pawn['interest_rate'] ?? 0) / 100; // convert to decimal

    // --- Calculate months covered (min 1 month) ---
    $datePawned = new DateTime($pawn['date_pawned']);
    $today = new DateTime();
    $days_diff = $datePawned->diff($today)->days;
    $months = max(1, ceil($days_diff / 30));

    // --- Compute interest portion based on current principal ---
    $interest_due = round($current_principal * $interest_rate * $months, 2);

    // // --- Allocate partial payment ---
    // if ($partial_amount <= $interest_due) {
    //     // Entire payment goes to interest
    //     $interest_paid = $partial_amount;
    //     $principal_paid = 0;
    // } else {
    //     $interest_paid = $interest_due;
    //     $principal_paid = $partial_amount - $interest_paid;
    // }

    // --- Compute new remaining principal ---
    $new_principal = $current_principal - $partial_amount;
    // if ($new_principal < 0)
    //     $new_principal = 0;



    $total_paid = $partial_amount + $interest_due;

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
        $partial_amount,
        $new_principal,
        $user_id,
        $notes
    ]);
    $pp_id = $pdo->lastInsertId();

    // --- Update pawned_items principal ---
    $stmt = $pdo->prepare("UPDATE pawned_items SET amount_pawned = ?, has_partial_payments = 1, updated_by = ?, updated_at = NOW() WHERE pawn_id = ?");
    $stmt->execute([$new_principal, $user_id, $pawn_id]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $total_paid, 'add');

    // --- Ledger entry ---
    $ledgerNotes = "Partial payment: Principal ₱" . number_format($total_paid, 2) ."Partial Payment ₱:" . number_format($partial_amount, 2) . "Interest ₱" . number_format($interest_due, 2);
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
        "Partial payment recorded. Pawn ID: %d, Total Amount Paid: ₱%s (Partial Payment:₱%s Interest ₱%s), New Principal: ₱%s",
        $pawn_id,
        number_format($total_paid, 2),
        number_format($partial_amount, 2),
        number_format($interest_due, 2),
        number_format($new_principal, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment', $log_desc);

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment of ₱" . number_format($partial_amount, 2) . " saved!<br>Remaining Principal: ₱" . number_format($new_principal, 2) . "<br>Cash On Hand: +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
