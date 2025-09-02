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
    DATE_FORMAT(p.date_pawned, '%Y-%m') AS month,

    -- Total pawned items
    COALESCE(SUM(CASE WHEN p.status = 'pawned' AND is_deleted = 0 THEN p.amount_pawned ELSE 0 END), 0) AS total_pawned,

    -- Total interest (from tubo_payments)
    (
        SELECT COALESCE(SUM(t.interest_amount), 0)
        FROM tubo_payments t
        WHERE t.branch_id = :branch_id
          AND DATE_FORMAT(t.date_paid, '%Y-%m') = DATE_FORMAT(p.date_pawned, '%Y-%m')
    ) AS total_interest,

    -- Total interest (from partial payments)
    (
        SELECT COALESCE(SUM(pp.interest_paid), 0)
        FROM partial_payments pp
        WHERE pp.branch_id = :branch_id
          AND DATE_FORMAT(pp.created_at, '%Y-%m') = DATE_FORMAT(p.date_pawned, '%Y-%m')
    ) AS total_partial_interest,

    -- Total penalties (from claims)
    (
        SELECT COALESCE(SUM(c.penalty_amount), 0)
        FROM claims c
        WHERE c.branch_id = :branch_id
          AND DATE_FORMAT(c.date_claimed, '%Y-%m') = DATE_FORMAT(p.date_pawned, '%Y-%m')
    ) AS total_penalty

FROM pawned_items p
WHERE p.date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
  AND p.branch_id = :branch_id
GROUP BY month
ORDER BY month ASC;

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
