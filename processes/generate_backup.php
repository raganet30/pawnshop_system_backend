<?php
session_start();

// Allow only super_admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Load DB config
require_once "../config/db.php";

// Set root-level backups folder
$backupDir = realpath(__DIR__ . "/../backups");

// If folder doesn’t exist, create it
if ($backupDir === false) {
    $backupDir = __DIR__ . "/../backups";
    mkdir($backupDir, 0777, true);
}

$backupFile = $backupDir . "/pawnshop_db_backup_" . date("Y-m-d_H-i-s") . ".sql";

try {
    // Open file
    $handle = fopen($backupFile, "w");

    // Get all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        // Write CREATE TABLE
        $res = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, "\n\n" . $row["Create Table"] . ";\n\n");

        // Write INSERTS
        $res = $pdo->query("SELECT * FROM `$table`");
        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            $values = array_map([$pdo, 'quote'], array_values($r));
            $values = implode(",", $values);
            fwrite($handle, "INSERT INTO `$table` VALUES ($values);\n");
        }
    }

    fclose($handle);

    // Optional: keep only last 10 backups
    $files = glob($backupDir . "/pawnshop_db_backup_*.sql");
    if (count($files) > 20) {
    usort($files, function($a, $b) {
        return filemtime($a) - filemtime($b); // oldest first
    });
    while (count($files) > 20) {
        $oldest = array_shift($files);
        unlink($oldest);
    }
}

    echo json_encode(["success" => true, "file" => basename($backupFile)]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
