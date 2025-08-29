<?php
// Prevent BOM/whitespace issues
header('Content-Type: application/json');
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$fullname = trim($_POST['full_name'] ?? '');
$contact = trim($_POST['contact_no'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($fullname === '' || $contact === '' || $address === '') {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO customers (full_name, contact_no, address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$fullname, $contact, $address]);

    echo json_encode(["success" => true, "message" => "Pawner added successfully"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
