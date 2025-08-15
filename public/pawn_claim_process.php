<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$pawn_id = $_POST['pawn_id'] ?? null;
if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Pawn ID is required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? LIMIT 1");
$stmt->execute([$pawn_id]);
$pawn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pawn) {
    echo json_encode(["status" => "error", "message" => "Pawn record not found"]);
    exit;
}

$date_pawned = new DateTime($pawn['date_pawned']);
$today = new DateTime();
$diffDays = max(1, ceil($today->diff($date_pawned)->days));
$months = max(1, ceil($diffDays / 30));

$interest = $pawn['amount_pawned'] * 0.06 * $months;
// $total = $pawn['amount_pawned'] + $interest;

// Update database
$update = $pdo->prepare("UPDATE pawned_items SET status = 'claimed', date_claimed = NOW(), interest_amount = ? WHERE pawn_id = ?");
$update->execute([$interest, $pawn_id]);

echo json_encode(["status" => "success", "message" => "Item successfully claimed."]);
