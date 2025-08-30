<?php
session_start();
require_once "../config/db.php"; // DB connection
require_once "../config/helpers.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $branch_id = $_SESSION['user']['branch_id'];

    $user_id = $_SESSION['user']['id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch user
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($old_password, $user['password_hash'])) {
        echo json_encode(["status" => "error", "message" => "Old password is incorrect."]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "New passwords do not match."]);
        exit;
    }

    if (
        strlen($new_password) < 8 ||
        !preg_match("/[A-Z]/", $new_password) ||
        !preg_match("/[a-z]/", $new_password) ||
        !preg_match("/[0-9]/", $new_password) ||
        !preg_match("/[^A-Za-z0-9]/", $new_password)
    ) {
        echo json_encode(["status" => "error", "message" => "New password must be at least 8 characters and include uppercase, lowercase, number, and special character."]);
        exit;
    }

    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->execute([$new_hash, $user_id]);


    
    $description = "Changed New Password";
    logAudit(
        $pdo,
        $user_id,
        $branch_id,
        'Change Password',
        $description
    );



    echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
    exit;
}
