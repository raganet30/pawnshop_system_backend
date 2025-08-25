<?php
/**
 * Database configuration for Localhost & InfinityFree
 * Auto-detects based on domain/host
 */

$serverHost = $_SERVER['HTTP_HOST'] ?? 'cli'; // fallback for CLI (artisan, cron, etc.)

if ($serverHost === 'localhost' || $serverHost === '127.0.0.1' || $serverHost === 'cli') {
    // 🖥 Localhost settings
    $host = "127.0.0.1";
    $dbname = "pawnshop_db";
    $user = "root";   // change if your local MySQL has a password
    $pass = "";
} else {
    // 🌐 InfinityFree settings
    $host = "sql213.infinityfree.com";     // replace XXX with your InfinityFree SQL host
    $dbname = "if0_39781345_pawnshop_db";   // replace with your InfinityFree DB name
    $user = "if0_39781345";        // replace with your InfinityFree username
    $pass = "qPA1maABR1z";        // replace with your InfinityFree password
}

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Display a user-friendly message and stop execution
    echo "<h2 style='color:red;'>Database Connection Failed</h2>";
    echo "<p>Please check your database settings in <code>config/db_config.php</code></p>";
    echo "<p><strong>Error Details:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
