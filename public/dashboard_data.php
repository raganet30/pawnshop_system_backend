<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

/* ===========================
   RECENT PAWNED ITEMS (ALL)
   =========================== */
$recent_items = $pdo->query("
    SELECT pawn_id, date_pawned, owner_name, unit_description, category, amount_pawned, status 
    FROM pawned_items where is_deleted = 0
    ORDER BY updated_at DESC 
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

/* ===========================
   MONTHLY TRENDS
   =========================== */
$trend_stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(date_pawned, '%Y-%m') AS month,
        COALESCE(SUM(amount_pawned), 0) AS total_pawned,
        COALESCE(SUM(interest_amount), 0) AS total_interest
    FROM pawned_items
    WHERE date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND is_deleted = 0
    GROUP BY month
    ORDER BY month ASC
");
$trend_data = $trend_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "recent_items" => $recent_items,
    "trend_data"   => $trend_data
]);
