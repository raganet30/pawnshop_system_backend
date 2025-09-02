<?php
session_start();
require_once "../config/db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}




$pawn_id = $_GET['pawn_id'] ?? null;
if (!$pawn_id) {
    echo json_encode(["status" => "error", "message" => "Missing pawn_id"]);
    exit();
}

$query = "
    SELECT 
        p.pawn_id,
        p.date_pawned,
        p.unit_description,
        p.category,
        p.amount_pawned,
        c.date_claimed,
        c.interest_amount,
        c.total_paid,
        c.penalty_amount,
        cu.full_name,
        cu.contact_no,
        cu.address,
        c.photo_path,
        b.branch_name,
        b.branch_address,
        b.branch_phone
    FROM claims c
    JOIN pawned_items p ON c.pawn_id = p.pawn_id
    JOIN customers cu ON p.customer_id = cu.customer_id
    JOIN branches b ON p.branch_id = b.branch_id
    WHERE c.pawn_id = ?
    LIMIT 1
";

$stmt = $pdo->prepare($query);
$stmt->execute([$pawn_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$cashierName = isset($_SESSION['user']['full_name']) 
    ? $_SESSION['user']['full_name'] 
    : "Cashier";

if ($data) {
    // Add extra print info
    // $data['or_no'] = "OR-" . rand(10000, 99999);
    $data['cashier'] = $_SESSION['user']['full_name'] ?? "Cashier";
    $data['printed_at'] = date("m/d/Y h:i A");

     $data['cashier'] = $cashierName;
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "Claim not found"]);
}
 