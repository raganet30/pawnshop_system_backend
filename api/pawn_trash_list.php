<?php
session_start();
require_once "../config/db.php";

if (!in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    echo json_encode(["data" => []]);
    exit();
}

$branch_id = $_SESSION["user"]["branch_id"];

$stmt = $pdo->query("SELECT 
                    p.pawn_id,
                    p.date_pawned,
                    c.full_name as owner_name,
                    p.unit_description,
                    p.category,
                    p.amount_pawned,
                    p.status
                FROM pawned_items p
                JOIN customers c ON p.customer_id = c.customer_id
                WHERE p.is_deleted = 1 AND p.branch_id = $branch_id
                ORDER BY p.date_pawned DESC");

echo json_encode(["data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
