<?php
session_start();

// Allow only logged-in super_admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    http_response_code(403);
    exit("Unauthorized access.");
}

if (!isset($_GET['file'])) {
    http_response_code(400);
    exit("No file specified.");
}

// Sanitize filename (prevent directory traversal attacks)
$filename = basename($_GET['file']);

// Backups folder (root level)
$backupDir = realpath(__DIR__ . "/../backups");
$filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

// Validate file existence
if (!file_exists($filePath)) {
    http_response_code(404);
    exit("File not found.");
}

// Serve the file for download
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filePath));
header('Pragma: public');

readfile($filePath);
exit;
?>
