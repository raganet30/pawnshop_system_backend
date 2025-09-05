<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "project";

// 🔹 Backup folder in project root
$backupDir = dirname(__DIR__) . "/backup/";
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$filename = $dbname . "_" . date("Ymd_His") . ".sql";
$filepath = $backupDir . $filename;

// 🔹 Try to detect mysqldump path
$mysqldump = "mysqldump"; // default (works if mysqldump is in PATH)

// Windows (XAMPP)
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $xamppDump = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
    if (file_exists($xamppDump)) {
        $mysqldump = $xamppDump;
    }
}

// Linux (common paths)
else {
    $linuxPaths = ["/usr/bin/mysqldump", "/usr/local/bin/mysqldump", "/bin/mysqldump"];
    foreach ($linuxPaths as $path) {
        if (file_exists($path)) {
            $mysqldump = $path;
            break;
        }
    }
}

// 🔹 Build command
$command = "\"$mysqldump\" -h $host -u $user " . ($pass ? "-p$pass " : "") . "$dbname > \"$filepath\"";

// 🔹 Run command
exec($command, $output, $result);

if ($result === 0) {
    echo json_encode([
        "success" => true,
        "filename" => $filename,
        "path" => "backup/" . $filename
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Backup failed. Command tried: $command"
    ]);
}
