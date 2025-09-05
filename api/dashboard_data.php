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
        p.original_amount_pawned,
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
// Shows total pawned amount + total income
// ============================
$trend_stmt = $pdo->prepare("
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
      AND p.branch_id = :branch_id
      AND p.date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month

    UNION ALL

    -- Tubo interest
    SELECT 
        DATE_FORMAT(tp.date_paid, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(tp.interest_amount) AS total_interest,
        0 AS total_penalty,
        SUM(tp.interest_amount) AS total_income
    FROM tubo_payments tp
    WHERE tp.date_paid >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND tp.branch_id = :branch_id
    GROUP BY month

    UNION ALL

    -- Partial payments interest
    SELECT 
        DATE_FORMAT(pp.created_at, '%Y-%m') AS month,
        0 AS total_pawned,
        SUM(pp.interest_paid) AS total_interest,
        0 AS total_penalty,
        SUM(pp.interest_paid) AS total_income
    FROM partial_payments pp
    WHERE pp.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND pp.branch_id = :branch_id
    GROUP BY month

    UNION ALL

    -- Penalties
    SELECT 
        DATE_FORMAT(c.date_claimed, '%Y-%m') AS month,
        0 AS total_pawned,
        0 AS total_interest,
        SUM(c.penalty_amount) AS total_penalty,
        SUM(c.penalty_amount) AS total_income
    FROM claims c
    WHERE c.date_claimed >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
      AND c.branch_id = :branch_id
    GROUP BY month
) t
GROUP BY t.month
ORDER BY t.month ASC;



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
