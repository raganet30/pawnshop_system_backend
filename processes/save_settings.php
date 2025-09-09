<?php
session_start();
require_once "../config/db.php";

//  Require login
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

//  Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Sanitize inputs
$cash_threshold   = isset($_POST['cash_threshold']) ? floatval($_POST['cash_threshold']) : 0;
$pawn_maturity_reminder_days    = isset($_POST['pawn_maturity_reminder_days']) ? intval($_POST['pawn_maturity_reminder_days']) : 3;
$export_format    = isset($_POST['export_format']) ? trim($_POST['export_format']) : 'excel';
$report_info      = isset($_POST['report_info']) ? trim($_POST['report_info']) : '';
$backup_frequency = isset($_POST['backup_frequency']) ? trim($_POST['backup_frequency']) : 'manual';

//  Validate minimal rules
$valid_formats = ['pdf', 'excel', 'csv'];
$valid_backups = ['manual', 'daily', 'weekly', 'monthly'];

if (!in_array($export_format, $valid_formats)) {
    echo json_encode(["success" => false, "message" => "Invalid export format"]);
    exit;
}

if (!in_array($backup_frequency, $valid_backups)) {
    echo json_encode(["success" => false, "message" => "Invalid backup frequency"]);
    exit;
}

//  Always update row id=1 (singleton settings)
try {
    $session_timeout = isset($_POST['session_timeout']) ? intval($_POST['session_timeout']) : 15;

    $stmt = $pdo->prepare("
    UPDATE settings 
    SET cash_threshold=?, pawn_maturity_reminder_days=?, export_format=?, report_info=?, backup_frequency=?, session_timeout=?, updated_at=NOW() 
    WHERE id=1
");
$ok = $stmt->execute([
    $cash_threshold,
    $pawn_maturity_reminder_days,
    $export_format,
    $report_info,
    $backup_frequency,
    $session_timeout
]);

    if ($ok) {
        echo json_encode(["success" => true, "message" => "Settings updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update settings"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
