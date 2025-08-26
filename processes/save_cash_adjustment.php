<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user']['id'];
$branch_id = $_SESSION['user']['branch_id'];

$amount = floatval($_POST['amount'] ?? 0);
$action = $_POST['action'] ?? '';
$notes = trim($_POST['notes'] ?? '');

if ($amount <= 0 && $action !== 'set') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Get current COH
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ?");
    $stmt->execute([$branch_id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        throw new Exception("Branch not found");
    }

    $currentCOH = (float) $branch['cash_on_hand'];
    $newCOH = $currentCOH;
    $direction = null;

    // Adjust based on action
    if ($action === 'add') {
        $newCOH += $amount;
        $direction = 'in';
    } elseif ($action === 'subtract') {
        $newCOH -= $amount;
        $direction = 'out';
    } elseif ($action === 'set') {
        $newCOH = $amount;
        $direction = ($amount >= $currentCOH) ? 'in' : 'out';
    } else {
        throw new Exception("Invalid action");
    }

    // Update branches table
    $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = ? WHERE branch_id = ?");
    $stmt->execute([$newCOH, $branch_id]);

    // Insert into cash_ledger
    // Insert COH adjustment into ledger
    $stmt = $pdo->prepare("
    INSERT INTO cash_ledger 
    (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, created_at, user_id)
    VALUES (?, 'adjustment', ?, ?, 'coh_adjustment', 0, ?, NOW(), ?)
");
    $stmt->execute([$branch_id, $direction, $amount, $notes, $user_id]);

    // Get the new ledger_id
    $ledgerId = $pdo->lastInsertId();

    // Update self-reference
    $update = $pdo->prepare("UPDATE cash_ledger SET ref_id = ? WHERE ledger_id = ?");
    $update->execute([$ledgerId, $ledgerId]);




    // Insert into audit_logs
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action_type, description, branch_id, created_at)
        VALUES (?, 'cash on hand_adjustment', ?, ?, NOW())
    ");
    $desc = "Cash on Hand {$action}: ₱" . number_format($amount, 2) . " (New COH: ₱" . number_format($newCOH, 2) . ")";
    $stmt->execute([$user_id, $desc, $branch_id]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'new_coh' => number_format($newCOH, 2)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
