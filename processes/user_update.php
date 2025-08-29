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

$user_id = $_POST['user_id'] ?? '';
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$branch_id = $_POST['branch_id'] ?? '';
$role = $_POST['role'] ?? '';
$status = $_POST['status'] ?? '';

if(!$user_id || !$full_name || !$username || !$branch_id || !$role || !$status){
    echo json_encode(["success"=>false,"message"=>"All fields except password are required"]);
    exit();
}

// Fetch target user
$stmt = $pdo->prepare("SELECT role FROM users WHERE user_id=?");
$stmt->execute([$user_id]);
$targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$targetUser){
    echo json_encode(["success"=>false,"message"=>"User not found"]);
    exit();
}

// Prevent editing existing super-admin
if($targetUser['role'] === 'super_admin'){
    echo json_encode(["success"=>false,"message"=>"Super Admin account cannot be edited"]);
    exit();
}

// Prevent creating another super-admin
if($role === 'super_admin'){
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='super_admin'");
    if($stmt->fetchColumn() > 0){
        echo json_encode(["success"=>false,"message"=>"A Super Admin account already exists"]);
        exit();
    }
}

// Check username uniqueness for other users
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username=? AND user_id<>?");
$stmt->execute([$username,$user_id]);
if($stmt->fetchColumn() > 0){
    echo json_encode(["success"=>false,"message"=>"Username already exists"]);
    exit();
}

// Update
try {
    if($password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, password_hash=?, branch_id=?, role=?, status=? WHERE user_id=?");
        $stmt->execute([$full_name, $username, $hash, $branch_id, $role, $status, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, branch_id=?, role=?, status=? WHERE user_id=?");
        $stmt->execute([$full_name, $username, $branch_id, $role, $status, $user_id]);
    }
    echo json_encode(["success"=>true,"message"=>"User updated successfully"]);
}catch(PDOException $e){
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
