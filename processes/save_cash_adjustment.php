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

try {
    $pdo->beginTransaction();

    // Get current COH
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
    $stmt->execute([$branch_id]);
    $currentCOH = (float) $stmt->fetchColumn();

    if ($currentCOH === false) {
        throw new Exception("Branch not found");
    }

    $delta = 0;
    $direction = null;

    switch ($action) {
        case 'add':
            if ($amount <= 0) throw new Exception("Invalid amount");
            $delta = $amount;
            $direction = 'in';
            break;

        case 'deduct':
            if ($amount <= 0) throw new Exception("Invalid amount");
            if ($currentCOH < $amount) throw new Exception("Insufficient cash on hand.");
            $delta = $amount;
            $direction = 'out';
            break;

        case 'set':
            $delta = abs($amount - $currentCOH);
            if ($delta == 0) {
                throw new Exception("COH is already the specified amount.");
            }
            $direction = ($amount > $currentCOH) ? 'in' : 'out';
            break;

        default:
            throw new Exception("Invalid action");
    }

    // Calculate new COH
    $newCOH = ($direction === 'in') ? $currentCOH + $delta : $currentCOH - $delta;

    // Update branches table
    $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = ? WHERE branch_id = ?");
    $stmt->execute([$newCOH, $branch_id]);

    // Insert into cash ledger
    // Prepare detailed notes for ledger
switch ($action) {
    case 'add':
        $ledgerNotes = sprintf(
            "Added ₱%s (Old COH: ₱%s → New COH: ₱%s)",
            number_format($delta, 2),
            number_format($currentCOH, 2),
            number_format($newCOH, 2)
        );
        break;

    case 'deduct':
        $ledgerNotes = sprintf(
            "Deducted ₱%s (Old COH: ₱%s → New COH: ₱%s)",
            number_format($delta, 2),
            number_format($currentCOH, 2),
            number_format($newCOH, 2)
        );  
        break;

    case 'set':
        $ledgerNotes = sprintf(
            "Set COH ₱%s (from ₱%s)",
            number_format($amount, 2),
            number_format($currentCOH, 2)
        );
        break;
}

// Insert into cash ledger with detailed notes
insertCashLedger(
    $pdo,
    $branch_id,
    "coh_adjustment",
    $direction,
    $delta,
    "branches",
    $branch_id,
    $ledgerNotes,
    $notes,
    $user_id
);


    // Log Audit
    $description = sprintf(
        "COH %s: %s₱%s (Old COH: ₱%s, New COH: ₱%s)",
        ucfirst($action),
        ($direction === 'out' ? '-' : '+'),
        number_format($delta, 2),
        number_format($currentCOH, 2),
        number_format($newCOH, 2)
    );
    logAudit($pdo, $user_id, $branch_id, "Cash On Hand Adjustment", $description);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'new_coh' => number_format($newCOH, 2)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
