<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}


// branch id is set in the session for branch-specific views
$branch_id = $_SESSION['user']['branch_id'];



// Pawned Units & Value
$pawned_stats = $pdo->query("
    SELECT COUNT(*) AS total_units,
           COALESCE(SUM(amount_pawned),0) AS total_value
    FROM pawned_items
    WHERE status = 'pawned' AND is_deleted = 0 AND branch_id = $branch_id
")->fetch(PDO::FETCH_ASSOC);

// Cash on Hand
$stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$cash_on_hand = $stmt->fetchColumn() ?? 0;

// Claimed Items
$claimed_qty = $pdo->query("SELECT COUNT(*) FROM pawned_items WHERE status = 'claimed' AND is_deleted = 0 AND branch_id = $branch_id  ")->fetchColumn();

// Forfeited Items
$forfeited_qty = $pdo->query("SELECT COUNT(*) FROM pawned_items WHERE status = 'forfeited' AND is_deleted = 0 AND branch_id = $branch_id ")->fetchColumn();

// Daily Interest
$daily_interest = $pdo->query("
    SELECT COALESCE(SUM(interest_amount),0)
    FROM pawned_items
    WHERE status = 'claimed' AND DATE(date_claimed) = CURDATE() AND is_deleted = 0 AND branch_id = $branch_id
")->fetchColumn();

// Grand Interest
$grand_total_interest = $pdo->query("
    SELECT COALESCE(SUM(interest_amount),0)
    FROM pawned_items WHERE status = 'claimed' AND is_deleted = 0 AND branch_id = $branch_id
")->fetchColumn();

echo json_encode([
    "pawned_units" => $pawned_stats['total_units'] ?? 0,
    "pawned_value" => $pawned_stats['total_value'] ?? 0,
    "cash_on_hand" => $cash_on_hand,
    "claimed_qty" => $claimed_qty,
    "forfeited_qty" => $forfeited_qty,
    "daily_interest" => $daily_interest,
    "grand_total_interest" => $grand_total_interest
]);
