<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user = $_SESSION['user'];
if (!in_array($user['role'], ['admin','super_admin'])) {
    echo json_encode(["status" => "error", "message" => "Permission denied"]);
    exit();
}

if (!isset($_POST['pawn_id']) || !is_numeric($_POST['pawn_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$pawn_id = (int)$_POST['pawn_id'];
$user_id = $user['id'];
try {
    $pdo->beginTransaction();

    // 1. Get pawn record
    $stmt = $pdo->prepare("SELECT amount_pawned, branch_id FROM pawned_items WHERE pawn_id = ? AND is_deleted = 0");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found or already deleted.");
    }

    $amount = (float)$pawn['amount_pawned'];
    $branch_id = (int)$pawn['branch_id'];
    // $user_id = $user['user_id'];

    // 2. Update branch cash_on_hand (add back pawned amount)
    $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand + ? WHERE branch_id = ?");
    $stmt->execute([$amount, $branch_id]);

    // 3. Log transaction in cash_ledger
    $stmt = $pdo->prepare("INSERT INTO cash_ledger 
        (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id, created_at) 
        VALUES (?, 'delete', 'in', ?, 'pawned_items', ?, 'Pawn deleted - moved to trash, amount refunded to COH', ?, NOW())");
    $stmt->execute([$branch_id, $amount, $pawn_id, $user_id]);

    // 4. Soft-delete pawn record
    $stmt = $pdo->prepare("UPDATE pawned_items SET is_deleted = 1 WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Pawn moved to trash and COH updated. +₱" . number_format($amount, 2)]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>
