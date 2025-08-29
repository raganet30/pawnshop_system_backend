<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php"; // adjust path if needed

function checkSessionTimeout($pdo)
{
    // Load timeout setting from DB
    $stmt = $pdo->query("SELECT session_timeout FROM settings WHERE id=1 LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    $timeout = isset($settings['session_timeout']) ? intval($settings['session_timeout']) : 15; // default 15 mins

    if (isset($_SESSION['last_activity'])) {
        $timeout = 1800; // 30 minutes

        if ((time() - $_SESSION['last_activity']) > $timeout) {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['expired'] = "Your session has expired due to inactivity.<br>Please log in again.";
            header("Location: index");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();

}
