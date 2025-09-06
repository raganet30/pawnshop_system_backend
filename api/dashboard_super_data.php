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

    -- Pawned items counts
    SUM(CASE WHEN p.status = 'pawned'    THEN 1 ELSE 0 END) AS total_pawned,
    SUM(CASE WHEN p.status = 'claimed'   THEN 1 ELSE 0 END) AS claimed,
    SUM(CASE WHEN p.status = 'forfeited' THEN 1 ELSE 0 END) AS forfeited,

    -- Total pawned value
    COALESCE(SUM(CASE WHEN p.status = 'pawned' THEN p.amount_pawned ELSE 0 END), 0) AS total_pawned_value,

    -- From tubo_payments (interest)
    COALESCE(tp_sum.total_tp_interest_amount, 0) AS total_tp_interest_amount,

    -- From claims (interest + penalty)
    COALESCE(c_sum.total_claim_interest_amount, 0) AS total_claim_interest_amount,

    -- From partial_payments (interest_paid)
    COALESCE(pp_sum.total_partial_interest, 0) AS total_partial_interest,

    -- Total income (all combined)
    ( COALESCE(tp_sum.total_tp_interest_amount, 0) 
    + COALESCE(c_sum.total_claim_interest_amount, 0) 
    + COALESCE(pp_sum.total_partial_interest, 0) ) AS total_income,

    b.cash_on_hand

FROM branches b

-- Pawned items join (for counts & pawned value)
LEFT JOIN pawned_items p 
    ON b.branch_id = p.branch_id 
   AND p.is_deleted = 0

-- Aggregate tubo payments per branch
LEFT JOIN (
    SELECT branch_id, SUM(interest_amount) AS total_tp_interest_amount
    FROM tubo_payments
    GROUP BY branch_id
) tp_sum ON b.branch_id = tp_sum.branch_id

-- Aggregate claims per branch (interest + penalty)
LEFT JOIN (
    SELECT branch_id, SUM(interest_amount + penalty_amount) AS total_claim_interest_amount
    FROM claims
    GROUP BY branch_id
) c_sum ON b.branch_id = c_sum.branch_id

-- Aggregate partial payments per branch
LEFT JOIN (
    SELECT branch_id, SUM(interest_paid) AS total_partial_interest
    FROM partial_payments
    GROUP BY branch_id
) pp_sum ON b.branch_id = pp_sum.branch_id

GROUP BY b.branch_id, b.branch_name, b.cash_on_hand;


")->fetchAll(PDO::FETCH_ASSOC);





// MONTHLY TRENDS (last 12 months)
$trend_stmt = $pdo->query("
SELECT 
    t.month,
    SUM(t.total_pawned)         AS total_pawned,
    SUM(t.total_interest)       AS total_interest,
    SUM(t.total_claim_interest) AS total_claim_interest,
    SUM(t.total_income)         AS total_income
FROM (
    -- Pawned amounts (only active pawned items)
    SELECT 
        DATE_FORMAT(p.date_pawned, '%Y-%m') AS month,
        SUM(CASE WHEN p.status = 'pawned' THEN p.amount_pawned ELSE 0 END) AS total_pawned,
        0 AS total_interest,
        0 AS total_claim_interest,
        0 AS total_income
    FROM pawned_items p
    WHERE p.is_deleted = 0
      AND p.date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Tubo interest
    SELECT 
        DATE_FORMAT(tp.date_paid, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(tp.interest_amount) AS total_interest,
        0 AS total_claim_interest,
        SUM(tp.interest_amount) AS total_income
    FROM tubo_payments tp
    WHERE tp.date_paid >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Partial payments interest
    SELECT 
        DATE_FORMAT(pp.created_at, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(pp.interest_paid) AS total_interest,
        0 AS total_claim_interest,
        SUM(pp.interest_paid) AS total_income
    FROM partial_payments pp
    WHERE pp.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Claims (interest + penalty)
    SELECT 
        DATE_FORMAT(c.date_claimed, '%Y-%m') AS month,
        0 AS total_pawned,
        0 AS total_interest,
        SUM(c.interest_amount + c.penalty_amount) AS total_claim_interest,
        SUM(c.interest_amount + c.penalty_amount) AS total_income
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
