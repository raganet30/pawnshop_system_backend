<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Restrict to admin/super_admin only
if ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

$pawn_id = $_POST['pawn_id'] ?? null;
$unit_description = $_POST['unit_description'] ?? '';
$category = $_POST['category'] ?? '';
$new_amount = floatval($_POST['amount_pawned'] ?? 0);
$notes = $_POST['notes'] ?? '';
$date_pawned = $_POST['date_pawned'] ?? '';

if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Pawn ID is required"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch old pawn record
    $stmt = $pdo->prepare("SELECT amount_pawned, branch_id FROM pawned_items WHERE pawn_id = ? AND is_deleted = 0");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found.");
    }

    $old_amount = floatval($pawn['amount_pawned']);
    $branch_id = $pawn['branch_id'];

    // Compute difference
    $difference = $new_amount - $old_amount;

    // ✅ Update only pawn details (NOT customer info)
    $stmt = $pdo->prepare("UPDATE pawned_items 
        SET unit_description = ?, category = ?, amount_pawned = ?, notes = ?, date_pawned = ?
        WHERE pawn_id = ?");
    $stmt->execute([$unit_description, $category, $new_amount, $notes, $date_pawned, $pawn_id]);

    // ✅ Adjust COH only if amount changed
    if ($difference != 0) {
        // Lock branch row and get current COH
        $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
        $stmt->execute([$branch_id]);
        $branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$branchRow) {
            throw new Exception("Branch not found.");
        }

        $current_coh = floatval($branchRow['cash_on_hand']);
        $new_coh = $current_coh - $difference;
        $user_id = $_SESSION['user']['id'] ?? null;
        // Prevent negative balance
        if ($new_coh < 0) {
            throw new Exception("Not enough Cash On Hand balance for this operation.");
        }

        // Apply COH update
        $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = ? WHERE branch_id = ?");
        $stmt->execute([$new_coh, $branch_id]);




        //   update cash ledger
        // 1. Remove old ledger entry for this pawn
        $del = $pdo->prepare("DELETE FROM cash_ledger 
                      WHERE ref_table = 'pawned_items' 
                        AND ref_id = :pawn_id 
                        AND branch_id = :branch_id");
        $del->execute([
            'pawn_id' => $pawn_id,
            'branch_id' => $branch_id
        ]);

        // 2. Determine direction based on change
        $direction = ($new_amount >= $old_amount) ? "out" : "in";

        // 3. Amount is only the difference
        $amount = abs($new_amount - $old_amount);

        // 4. Description & Notes
        $description = "Pawn Edit (ID #$pawn_id)";
        $notes = "Pawn amount adjusted from ₱"
            . number_format($old_amount, 2)
            . " to ₱" . number_format($new_amount, 2);

        // 5. Insert into ledger
        insertCashLedger(
            $pdo,
            $branch_id,
            "pawn_edit",     // txn_type
            $direction,      // in/out
            $amount,         // only the difference
            "pawned_items",  // ref_table
            $pawn_id,
            $description,
            $notes,
            $user_id
        );




        // Assume $old_amount and $new_amount are already defined
        // 1. Compute difference
        $difference = $new_amount - $old_amount;

        // 2. Direction text for SweetAlert
        if ($difference > 0) {
            $adjustment_text = "Cash on Hand adjusted -₱" . number_format($difference, 2);
        } elseif ($difference < 0) {
            $adjustment_text = "Cash on Hand adjusted +₱" . number_format(abs($difference), 2);
        } else {
            $adjustment_text = "No Cash on Hand adjustment.";
        }

        // 3. Build JSON response
        echo json_encode([
            "status" => "success",
            "message" => "Pawn item updated successfully.<br>"
                . $adjustment_text
        ]);

    } else {
       
        // insert into audit_logs
        $user_id = $_SESSION['user']['id'] ?? null;
        $description = "Edit pawn ID: $pawn_id details";
        logAudit($pdo, $user_id, $branch_id, 'Edit Pawn Item', $description);

        echo json_encode(["status" => "success", "message" => "Pawn item updated successfully."]);
    }

    $pdo->commit();



} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
