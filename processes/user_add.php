<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(["success"=>false,"message"=>"Invalid request"]);
    exit();
}

$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$branch_id = $_POST['branch_id'] ?? '';
$role = $_POST['role'] ?? '';
$status = $_POST['status'] ?? '';

if(!$full_name || !$username || !$password || !$branch_id || !$role || !$status){
    echo json_encode(["success"=>false,"message"=>"All fields are required"]);
    exit();
}

// Check username uniqueness
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username=?");
$stmt->execute([$username]);
if($stmt->fetchColumn() > 0){
    echo json_encode(["success"=>false,"message"=>"Username already exists"]);
    exit();
}

// Restrict only 1 super-admin
if($role === 'super_admin'){
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='super_admin'");
    if($stmt->fetchColumn() > 0){
        echo json_encode(["success"=>false,"message"=>"A Super Admin account already exists"]);
        exit();
    }
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (branch_id, username, password_hash, role, full_name, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$branch_id, $username, $hash, $role, $full_name, $status]);
    echo json_encode(["success"=>true,"message"=>"User added successfully"]);
}catch(PDOException $e){
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
