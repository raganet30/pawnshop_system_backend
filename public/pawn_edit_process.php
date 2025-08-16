<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Restrict to super_admin only
if ($_SESSION['user']['role'] !== 'super_admin') {
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

$pawn_id         = $_POST['pawn_id'] ?? null;
$owner_name      = $_POST['owner_name'] ?? '';
$contact_no      = $_POST['contact_no'] ?? '';
$unit_description= $_POST['unit_description'] ?? '';
$category        = $_POST['category'] ?? '';
$new_amount      = floatval($_POST['amount_pawned'] ?? 0);
$notes            = $_POST['notes'] ?? '';
$date_pawned     = $_POST['date_pawned'] ?? '';

if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Pawn ID is required"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch old pawn record
    $stmt = $pdo->prepare("SELECT amount_pawned, branch_id FROM pawned_items WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found.");
    }

    $old_amount = floatval($pawn['amount_pawned']);
    $branch_id  = $pawn['branch_id'];

    // Compute difference
    $difference = $new_amount - $old_amount;

    // Update pawned_items
    $stmt = $pdo->prepare("UPDATE pawned_items 
        SET owner_name = ?, contact_no = ?, unit_description = ?, category = ?, 
            amount_pawned = ?, notes = ?, date_pawned = ? 
        WHERE pawn_id = ?");
    $stmt->execute([$owner_name, $contact_no, $unit_description, $category, $new_amount, $notes, $date_pawned, $pawn_id]);

    // Adjust COH only if difference != 0
    if ($difference != 0) {
        $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?");
        $stmt->execute([$difference, $branch_id]);

        // Log to cash ledger
        $stmt = $pdo->prepare("INSERT INTO cash_ledger (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id) 
                               VALUES (?, 'pawn_edit', ?, ?, 'pawned_items', ?, ?, ?)");
        $direction = $difference > 0 ? 'out' : 'in'; // if increased amount, more cash out
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
