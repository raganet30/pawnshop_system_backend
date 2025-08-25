<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

// --- Auth check ---
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user = $_SESSION['user'];
if (!in_array($user['role'], ['admin'])) {
    echo json_encode(["status" => "error", "message" => "Permission denied"]);
    exit();
}

$pawn_id = $_POST['pawn_id'] ?? null;
if (!$pawn_id || !is_numeric($pawn_id)) {
    echo json_encode(["status" => "error", "message" => "Invalid Pawn ID"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Get forfeiture record with pawned item details
    $stmt = $pdo->prepare("
        SELECT f.*, p.amount_pawned, p.unit_description
        FROM forfeitures f
        JOIN pawned_items p ON f.pawn_id = p.pawn_id
        WHERE f.pawn_id = ?
    ");
    $stmt->execute([$pawn_id]);
    $forfeit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$forfeit) {
        throw new Exception("Forfeited item not found.");
    }

    $branch_id = $forfeit['branch_id'];
    $amount = $forfeit['amount_pawned'];

    // 2. Deduct from cash_on_hand
    // $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?")
    //     ->execute([$amount, $branch_id]);
     updateCOH($pdo, $branch_id, $amount, 'subtract');



    // 3. Revert pawn status
    $pdo->prepare("UPDATE pawned_items SET status = 'pawned' WHERE pawn_id = ?")
        ->execute([$pawn_id]);

    // 4. Remove forfeiture record
    $pdo->prepare("DELETE FROM forfeitures WHERE pawn_id = ?")
        ->execute([$pawn_id]);

    // 5. Update cash ledger (log reversal)
    $del = $pdo->prepare("DELETE FROM cash_ledger 
        WHERE ref_table = 'forfeitures' 
          AND ref_id = :pawn_id 
          AND branch_id = :branch_id");
    $del->execute([
        'pawn_id' => $pawn_id,
        'branch_id' => $branch_id
    ]);

    $direction = "out"; // money leaving the branch
    $description = "Revert Forfeit (Pawn ID #$pawn_id)";
    $notes = "Reverted forfeited pawned item. COH deducted ₱" . number_format($amount, 2);

    insertCashLedger(
        $pdo,
        $branch_id,
        "forfeit",
        $direction,
        $amount,
        "forfeitures",
        $pawn_id,
        $description,
        $notes,
        $user['id']
    );

    // 6. Insert audit log
    $audit_desc = sprintf(
        "Reverted a forfeited item to pawned status. (PawnID: %s, Amount: ₱%s, Item: %s)",
        $pawn_id,
        number_format($amount, 2),
        $forfeit['unit_description']
    );
    logAudit($pdo, $user['id'], $branch_id, 'Revert Forfeited Item', $audit_desc);

    // 7. Delete from tubo payments if exists
    $pdo->prepare("DELETE FROM tubo_payments WHERE pawn_id = ?")->execute([$pawn_id]);

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Forfeited item reverted to pawned items.<br>COH deducted -₱" . number_format($amount, 2)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
