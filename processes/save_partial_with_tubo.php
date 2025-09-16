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
    $ppDatePaid = $_POST['ppDatePaid'] ?? date('Y-m-d');
    $notes = $_POST['notes'] ?? "";
    $user_id = $_SESSION['user']['id'];
    $branch_id = $_SESSION['user']['branch_id'];

    // Tubo-related fields from UI
    $period_start     = $_POST['period_start']     ?? null;
    $period_end       = $_POST['period_end']       ?? null;
    $months_covered   = $_POST['months_covered']   ?? 1;
    $new_due_date     = $_POST['new_due_date']     ?? null;
    $interest_amount  = isset($_POST['interest_amount']) ? floatval($_POST['interest_amount']) : 0;

    if (!$pawn_id || $partial_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID or amount."]);
        exit;
    }

    $paymentDate = new DateTime($ppDatePaid);

    // --- Start Transaction ---
    $pdo->beginTransaction();

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned' FOR UPDATE");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawned item not found or already closed.");
    }

    $pawn_item = htmlspecialchars($pawn['unit_description']);
    $current_principal = floatval($pawn['amount_pawned']);
    $interest_rate = floatval($pawn['interest_rate']);

    // --- Compute new principal ---
    $new_principal = $current_principal - $partial_amount;
    if ($new_principal < 0) $new_principal = 0;

    // -------------------------
    // 1. Insert into partial_payments
    // -------------------------
    $stmt = $pdo->prepare("
        INSERT INTO partial_payments
        (pawn_id, branch_id, amount_paid, interest_paid, principal_paid, remaining_principal, status, user_id, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, ?)
    ");
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $partial_amount, // amount_paid = partial only
        0,               // interest_paid = 0
        $partial_amount, // principal_paid
        $new_principal,  // remaining principal
        $user_id,
        $notes,
        $paymentDate->format('Y-m-d H:i:s')
    ]);
    $pp_id = $pdo->lastInsertId();

    // -------------------------
    // 2. Insert into tubo_payments (values from UI)
    // -------------------------
    $stmt = $pdo->prepare("
        INSERT INTO tubo_payments
        (pawn_id, payment_type, branch_id, date_paid, period_start, period_end, months_covered, new_due_date,
         interest_rate, interest_amount, cashier_id, notes, created_at)
        VALUES (?, 'renewal', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    // $txn_code = uniqid("TP"); // unique txn code
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $paymentDate->format('Y-m-d'),
        $period_start,
        $period_end,
        $months_covered,
        $new_due_date,
        $interest_rate,
        $interest_amount,
        $user_id,
        "Partial Payment + Tubo",
        $paymentDate->format('Y-m-d H:i:s')
    ]);
    $tubo_id = $pdo->lastInsertId();

    // -------------------------
    // 3. Update pawned_items
    // -------------------------
    $stmt = $pdo->prepare("
        UPDATE pawned_items
        SET amount_pawned = ?, current_due_date = ?, has_partial_payments = 1, has_tubo_payments=1, updated_by = ?, updated_at = NOW()
        WHERE pawn_id = ?
    ");
    $stmt->execute([
        $new_principal,
        $new_due_date, // from UI
        $user_id,
        $pawn_id
    ]);

    // -------------------------
    // 4. Update branch COH
    // -------------------------
    $total_inflow = $partial_amount + $interest_amount;
    updateCOH($pdo, $branch_id, $total_inflow, 'add');

    // -------------------------
    // 5. Ledger entry
    // -------------------------
    $ledgerNotes = "Partial ₱" . number_format($partial_amount, 2) . 
                   " | Interest ₱" . number_format($interest_amount, 2);
    insertCashLedger(
        $pdo,
        $branch_id,
        "partial_payment",
        "in",
        $total_inflow,
        "partial_payments",
        $pp_id,
        "Partial + Tubo (Pawn ID #$pawn_id)",
        $ledgerNotes,
        $user_id
    );

    // -------------------------
    // 6. Audit log
    // -------------------------
    $log_desc = sprintf(
        "Partial payment recorded. Pawn Item: %s, Partial ₱%s, Interest ₱%s, New Principal ₱%s, New Due Date %s",
        $pawn_item,
        number_format($partial_amount, 2),
        number_format($interest_amount, 2),
        number_format($new_principal, 2),
        $new_due_date
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment + Tubo', $log_desc);

    // --- Commit ---
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment and tubo saved successfully.<br>
                      Partial: ₱" . number_format($partial_amount, 2) . "<br>
                      Interest: ₱" . number_format($interest_amount, 2) . "<br>
                      Remaining Principal: ₱" . number_format($new_principal, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal,
        "new_due_date" => $new_due_date
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
