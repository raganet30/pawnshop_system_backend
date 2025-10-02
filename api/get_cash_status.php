<?php
session_start();
require_once "../config/db.php";

$branchId = $_SESSION['user']['branch_id'] ?? null;

if (!$branchId) {
    echo json_encode(['success' => false, 'message' => 'No branch ID found']);
    exit;
}

try {
    // Fetch branch cash
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ?");
    $stmt->execute([$branchId]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch threshold from settings
    $stmt2 = $pdo->query("SELECT cash_threshold FROM settings LIMIT 1");
    $settings = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($branch && $settings) {
        echo json_encode([
            'success' => true,
            'cash_on_hand' => (float)$branch['cash_on_hand'],
            'cash_threshold' => (float)$settings['cash_threshold']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
