<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

try {
    $pdo->beginTransaction();

    $branch_id  = $_SESSION['user']['branch_id'];
    $user_id    = $_SESSION['user']['id'];
    $full_name  = $_SESSION['user']['name'];

    $owner_name       = $_POST['owner_name'] ?? '';
    $contact_no       = $_POST['contact_no'] ?? '';
    $unit_description = $_POST['unit_description'] ?? '';
    $category         = $_POST['category'] ?? '';
    $amount_pawned    = (float) ($_POST['amount_pawned'] ?? 0);
    $notes            = $_POST['notes'] ?? null;
    $date_pawned      = $_POST['date_pawned'] ?? date("Y-m-d");

    // 1. Lock COH row
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
    $stmt->execute([$branch_id]);
    $current_cash = $stmt->fetchColumn();

    if ($current_cash === false) {
        throw new Exception("Branch cash record not found.");
    }

    if ($current_cash < $amount_pawned) {
        throw new Exception("Insufficient cash on hand. Available: ₱" . number_format($current_cash, 2));
    }

    // 2. Insert pawn item
    $stmt = $pdo->prepare("INSERT INTO pawned_items 
        (branch_id, owner_name, contact_no, unit_description, category, amount_pawned, notes, date_pawned, created_by, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pawned')");
    $stmt->execute([
        $branch_id,
        $owner_name,
        $contact_no,
        $unit_description,
        $category,
        $amount_pawned,
        $notes,
        $date_pawned,
        $user_id
    ]);

    // 3. Deduct COH
    $updateCash = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?");
    $updateCash->execute([$amount_pawned, $branch_id]);

    // 4. Log Audit
    $description = sprintf(
        "%s added a new pawn item: %s (Unit: %s, Category: %s, Amount: ₱%s) ",
        $full_name,
        $owner_name,
        $unit_description,
        $category,
        number_format($amount_pawned, 2)
    );

    logAudit($pdo, $user_id, $branch_id, 'Add Pawned Item', $description);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Pawn item added successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
