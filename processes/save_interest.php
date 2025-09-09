<?php
session_start();
header('Content-Type: application/json');
require_once "../config/db.php";
require_once "../config/helpers.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}


    $branch_id = $_SESSION['user']['branch_id'];
    $user_id = $_SESSION['user']['id'];
    $full_name = $_SESSION['user']['full_name'];

if ($_SESSION['user']['role'] === 'cashier') {
    echo json_encode(["success" => false, "message" => "Permission denied."]);
    exit();
}

if (!isset($_POST['rate']) || !is_numeric($_POST['rate'])) {
    echo json_encode(["success" => false, "message" => "Invalid interest rate."]);
    exit();
}

$newRate = floatval($_POST['rate']);

$newCustomRate = isset($_POST['custom_rate']) ? floatval($_POST['custom_rate']) : null;
if ($newCustomRate === null || !is_numeric($newCustomRate)) {
    echo json_encode(["success" => false, "message" => "Invalid custom interest rate."]);
    exit();
}$newCustomRate = isset($_POST['custom_rate']) ? floatval($_POST['custom_rate']) : null;


// Update branch interest rate
$stmt = $pdo->prepare("UPDATE branches SET interest_rate = ?, custom_interest_rate1=? WHERE branch_id = ?");
if ($stmt->execute([$newRate, $newCustomRate, $branch_id])) {
    
    
    

    $description ="Updated rates: default {$newRate}%, motorcycle {$newCustomRate}%";
    logAudit($pdo, $user_id, $branch_id, 'Interest Rate Adjustment', $description);



    echo json_encode(["success" => true, "message" => "Interest rate updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database update failed."]);
}
