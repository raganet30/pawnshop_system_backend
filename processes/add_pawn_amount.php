<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$pawn_id = $_POST['pawn_id'] ?? null;
$add_amount = floatval($_POST['add_amount'] ?? 0);

if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Pawn ID is required"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch old pawn record
    $stmt = $pdo->prepare("SELECT amount_pawned, branch_id, category, unit_description FROM pawned_items WHERE pawn_id = ? AND is_deleted = 0");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        throw new Exception("Pawn record not found.");
    }

    $old_amount = floatval($pawn['amount_pawned']);
    $branch_id = $pawn['branch_id'];
    $category = $pawn['category'] ?? null;
    $unit_description = $pawn['unit_description'] ?? '';

    // Compute new amount
    $new_amount = $old_amount + $add_amount;
    $difference = $new_amount - $old_amount;

    // Get interest rate (optional, if you need)
    $interest_rate = getInterestRate($pdo, $branch_id, $category);

    // Update pawned item
    $stmt = $pdo->prepare("UPDATE pawned_items 
        SET amount_pawned = ?, original_amount_pawned = ?
        WHERE pawn_id = ?");
    $stmt->execute([$new_amount, $new_amount, $pawn_id]);

    // Adjust COH only if amount changed
    if ($difference != 0) {
        $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
        $stmt->execute([$branch_id]);
        $branchRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$branchRow) {
            throw new Exception("Branch not found.");
        }

        $current_coh = floatval($branchRow['cash_on_hand']);
        $new_coh = $current_coh - $difference;

        if ($new_coh < 0) {
            throw new Exception("Not enough Cash On Hand balance for this operation.");
        }

        $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = ? WHERE branch_id = ?");
        $stmt->execute([$new_coh, $branch_id]);

        // Insert into cash ledger
        $direction = ($difference > 0) ? "out" : "in";
        $amount = abs($difference);
        $description = "Pawn Add Amount (ID #$pawn_id)";
        $notes = "Pawn amount adjusted from ₱" . number_format($old_amount, 2) . " to ₱" . number_format($new_amount, 2);
        $user_id = $_SESSION['user']['id'] ?? null;

        insertCashLedger(
            $pdo,
            $branch_id,
            "pawn_add_amount",
            $direction,
            $amount,
            "pawned_items",
            $pawn_id,
            $description,
            $notes,
            $user_id
        );
    }

    // Insert audit log
    $user_id = $_SESSION['user']['id'] ?? null;
    $audit_desc = "Add pawn item amount: $unit_description, adjusted amount from ₱" . number_format($old_amount, 2) . " to ₱" . number_format($new_amount, 2);
    logAudit($pdo, $user_id, $branch_id, 'Edit Pawn Item', $audit_desc);

    $pdo->commit();

    // Build response
    $adjustment_text = ($difference > 0) ? "-₱" . number_format($difference, 2) :
        (($difference < 0) ? "+₱" . number_format(abs($difference), 2) : "No change");

    echo json_encode([
        "status" => "success",
        "message" => "Pawn item updated successfully. Cash on Hand adjustment: $adjustment_text"
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
