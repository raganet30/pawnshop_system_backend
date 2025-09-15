<?php
// auto_backup.php
session_start();

// Load DB config
require_once "../config/db.php";

// Set root-level backups folder
$backupDir = realpath(__DIR__ . "/../backups");
if ($backupDir === false) {
    $backupDir = __DIR__ . "/../backups";
    mkdir($backupDir, 0777, true);
}

$backupFile = $backupDir . "/pawnshop_db_backup_" . date("Y-m-d_H-i-s") . ".sql";

try {
    $handle = fopen($backupFile, "w");

    // Get all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        $res = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, "\n\n" . $row["Create Table"] . ";\n\n");

        $res = $pdo->query("SELECT * FROM `$table`");
        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            $values = array_map([$pdo, 'quote'], array_values($r));
            fwrite($handle, "INSERT INTO `$table` VALUES (" . implode(",", $values) . ");\n");
        }
    }

    fclose($handle);

    // Keep only last 20 backups
    $files = glob($backupDir . "/pawnshop_db_backup_*.sql");
    if (count($files) > 20) {
        usort($files, fn($a, $b) => filemtime($a) - filemtime($b));
        while (count($files) > 20) {
            unlink(array_shift($files));
        }
    }

    echo "Backup created successfully: " . basename($backupFile);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
