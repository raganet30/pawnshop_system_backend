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

// Update branch interest rate
$stmt = $pdo->prepare("UPDATE branches SET interest_rate = ? WHERE branch_id = ?");
if ($stmt->execute([$newRate, $branch_id])) {
    
    
    // Insert into audit logs
    $stmtLog = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, description, branch_id, created_at) 
                              VALUES (?, 'update', ?, ?, NOW())");
    $stmtLog->execute([
        $user_id,
        "Updated interest rate to {$newRate}%",
        $branch_id
    ]);



    echo json_encode(["success" => true, "message" => "Interest rate updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database update failed."]);
}
