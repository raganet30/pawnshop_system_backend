<?php
session_start();
require_once "../config/db.php";

// Ensure logged in
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

$id = intval($_POST['id']);
$fullname = trim($_POST['fullname'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$address = trim($_POST['address'] ?? '');

if (!$id || $fullname === '' || $contact === '' || $address === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE customers SET full_name = ?, contact_no = ?, address = ? WHERE customer_id = ?");
    $success = $stmt->execute([$fullname, $contact, $address, $id]);

    if ($success) {
        echo json_encode(["success" => true, "message" => "Pawner updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update pawner"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
