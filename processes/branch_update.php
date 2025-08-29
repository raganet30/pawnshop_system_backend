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

$branch_id     = intval($_POST['branch_id'] ?? 0);
$branch_name   = trim($_POST['branch_name'] ?? '');
$branch_address = trim($_POST['branch_address'] ?? '');
$branch_phone  = trim($_POST['branch_phone'] ?? '');
$status        = trim($_POST['status'] ?? 'active');
$interest_rate = trim($_POST['interest_rate'] ?? 0);
// $cash_on_hand  = trim($_POST['cash_on_hand'] ?? 0);

if ($branch_id <= 0 || $branch_name === '' || $branch_address === '' || $branch_phone === '') {
    echo json_encode(["success" => false, "message" => "Invalid or missing data"]);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE branches SET 
        branch_name=?, branch_address=?, branch_phone=?, status=?, interest_rate=?
        WHERE branch_id=?");
    $stmt->execute([$branch_name, $branch_address, $branch_phone, $status, $interest_rate, $branch_id]);

    echo json_encode(["success" => true, "message" => "Branch updated successfully"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
