<?php
require_once "../config/db.php";
require_once "../config/app.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: ../public/index.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, full_name, username, password_hash, role, branch_id, status 
                               FROM users 
                               WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "No active user found with username: {$username}";
            header("Location: ../public/index.php");
            exit;
        }

        // Debug: Show fetched user data (excluding hash for security)
        // error_log("DEBUG: Found user - ID: {$user['user_id']}, Role: {$user['role']}, Branch: {$user['branch_id']}");

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => filter_var($user['user_id'], FILTER_SANITIZE_NUMBER_INT),
                'full_name' => htmlspecialchars($user['full_name'], ENT_QUOTES),
                'role' => htmlspecialchars($user['role'], ENT_QUOTES),
                'branch_id' => filter_var($user['branch_id'], FILTER_SANITIZE_NUMBER_INT)
            ];

            // Redirect based on role
            if ($user['role'] === 'super_admin') {
                header("Location: ../public/dashboard_super.php");
            } else {
                header("Location: ../public/dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password verification failed for username: {$username}";
            header("Location: ../public/index.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../public/index.php");
        exit;
    }
} else {
    header("Location: ../public/index.php");
    exit;
}
