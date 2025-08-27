<?php
require_once "../config/db.php";
require_once "../config/helpers.php";
session_start();


// --- Insert into audit_logs ---
$user_id = $_SESSION['user']['id'] ?? null;
$session_branch_id = $_SESSION['user']['branch_id'] ?? null;

if ($user_id) {
    $description = "Logged Out";
    logAudit($pdo, $user_id, $session_branch_id, 'Logout', $description);
}


session_unset();  // Remove all session variables
session_destroy(); // Destroy the session
header("Location: index");
exit();
