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

    // 1. Get claim record
    $stmt = $pdo->prepare("SELECT * FROM claims WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$claim) {
        throw new Exception("Claim record not found.");
    }

    $branch_id = $claim['branch_id'];
    $total_paid = $claim['total_paid'];
    $claim_id = $claim['claim_id'];
    $principal_amount = $claim['principal_amount'];


    // 2. Deduct from cash_on_hand
    // $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?")
    //     ->execute([$total_paid, $branch_id]);

    updateCOH($pdo, $branch_id, $total_paid, 'subtract');




    // 3. Revert pawn status
    $pdo->prepare("UPDATE pawned_items 
                      SET status = 'pawned'
                    WHERE pawn_id = ?")
        ->execute([$pawn_id]);

    // 4. Remove claim record
    $pdo->prepare("DELETE FROM claims WHERE pawn_id = ?")->execute([$pawn_id]);

    // 5. update cash ledger (to log reversal)
    // Delete any previous ledger entry for this claim (optional safety)
    $del = $pdo->prepare("DELETE FROM cash_ledger 
          WHERE ref_id = :pawn_id 
            AND branch_id = :branch_id");
    $del->execute([
        'pawn_id' => $pawn_id,
        'branch_id' => $branch_id
    ]);

    // Amount to revert
    $amount = $total_paid;

    // Direction
    $direction = "out"; // money going out due to claim reversal

    // Description & Notes
    $description = "Revert Claim (ID #$pawn_id)";
    $notes = "Reverted claimed pawned item. Total amount ₱" . number_format($total_paid, 2);

    // Insert into cash ledger
    insertCashLedger(
        $pdo,
        $branch_id,
        "claim",       // txn_type
        $direction,    // in/out
        $principal_amount,
        "claims",      // ref_table
        $pawn_id,
        $description,
        $notes,
        $user['id']
    );



    $user_id = $_SESSION['user']['id'] ?? null;
    // 6. Insert audit log
    $description = sprintf(
        "Reverted a claimed iteom to pawn item.(PawnID: %s, Amount: ₱%s)",
        $pawn_id,
        number_format($principal_amount, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Revert Claimed Item', $description);




    // 7. delete from tubo payments
    $pdo->prepare("DELETE FROM tubo_payments WHERE pawn_id = ?")->execute([$pawn_id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Claimed item reverted to pawned items.<br>Cash on Hand adjusted -₱" . number_format($principal_amount, 2)]);

    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
