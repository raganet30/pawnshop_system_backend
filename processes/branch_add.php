<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$branch_name   = trim($_POST['branch_name'] ?? '');
$branch_address = trim($_POST['branch_address'] ?? '');
$branch_phone  = trim($_POST['branch_phone'] ?? '');
$status        = trim($_POST['status'] ?? 'active');
$interest_rate = trim($_POST['interest_rate'] ?? 0);
// $cash_on_hand  = trim($_POST['cash_on_hand'] ?? 0);
$cash_on_hand  = 0; //set to ) if new branch added, branch admin can edit it's own branch

if ($branch_name === '' || $branch_address === '' || $branch_phone === '') {
    echo json_encode(["success" => false, "message" => "All required fields must be filled"]);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO branches 
        (branch_name, branch_address, branch_phone, status, interest_rate, cash_on_hand, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$branch_name, $branch_address, $branch_phone, $status, $interest_rate, $cash_on_hand]);

    echo json_encode(["success" => true, "message" => "Branch added successfully"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
