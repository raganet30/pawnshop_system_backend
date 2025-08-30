<?php
session_start();
require_once "../config/db.php"; 
require_once "../config/helpers.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id   = $_SESSION['user']['id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $branch_id = $_SESSION['user']['branch_id'];

    // Fetch current user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit;
    }

    // Default: keep old photo
    $photo_path = $user['photo_path'];

    // Handle new photo upload
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        // Validate extension
        if (!in_array($ext, $allowed)) {
            echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, PNG, GIF, WEBP allowed."]);
            exit;
        }

        // Validate mime
        $mime = mime_content_type($_FILES['photo']['tmp_name']);
        if (strpos($mime, 'image/') !== 0) {
            echo json_encode(["status" => "error", "message" => "Invalid file. Not an image."]);
            exit;
        }

        $target_dir = __DIR__ . "/../uploads/avatars/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = "user_" . $user_id . "_" . time() . "." . $ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $new_path = "uploads/avatars/" . $file_name;

            // Optional: delete old photo if it's not the default avatar
            if (!empty($user['photo_path']) && file_exists(__DIR__ . "/../" . $user['photo_path']) && strpos($user['photo_path'], "avatar.png") === false) {
                unlink(__DIR__ . "/../" . $user['photo_path']);
            }

            $photo_path = $new_path;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload photo."]);
            exit;
        }
    }

    // Update DB
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, username = ?, photo_path = ? 
        WHERE user_id = ?
    ");
    $stmt->execute([$full_name, $username, $photo_path, $user_id]);

    // Update session
    $_SESSION['user']['full_name']  = $full_name;
    $_SESSION['user']['username']   = $username;
    $_SESSION['user']['photo_path'] = $photo_path;

    // Audit log
    $description = "Updated Profile Details";
    logAudit($pdo, $user_id, $branch_id, 'Update Profile', $description);

    echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
    exit;
}
