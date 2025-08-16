<?php
session_start();

require_once "../config/db.php";

// Only logged-in users can access
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Validate pawn_id
if (!isset($_GET['pawn_id']) || !is_numeric($_GET['pawn_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$pawn_id = intval($_GET['pawn_id']);

// Fetch pawn item
$stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? LIMIT 1");
$stmt->execute([$pawn_id]);
$pawn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pawn) {
    http_response_code(404);
    echo json_encode(["error" => "Pawn record not found"]);
    exit();
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($pawn);
