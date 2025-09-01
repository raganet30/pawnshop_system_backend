<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php"; // adjust path if needed

function checkSessionTimeout($pdo)
{
    // Load timeout setting from DB (in minutes)
    $stmt = $pdo->query("SELECT session_timeout FROM settings WHERE id=1 LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert to seconds
    $timeout = isset($settings['session_timeout']) && is_numeric($settings['session_timeout'])
        ? intval($settings['session_timeout']) * 60
        : 900; // default 15 mins

    // Check if user has last activity recorded
    if (isset($_SESSION['last_activity'])) {
        if ((time() - $_SESSION['last_activity']) > $timeout) {
            session_unset();
            session_destroy();
            session_start(); // restart session to set expired message
            $_SESSION['expired'] = "Your session has expired due to inactivity. Please log in again.";
            header("Location: index");
            exit();
        }
    }

    // Always update activity timestamp
    $_SESSION['last_activity'] = time();
}
