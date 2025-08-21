<?php
// dashboard_stats.php
session_start();
require_once "../config/db.php";

// ============================
// AUTH CHECK
// ============================
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$branch_id = $_SESSION['user']['branch_id'] ?? 1; // fallback to branch 1

// ============================
// PAWNED UNITS & VALUE
// ============================
$pawned_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_units,
        COALESCE(SUM(amount_pawned), 0) AS total_value
    FROM pawned_items
    WHERE status = 'pawned' AND branch_id = :branch_id AND is_deleted = 0
");
$pawned_stmt->execute(['branch_id' => $branch_id]);
$pawned_stats = $pawned_stmt->fetch(PDO::FETCH_ASSOC);

// ============================
// CASH ON HAND (branch table)
// ============================
$cash_stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = :branch_id");
$cash_stmt->execute(['branch_id' => $branch_id]);
$cash_on_hand = $cash_stmt->fetchColumn() ?? 0;

// ============================
// CLAIMED & FORFEITED COUNTS
// ============================
$claimed_stmt = $pdo->prepare("SELECT COUNT(*) FROM pawned_items WHERE status = 'claimed' AND branch_id = :branch_id AND is_deleted = 0");
$claimed_stmt->execute(['branch_id' => $branch_id]);
$claimed_qty = $claimed_stmt->fetchColumn();

$forfeited_stmt = $pdo->prepare("SELECT COUNT(*) FROM pawned_items WHERE status = 'forfeited' AND branch_id = :branch_id AND is_deleted = 0");
$forfeited_stmt->execute(['branch_id' => $branch_id]);
$forfeited_qty = $forfeited_stmt->fetchColumn();

// ============================
// DAILY INTEREST (from tubo_payments)
// ============================
$daily_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_amount), 0)
    FROM tubo_payments
    WHERE branch_id = :branch_id
      AND DATE(date_paid) = CURDATE()
");
$daily_stmt->execute(['branch_id' => $branch_id]);
$daily_interest = $daily_stmt->fetchColumn();

// ============================
// GRAND TOTAL INTEREST (all time)
// ============================
$grand_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_amount), 0)
    FROM tubo_payments
    WHERE branch_id = :branch_id
");
$grand_stmt->execute(['branch_id' => $branch_id]);
$grand_total_interest = $grand_stmt->fetchColumn();

// ============================
// RETURN JSON
// ============================
echo json_encode([
    "pawned_units"       => $pawned_stats['total_units'] ?? 0,
    "pawned_value"       => $pawned_stats['total_value'] ?? 0,
    "cash_on_hand"       => $cash_on_hand,
    "claimed_qty"        => $claimed_qty,
    "forfeited_qty"      => $forfeited_qty,
    "daily_interest"     => $daily_interest,
    "grand_total_interest" => $grand_total_interest
]);
