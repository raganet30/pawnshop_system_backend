<?php
session_start();
require_once "../config/db.php";

// Ensure logged in
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing ID"]);
    exit();
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$id]);
    $pawner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pawner) {
        echo json_encode($pawner);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Pawner not found"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
