<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";


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

    


     // Insert into cash ledger
    insertCashLedger(
        $pdo,
        $branch_id,
        "coh_adjustment",
        $direction,
        $amount,
        "coh_adjustment",
        "",
        "COH Adjustment",
        "COH Adjustment",
        $user_id
    );



    // Get the new ledger_id
    $ledgerId = $pdo->lastInsertId();

    // Update self-reference
    $update = $pdo->prepare("UPDATE cash_ledger SET ref_id = ? WHERE ledger_id = ?");
    $update->execute([$ledgerId, $ledgerId]);





// insert to audit logs
    $description = "Cash on Hand {$action}: ₱" . number_format($amount, 2) . " (New COH: ₱" . number_format($newCOH, 2) . ")";
    logAudit(
        $pdo,
        $user_id,
        $branch_id,
        'Cash On Hand Adjustment',
        $description
    );




    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'new_coh' => number_format($newCOH, 2)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
