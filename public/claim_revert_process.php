<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user = $_SESSION['user'];
if (!in_array($user['role'], ['admin','super_admin'])) {
    echo json_encode(["status" => "error", "message" => "Permission denied"]);
    exit();
}

$pawn_id = $_POST['pawn_id'] ?? null;
if (!$pawn_id || !is_numeric($pawn_id)) {
    echo json_encode(["status" => "error", "message" => "Invalid Pawn ID"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Get claim record
    $stmt = $pdo->prepare("SELECT * FROM claims WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$claim) {
        throw new Exception("Claim record not found.");
    }

    $branch_id = $claim['branch_id'];
    $total_paid = $claim['total_paid'];

    // 2. Deduct from cash_on_hand
    $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?")
        ->execute([$total_paid, $branch_id]);

    // 3. Revert pawn status
    $pdo->prepare("UPDATE pawned_items SET status = 'pawned', date_claimed = NULL, interest_amount = 0 WHERE pawn_id = ?")
        ->execute([$pawn_id]);

    // 4. Remove claim record
    $pdo->prepare("DELETE FROM claims WHERE pawn_id = ?")->execute([$pawn_id]);

    // 5. Log action
    // $fullname = $user['fullname'] ?? $user['username'];
    // logAction($pdo, $user['id'], $branch_id, 'revert_claim', 'pawned_items', $pawn_id,
    //     "$fullname reverted claim #$pawn_id back to pawned."
    // );

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Claim reverted successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
