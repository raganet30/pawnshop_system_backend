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

try {
    $stmt = $pdo->prepare("UPDATE pawned_items SET is_deleted = 1 WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);

    echo json_encode(["status" => "success", "message" => "Pawn moved to trash."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error: ".$e->getMessage()]);
}
?>
