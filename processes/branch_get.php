<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$id = intval($_GET['id'] ?? 0);

try {
    $stmt = $pdo->prepare("SELECT * FROM branches WHERE branch_id = ?");
    $stmt->execute([$id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($branch ?: []);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
