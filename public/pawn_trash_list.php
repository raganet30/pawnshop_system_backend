<?php
session_start();
require_once "../config/db.php";

if (!in_array($_SESSION['user']['role'], ['admin','super_admin'])) {
    echo json_encode(["data" => []]);
    exit();
}

$branch_id = $_SESSION["user"]["branch_id"];

$stmt = $pdo->query("SELECT pawn_id, date_pawned, owner_name, unit_description, category, amount_pawned, status 
                     FROM pawned_items 
                     WHERE is_deleted = 1 AND branch_id = $branch_id;
                     ORDER BY date_pawned DESC");

echo json_encode(["data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
