<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

$user = $_SESSION['user'];
$user_role = $user['role'];
$session_branch_id = $user['branch_id'];

// Get branch_id from AJAX request (for super_admin)
$selected_branch_id = $_GET['branch_id'] ?? null;

$branchCondition = [];
$params = [];

// Branch filtering
if ($user_role === 'super_admin') {
    if (!empty($selected_branch_id)) {
        $branchCondition[] = "cl.branch_id = ?";
        $params[] = $selected_branch_id;
    }
    // else leave empty to get all branches
} else {
    // normal admin: lock to session branch
    $branchCondition[] = "cl.branch_id = ?";
    $params[] = $session_branch_id;
}

// Date filter
if (!empty($_GET['fromDate']) && !empty($_GET['toDate'])) {
    $branchCondition[] = "DATE(cl.created_at) BETWEEN ? AND ?";
    $params[] = $_GET['fromDate'];
    $params[] = $_GET['toDate'];
}

$whereSQL = "";
if (!empty($branchCondition)) {
    $whereSQL = "WHERE " . implode(" AND ", $branchCondition);
}

$sql = "
    SELECT 
        cl.ledger_id,
        cl.created_at,
        b.branch_name,
        cl.txn_type,
        cl.direction,
        cl.amount,
        cl.ref_table,
        cl.ref_id,
        cl.description,
        u.username
    FROM cash_ledger cl
    JOIN branches b ON cl.branch_id = b.branch_id
    JOIN users u ON cl.user_id = u.user_id
    $whereSQL
    ORDER BY cl.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rows[] = [
        $row['created_at'],
        $row['branch_name'],
        ucfirst($row['txn_type']),
        ucfirst($row['direction']),
        "₱" . number_format($row['amount'], 2),
        $row['ref_table'] . " (#" . $row['ref_id'] . ")",
        $row['description'],
        $row['username']
    ];
}

echo json_encode(["data" => $rows]);
