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

$branch_id = $_SESSION['user']['branch_id']; // branch id

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
// DAILY INCOME = interest (tubo_payments) + penalties (claims)
// ============================

// Daily tubo payments interest
$daily_tp_interest_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_amount), 0)
    FROM tubo_payments
    WHERE branch_id = :branch_id
      AND DATE(date_paid) = CURDATE()
");
$daily_tp_interest_stmt->execute(['branch_id' => $branch_id]);
$daily_tp_interest = $daily_tp_interest_stmt->fetchColumn();

// Daily Claim Interest + Penalty
$daily_claim_interest_stmt = $pdo->prepare("
    SELECT COALESCE(SUM( interest_amount + penalty_amount), 0)
    FROM claims
    WHERE branch_id = :branch_id
      AND DATE(date_claimed) = CURDATE()
");
$daily_claim_interest_stmt->execute(['branch_id' => $branch_id]);
$daily_claim_interest = $daily_claim_interest_stmt->fetchColumn();


//Daily Partial Interest
$daily_partial_interest_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_paid), 0)
    FROM partial_payments
    WHERE branch_id = :branch_id
    AND DATE(created_at) = CURDATE()
");
$daily_partial_interest_stmt->execute(['branch_id' => $branch_id]);
$daily_partial_interest = $daily_partial_interest_stmt->fetchColumn();


// Final daily income
$daily_income = $daily_tp_interest + $daily_claim_interest + $daily_partial_interest;


// ============================
// GRAND TOTAL INCOME = interest (tubo_payments) + penalties (claims) + partial payments (min. interest)
// ============================

// Total tubo payments interest
$grand_tp_interest_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_amount), 0)
    FROM tubo_payments
    WHERE branch_id = :branch_id
");
$grand_tp_interest_stmt->execute(['branch_id' => $branch_id]);
$grand_tp_interest = $grand_tp_interest_stmt->fetchColumn();



// Total claim interest + penalty
$grand_claim_interest_stmt = $pdo->prepare("
    SELECT 
    COALESCE(SUM(interest_amount + penalty_amount), 0)
FROM claims
WHERE branch_id = :branch_id;

");
$grand_claim_interest_stmt->execute(['branch_id' => $branch_id]);
$grand_claim_interest = $grand_claim_interest_stmt->fetchColumn();


// Total partial payment interests
$grand_partial_interest_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(interest_paid), 0)
    FROM partial_payments
    WHERE branch_id = :branch_id
");
$grand_partial_interest_stmt->execute(['branch_id' => $branch_id]);
$grand_partial_interest = $grand_partial_interest_stmt->fetchColumn();




// Final grand total income
$grand_income = $grand_tp_interest + $grand_claim_interest + $grand_partial_interest;

// ============================
// RETURN JSON
// ============================
echo json_encode([
    "pawned_units" => $pawned_stats['total_units'] ?? 0,
    "pawned_value" => $pawned_stats['total_value'] ?? 0,
    "cash_on_hand" => $cash_on_hand,
    "claimed_qty" => $claimed_qty,
    "forfeited_qty" => $forfeited_qty,
    "daily_interest" => $daily_income,
    "grand_total_interest" => $grand_income
]);
