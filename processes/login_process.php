<?php
require_once "../config/db.php";
require_once "../config/app.php";
require_once "../config/helpers.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: ../public/index");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT u.user_id, u.full_name, u.username, u.password_hash, u.role, 
                                        u.branch_id, b.branch_name,b.branch_address, u.status
                                    FROM users u
                                    LEFT JOIN branches b ON u.branch_id = b.branch_id
                                    WHERE u.username = ? AND u.status = 'active'
                                    LIMIT 1
                                    ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "No active user found with username: {$username}";
            header("Location: ../public/index");
            exit;
        }

        // Debug: Show fetched user data (excluding hash for security)
        // error_log("DEBUG: Found user - ID: {$user['user_id']}, Role: {$user['role']}, Branch: {$user['branch_id']}");

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => filter_var($user['user_id'], FILTER_SANITIZE_NUMBER_INT),
                'full_name' => htmlspecialchars($user['full_name'], ENT_QUOTES),
                'role' => htmlspecialchars($user['role'], ENT_QUOTES),
                'branch_id' => filter_var($user['branch_id'], FILTER_SANITIZE_NUMBER_INT),
                'branch_name' => htmlspecialchars($user['branch_name'] ?? '', ENT_QUOTES),
                'branch_address' => htmlspecialchars($user['branch_address'] ?? '', ENT_QUOTES)
            ];


            // Initialize last activity timestamp for session timeout
            $_SESSION['last_activity'] = time();


            // Update last_login to current datetime
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);


            // --- Insert into audit_logs ---
            $user_id = $_SESSION['user']['id'] ?? null;
            $session_branch_id = $_SESSION['user']['branch_id'] ?? null;

            if ($user_id) {
                $description = "Logged In";
                logAudit($pdo, $user_id, $session_branch_id, 'Login', $description);
            }


            // Redirect based on role
            if ($user['role'] === 'super_admin') {
                header("Location: ../public/dashboard_super");
            } else {
                header("Location: ../public/dashboard");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password verification failed for username: {$username}";
            header("Location: ../public/index");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../public/index");
        exit;
    }
} else {
    header("Location: ../public/index");
    exit;
}
