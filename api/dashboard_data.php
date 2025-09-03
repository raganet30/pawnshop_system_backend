<?php
// dashboard_data.php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

// ============================
// AUTH CHECK
// ============================
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Get branch_id from session (default = 1 for safety)
$branch_id = $_SESSION['user']['branch_id'] ?? 1;

// ============================
// RECENT PAWNED ITEMS
// Last 20 items for this branch
// ============================
$recent_stmt = $pdo->prepare("
    SELECT 
        p.pawn_id,
        p.date_pawned,
        c.full_name AS owner_name,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.status
    FROM pawned_items p
    INNER JOIN customers c ON p.customer_id = c.customer_id
    WHERE p.branch_id = :branch_id and p.is_deleted=0
    ORDER BY p.updated_at DESC
    LIMIT 20
");
$recent_stmt->execute(['branch_id' => $branch_id]);
$recent_items = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// MONTHLY TRENDS (Last 12 months)
// Shows total pawned amount + total tubo interest collected
// ============================
$trend_stmt = $pdo->prepare("
   SELECT 
    m.month,
    COALESCE(SUM(m.interest_income), 0)     AS total_interest,
    COALESCE(SUM(m.partial_interest), 0)    AS total_partial_interest,
    COALESCE(SUM(m.penalty_income), 0)      AS total_penalty,
    (COALESCE(SUM(m.interest_income), 0) 
     + COALESCE(SUM(m.partial_interest), 0) 
     + COALESCE(SUM(m.penalty_income), 0))  AS total_income
FROM (
    -- Interest from tubo_payments
    SELECT 
        DATE_FORMAT(tp.date_paid, '%Y-%m') AS month,
        tp.branch_id,
        SUM(tp.interest_amount) AS interest_income,
        0 AS partial_interest,
        0 AS penalty_income
    FROM tubo_payments tp
    WHERE tp.date_paid >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND tp.branch_id = :branch_id
    GROUP BY month, tp.branch_id

    UNION ALL

    -- Interest from partial_payments
    SELECT 
        DATE_FORMAT(pp.created_at, '%Y-%m') AS month,
        pp.branch_id,
        0 AS interest_income,
        SUM(pp.interest_paid) AS partial_interest,
        0 AS penalty_income
    FROM partial_payments pp
    WHERE pp.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND pp.branch_id = :branch_id
    GROUP BY month, pp.branch_id

    UNION ALL

    -- Penalties from claims
    SELECT 
        DATE_FORMAT(c.date_claimed, '%Y-%m') AS month,
        c.branch_id,
        0 AS interest_income,
        0 AS partial_interest,
        SUM(c.penalty_amount) AS penalty_income
    FROM claims c
    WHERE c.date_claimed >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND c.branch_id = :branch_id
    GROUP BY month, c.branch_id
) m
GROUP BY m.month
ORDER BY m.month ASC;


");
$trend_stmt->execute(['branch_id' => $branch_id]);
$trend_data = $trend_stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// RETURN AS JSON
// ============================
echo json_encode([
    "recent_items" => $recent_items,
    "trend_data" => $trend_data
]);
