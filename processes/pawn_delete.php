<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";


if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user = $_SESSION['user'];
if (!in_array($user['role'], ['admin', 'super_admin'])) {
    echo json_encode(["status" => "error", "message" => "Permission denied"]);
    exit();
}

if (!isset($_POST['pawn_id']) || !is_numeric($_POST['pawn_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$pawn_id = (int) $_POST['pawn_id'];
$user_id = $user['id'];
try {
    $pdo->beginTransaction();

    // 1. Get pawn record
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? ");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found or already deleted.");
    }

    $amount = (float) $pawn['amount_pawned'];
    $branch_id = (int) $pawn['branch_id'];

    // update COH
    updateCOH($pdo, $branch_id, $amount, 'add');



    //  Log transaction in cash_ledger

    // update cash ledger (to log reversal)
    // Delete any previous ledger entry for this item
    $del = $pdo->prepare("DELETE FROM cash_ledger 
          WHERE ref_id = :pawn_id 
            AND branch_id = :branch_id");
    $del->execute([
        'pawn_id' => $pawn_id,
        'branch_id' => $branch_id
    ]);

    // ✅ Log the pawn deletion in cash ledger
    // if ($amount > 0) {
    //     $description = "Pawn Deleted (ID #$pawn_id)";
    //     $notes = "Pawn ID #$pawn_id deleted - amount refunded to cash on hand";

    //     insertCashLedger(
    //         $pdo,
    //         $branch_id,
    //         "delete",       // txn_type
    //         "in",           // direction
    //         $amount,
    //         "pawned_items", // ref_table
    //         $pawn_id,
    //         $description,
    //         $notes,
    //         $user_id
    //     );
    // }


    // --- Insert into audit logs ---
    $description = sprintf(
        "Deleted pawn ID: %d, Unit: %s",
        $pawn_id,
        $pawn['unit_description']
    );

    logAudit(
        $pdo,
        $user_id,
        $pawn['branch_id'],
        'Deleted Pawned Item',
        $description
    );



    // 4. Soft-delete pawn record
    $stmt = $pdo->prepare("UPDATE pawned_items SET is_deleted = 1 WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Pawn moved to trash.<br>Cash on Hand updated. +₱" . number_format($amount, 2)]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>