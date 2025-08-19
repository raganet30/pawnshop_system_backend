<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";


if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Restrict to super_admin only
if ($_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

$pawn_id = $_POST['pawn_id'] ?? null;
$owner_name = $_POST['owner_name'] ?? '';
$contact_no = $_POST['contact_no'] ?? '';
$unit_description = $_POST['unit_description'] ?? '';
$category = $_POST['category'] ?? '';
$new_amount = floatval($_POST['amount_pawned'] ?? 0);
$notes = $_POST['notes'] ?? '';
$date_pawned = $_POST['date_pawned'] ?? '';
$branch_id = $_SESSION['user']['branch_id'] ?? 1; // Default to branch 1 if not set

// Validate required fields
if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Pawn ID is required"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch old pawn record
    $stmt = $pdo->prepare("SELECT amount_pawned, branch_id FROM pawned_items WHERE pawn_id = ? AND is_deleted = 0  ");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found.");
    }

    $old_amount = floatval($pawn['amount_pawned']);
    $branch_id = $pawn['branch_id'];

    // Compute difference
    $difference = $new_amount - $old_amount;

    // Update pawned_items
    $stmt = $pdo->prepare("UPDATE pawned_items 
        SET owner_name = ?, contact_no = ?, unit_description = ?, category = ?, 
            amount_pawned = ?, notes = ?, date_pawned = ? 
        WHERE pawn_id = ?");
    $stmt->execute([$owner_name, $contact_no, $unit_description, $category, $new_amount, $notes, $date_pawned, $pawn_id]);


    // Log action



    // Adjust COH only if difference != 0
    if ($difference != 0) {
        // Lock branch row and get current Cash On Hand
        $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
        $stmt->execute([$branch_id]);
        $branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$branchRow) {
            throw new Exception("Branch not found.");
        }

        $current_coh = floatval($branchRow['cash_on_hand']);
        $new_coh = $current_coh - $difference;

        // If resulting COH would be negative, abort
        if ($new_coh <= 0) {
            throw new Exception("Not enough Cash On Hand balance for this operation.");
        }

        // Apply COH update
        $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = ? WHERE branch_id = ?");
        $stmt->execute([$new_coh, $branch_id]);

        // Log to cash ledger
        $direction = $difference > 0 ? 'out' : 'in';
        $stmt = $pdo->prepare("INSERT INTO cash_ledger (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id) 
                               VALUES (?, 'pawn_edit', ?, ?, 'pawned_items', ?, ?, ?)");
        $stmt->execute([
            $branch_id,
            $direction,
            abs($difference),
            $pawn_id,
            "Edit pawn amount from {$old_amount} to {$new_amount}",
            $_SESSION['user']['user_id'] ?? null
        ]);
    }

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Pawn item updated successfully. Cash on Hand adjusted."]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
