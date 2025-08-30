<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["data"=>[]]);
    exit();
}

$sql = "SELECT u.user_id, u.photo_path, u.full_name, u.username, u.role, u.status, u.last_login, u.created_at,
        b.branch_name, b.branch_id
        FROM users u
        LEFT JOIN branches b ON u.branch_id = b.branch_id
        ORDER BY u.created_at DESC";

$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["data" => $users]);
