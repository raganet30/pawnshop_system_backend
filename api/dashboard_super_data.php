<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

require_once "../config/db.php";

// GLOBAL CASH
$global_cash = $pdo->query("SELECT COALESCE(SUM(cash_on_hand), 0) FROM branches")->fetchColumn();

// BRANCH STATS
$branch_stats = $pdo->query("
   SELECT 
    b.branch_id, 
    b.branch_name,
    SUM(CASE WHEN p.status = 'pawned' AND p.is_deleted=0 THEN 1 ELSE 0 END) AS total_pawned,
    SUM(CASE WHEN p.status = 'claimed' AND p.is_deleted=0 THEN 1 ELSE 0 END) AS claimed,
    SUM(CASE WHEN p.status = 'forfeited' AND p.is_deleted=0 THEN 1 ELSE 0 END) AS forfeited,
    COALESCE(SUM(CASE WHEN p.status = 'pawned' AND p.is_deleted=0 THEN p.amount_pawned ELSE 0 END), 0) AS total_pawned_value,
    COALESCE(SUM(tp.interest_amount), 0) AS total_interest_amount,
    COALESCE(SUM(c.penalty_amount), 0) AS total_penalty_amount,
    COALESCE(SUM(pp_sum.total_interest_paid), 0) AS total_partial_interest,
    (COALESCE(SUM(tp.interest_amount), 0) + COALESCE(SUM(c.penalty_amount), 0) + COALESCE(SUM(pp_sum.total_interest_paid), 0)) AS total_income,
    b.cash_on_hand
FROM branches b
LEFT JOIN pawned_items p 
    ON b.branch_id = p.branch_id 
    AND p.is_deleted = 0
LEFT JOIN tubo_payments tp 
    ON p.pawn_id = tp.pawn_id 
LEFT JOIN claims c
    ON p.pawn_id = c.pawn_id 
LEFT JOIN (
    SELECT pawn_id, SUM(interest_paid) AS total_interest_paid
    FROM partial_payments
    GROUP BY pawn_id
) pp_sum ON p.pawn_id = pp_sum.pawn_id
GROUP BY b.branch_id, b.branch_name;

")->fetchAll(PDO::FETCH_ASSOC);

// MONTHLY TRENDS (last 12 months)
$trend_stmt = $pdo->query("
  SELECT 
    t.month,
    SUM(t.total_pawned)   AS total_pawned,
    SUM(t.total_interest) AS total_interest,
    SUM(t.total_penalty)  AS total_penalty,
    SUM(t.total_income)   AS total_income
FROM (
    -- Pawned amounts (by date_pawned, only active pawned items)
    SELECT 
        DATE_FORMAT(p.date_pawned, '%Y-%m') AS month,
        SUM(CASE WHEN p.status = 'pawned' THEN p.amount_pawned ELSE 0 END) AS total_pawned,
        0 AS total_interest,
        0 AS total_penalty,
        0 AS total_income
    FROM pawned_items p
    WHERE p.is_deleted = 0
      AND p.date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Tubo interest (by date_paid)
    SELECT 
        DATE_FORMAT(tp.date_paid, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(tp.interest_amount) AS total_interest,
        0 AS total_penalty,
        SUM(tp.interest_amount) AS total_income
    FROM tubo_payments tp
    WHERE tp.date_paid >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Partial payments interest (by created_at)
    SELECT 
        DATE_FORMAT(pp.created_at, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(pp.interest_paid) AS total_interest,
        0 AS total_penalty,
        SUM(pp.interest_paid) AS total_income
    FROM partial_payments pp
    WHERE pp.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Penalties (by date_claimed)
    SELECT 
        DATE_FORMAT(c.date_claimed, '%Y-%m') AS month,
        0 AS total_pawned,
        0 AS total_interest,
        SUM(c.penalty_amount) AS total_penalty,
        SUM(c.penalty_amount) AS total_income
    FROM claims c
    WHERE c.date_claimed >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month
) t
GROUP BY t.month
ORDER BY t.month ASC;


");
$trend_data = $trend_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "global_cash" => $global_cash,
    "branch_stats" => $branch_stats,
    "trend_data" => $trend_data
]);
