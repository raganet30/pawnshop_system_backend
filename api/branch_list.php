<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["data" => []]);
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM branches ORDER BY created_at DESC");
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["data" => $branches]);
} catch (PDOException $e) {
    echo json_encode(["data" => [], "error" => $e->getMessage()]);
}
