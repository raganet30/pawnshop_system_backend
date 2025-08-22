<?php
session_start();
require_once "../config/db.php";

// Only logged-in users can access
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Validate pawn_id
if (!isset($_GET['pawn_id']) || !is_numeric($_GET['pawn_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$pawn_id = intval($_GET['pawn_id']);

// Fetch pawn item with customer details
$sql = "
    SELECT 
        p.pawn_id,
        p.customer_id,
        c.full_name AS customer_name,
        c.contact_no,
        c.address,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.notes,
        p.date_pawned,
        p.status
    FROM pawned_items p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    WHERE p.pawn_id = ?
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pawn_id]);
$pawn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pawn) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Pawn record not found"]);
    exit();
}

// Fetch branch interest based on session branch
$branch_id = $_SESSION['user']['branch_id'] ?? 0;
$branchInterest = 6; // default if not found

if ($branch_id) {
    $stmtBranch = $pdo->prepare("SELECT interest_rate FROM branches WHERE branch_id = ?");
    $stmtBranch->execute([$branch_id]);
    $branch = $stmtBranch->fetch(PDO::FETCH_ASSOC);
    if ($branch) {
        $branchInterest = floatval($branch['interest_rate']);
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "pawn" => $pawn,
    "branch_interest" => $branchInterest
]);
