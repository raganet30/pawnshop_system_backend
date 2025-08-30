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
$confirmPassword = $_POST['confirm_password'] ?? '';
$branch_id = $_POST['branch_id'] ?? '';
$role = $_POST['role'] ?? '';
$status = $_POST['status'] ?? '';

if(!$full_name || !$username || !$password || !$confirmPassword || !$branch_id || !$role || !$status){
    echo json_encode(["success"=>false,"message"=>"All fields are required"]);
    exit();
}

// Password match
if($password !== $confirmPassword){
    echo json_encode(["success"=>false,"message"=>"Passwords do not match"]);
    exit();
}

// Strong password
if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)){
    echo json_encode([
        "success"=>false,
        "message"=>"Password must be at least 8 characters, include uppercase, lowercase, number, and special character."
    ]);
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

// Handle profile picture upload
$photo_path = null;
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = "../uploads/avatars/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array(strtolower($ext), $allowed)) {
        echo json_encode(["success"=>false,"message"=>"Invalid file type. Allowed: jpg, jpeg, png, gif, webp"]);
        exit();
    }

    $newName = uniqid("user_") . "." . $ext;
    $targetFile = $uploadDir . $newName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photo_path = "uploads/avatars/" . $newName; // store relative path in DB
    } else {
        echo json_encode(["success"=>false,"message"=>"Failed to upload profile picture"]);
        exit();
    }
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert
try {
    $stmt = $pdo->prepare("INSERT INTO users 
        (branch_id, username, password_hash, role, full_name, status, photo_path, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$branch_id, $username, $hash, $role, $full_name, $status, $photo_path]);
    
    echo json_encode(["success"=>true,"message"=>"User added successfully"]);
}catch(PDOException $e){
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
