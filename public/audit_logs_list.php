<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}



// branch id is set in the session for branch-specific views
// $branch_id = $_SESSION['user']['branch_id'] ?? 1; // Default to branch 1 if not set


// Fetch pawned items (only status = 'claimed')
$stmt = $pdo->query("
    SELECT audit_logs.*, branches.branch_name AS branch_name
    FROM audit_logs
    LEFT JOIN branches ON audit_logs.branch_id = branches.branch_id
    ORDER BY audit_logs.created_at DESC
");


$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


    $rows[] = [
        $row['created_at'],
        $row['action_type'],
        $row['description'],
        $row['branch_name']
    ];
}

echo json_encode(["data" => $rows]);
