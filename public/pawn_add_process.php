<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

try {
    $pdo->beginTransaction();

    $branch_id = $_SESSION['user']['branch_id'];
    $amount_pawned = (float) $_POST['amount_pawned'];

    // 1. Check current COH
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
    $stmt->execute([$branch_id]);
    $current_cash = $stmt->fetchColumn();

    if ($current_cash === false) {
        throw new Exception("Branch cash record not found.");
    }

    // 2. Validation: prevent negative COH
    if ($current_cash < $amount_pawned) {
        throw new Exception("Insufficient cash on hand. Available: ₱" . number_format($current_cash, 2));
    }

    // 3. Insert pawn item
    $stmt = $pdo->prepare("INSERT INTO pawned_items 
        (branch_id, owner_name, contact_no, unit_description, category, amount_pawned, notes, date_pawned, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pawned')");
    $stmt->execute([
        $branch_id,
        $_POST['owner_name'],
        $_POST['contact_no'],
        $_POST['unit_description'],
        $_POST['category'],
        $amount_pawned,
        $_POST['notes'] ?? null,
        $_POST['date_pawned']
    ]);

    // 4. Deduct from COH
    $updateCash = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?");
    $updateCash->execute([$amount_pawned, $branch_id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Pawn item added successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
