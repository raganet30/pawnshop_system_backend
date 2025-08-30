<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

// If request is for distinct action types
if (isset($_GET['action_types'])) {
    $stmt = $pdo->query("SELECT DISTINCT action_type FROM audit_logs ORDER BY action_type");
    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(["actionTypes" => $types]);
    exit();
}

// --- Normal logs fetch ---
$branchId = $_GET['branch_id'] ?? null;
$fromDate = $_GET['fromDate'] ?? null;
$toDate = $_GET['toDate'] ?? null;
$actionType = $_GET['action_type'] ?? null;

$query = "
    SELECT 
    audit_logs.*, 
    branches.branch_name, 
    users.full_name
FROM audit_logs
LEFT JOIN branches ON audit_logs.branch_id = branches.branch_id
LEFT JOIN users ON audit_logs.user_id = users.user_id
WHERE 1=1

";

$params = [];

if (!empty($branchId)) {
    $query .= " AND audit_logs.branch_id = ? ";
    $params[] = $branchId;
}

if (!empty($fromDate)) {
    $query .= " AND DATE(audit_logs.created_at) >= ? ";
    $params[] = $fromDate;
}

if (!empty($toDate)) {
    $query .= " AND DATE(audit_logs.created_at) <= ? ";
    $params[] = $toDate;
}

if (!empty($actionType)) {
    $query .= " AND audit_logs.action_type = ? ";
    $params[] = $actionType;
}

$query .= " ORDER BY audit_logs.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = [
        $row['created_at'],
        $row['full_name'],
        $row['action_type'],
        $row['description'],
        $row['branch_name']
    ];
}

echo json_encode(["data" => $rows]);
