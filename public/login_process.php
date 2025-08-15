<?php
require_once "../config/db.php";
require_once "../config/app.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: login.php");
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
            header("Location: login.php");
            exit;
        }

        // Debug: Show fetched user data (excluding hash for security)
        error_log("DEBUG: Found user - ID: {$user['user_id']}, Role: {$user['role']}, Branch: {$user['branch_id']}");

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id'        => $user['user_id'],
                'name'      => $user['full_name'],
                'role'      => $user['role'],
                'branch_id' => $user['branch_id']
            ];

            // Redirect based on role
            if ($user['role'] === 'cashier') {
                header("Location: pawns.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password verification failed for username: {$username}";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
