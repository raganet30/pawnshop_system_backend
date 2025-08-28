<?php
session_start();
require_once "../config/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

try {
    $stmt = $pdo->query("SELECT id, fullname, contact, address, created_at FROM customers ORDER BY id DESC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "data" => $customers
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "data" => [],
        "error" => $e->getMessage()
    ]);
}
