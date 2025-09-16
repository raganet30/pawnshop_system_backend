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

    // --- Compute new principal ---
    $new_principal = $current_principal - $partial_amount;
    if ($new_principal < 0) $new_principal = 0;

    // -------------------------
    // 1. Insert into partial_payments only
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
    // 2. Update pawned_items
    // -------------------------
    $stmt = $pdo->prepare("
        UPDATE pawned_items
        SET amount_pawned = ?, has_partial_payments = 1, updated_by = ?, updated_at = NOW()
        WHERE pawn_id = ?
    ");
    $stmt->execute([
        $new_principal,
        $user_id,
        $pawn_id
    ]);

    // -------------------------
    // 3. Update branch COH
    // -------------------------
    updateCOH($pdo, $branch_id, $partial_amount, 'add');

    // -------------------------
    // 4. Ledger entry
    // -------------------------
    $ledgerNotes = "Partial ₱" . number_format($partial_amount, 2);
    insertCashLedger(
        $pdo,
        $branch_id,
        "partial_payment",
        "in",
        $partial_amount,
        "partial_payments",
        $pp_id,
        "Partial Payment (Pawn ID #$pawn_id)",
        $ledgerNotes,
        $user_id
    );

    // -------------------------
    // 5. Audit log
    // -------------------------
    $log_desc = sprintf(
        "Partial payment recorded. Pawn Item: %s, Partial ₱%s, New Principal ₱%s",
        $pawn_item,
        number_format($partial_amount, 2),
        number_format($new_principal, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment', $log_desc);

    // --- Commit ---
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment saved successfully.<br>
                      Partial: ₱" . number_format($partial_amount, 2) . "<br>
                      Remaining Principal: ₱" . number_format($new_principal, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
