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

$action = $_POST['action'] ?? '';
$ids = $_POST['ids'] ?? [];

if (empty($ids) || !is_array($ids)) {
    echo json_encode(["status" => "error", "message" => "Invalid pawn ID(s)"]);
    exit();
}

// Filter numeric IDs
$pawn_ids = array_filter($ids, 'is_numeric');
$user_id = $user['id'];


if (empty($pawn_ids)) {
    echo json_encode(["status" => "error", "message" => "No valid IDs provided"]);
    exit();
}

try {
    if ($action === "restore") {
        foreach ($pawn_ids as $pawn_id) {
            // Get pawn details first
            $stmt = $pdo->prepare("SELECT amount_pawned, branch_id FROM pawned_items WHERE pawn_id = ?");
            $stmt->execute([$pawn_id]);
            $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pawn) {
                $amount = (float) $pawn['amount_pawned'];
                $branch_id = (int) $pawn['branch_id'];

                // Restore pawn
                $pdo->prepare("UPDATE pawned_items SET is_deleted = 0 WHERE pawn_id = ?")
                    ->execute([$pawn_id]);

                // Add back amount to branch COH
                // $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?")
                //     ->execute([$amount, $branch_id]);

                updateCOH($pdo, $branch_id, $amount, 'subtract');



                // Log to cash ledger
                // $pdo->prepare("INSERT INTO cash_ledger 
                //     (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id, created_at)
                //     VALUES (?, 'restore', 'in', ?, 'pawned_items', ?, 'Pawn restored from trash', ?, NOW())")
                //     ->execute([$branch_id, $amount, $pawn_id, $user_id]);

                $description = "Restore Pawn (ID #$pawn_id)";
                $notes = "Deleted Pawn ID(s) #$pawn_id restored.";

                insertCashLedger(
                    $pdo,
                    $branch_id,
                    "restore",     // txn_type
                    "in",        // direction
                    $amount,
                    "claims",    // ref_table
                    $pawn_id,
                    $description,
                    $notes,
                    $user_id
                );


            }
        }
        echo json_encode(["status" => "success", "message" => "Selected pawn(s) & COH updated."]);
    } elseif ($action === "delete") {
        // Permanently delete records
        $in = str_repeat('?,', count($pawn_ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM pawned_items WHERE pawn_id IN ($in)");
        $stmt->execute($pawn_ids);

        echo json_encode(["status" => "success", "message" => "Selected pawn(s) permanently deleted."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
