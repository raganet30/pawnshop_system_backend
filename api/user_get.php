<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["success" => false, "message" => "Missing user ID"]);
    exit();
}

// Fetch user from DB
$stmt = $pdo->prepare("SELECT photo_path, user_id, full_name, username, branch_id, role, status FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Ensure photo_path is clean relative path (no ../)
    $user['photo_path'] = !empty($user['photo_path']) ? $user['photo_path'] : null;
    echo json_encode($user);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
