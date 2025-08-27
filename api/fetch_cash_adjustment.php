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
    SELECT ledger_id, amount, direction, notes, created_at
    FROM cash_ledger
    WHERE branch_id = ? AND txn_type = 'coh_adjustment'
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->execute([$user['branch_id']]);
$adjustments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($adjustments);
