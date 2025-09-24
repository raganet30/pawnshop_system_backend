<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

$user = $_SESSION['user'];

// Only adjustments are needed from cash_ledger
$stmt = $pdo->prepare("
    SELECT 
    cl.ledger_id,
    cl.amount,
    cl.direction,
    cl.description,
    cl.notes,
    cl.created_at,
    u.user_id,
    u.full_name
FROM cash_ledger cl
JOIN users u ON cl.user_id = u.user_id
WHERE cl.branch_id = ? 
  AND cl.txn_type = 'coh_adjustment'
ORDER BY cl.created_at DESC
LIMIT 20;

");
$stmt->execute([$user['branch_id']]);
$adjustments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($adjustments);
