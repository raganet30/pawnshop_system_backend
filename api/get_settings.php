<?php
session_start();
require_once "../config/db.php";

// Only allow logged in users
if (!isset($_SESSION['user'])) {
    echo json_encode(["success"=>false, "message"=>"Unauthorized"]);
    exit;
}

$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(["success"=>true, "data"=>$settings]);
