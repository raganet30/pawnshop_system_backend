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

// Fetch pawn item with customer details + photo_path
$sql = "
    SELECT 
        p.pawn_id,
        p.branch_id,
        p.customer_id,
        c.full_name AS customer_name,
        c.contact_no,
        c.address,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.original_amount_pawned,
        p.notes,
        p.date_pawned,
        p.status,
        p.photo_path,
        p.current_due_date
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
$branch_id = $_SESSION['user']['branch_id'] ?? $pawn['branch_id'] ?? 0;
$branchInterest = 0.06; // default if not found

if ($branch_id) {
    $stmtBranch = $pdo->prepare("SELECT interest_rate FROM branches WHERE branch_id = ?");
    $stmtBranch->execute([$branch_id]);
    $branch = $stmtBranch->fetch(PDO::FETCH_ASSOC);
    if ($branch) {
        $branchInterest = floatval($branch['interest_rate']);
    }
}

// Fetch tubo history
$sqlTubo = "
    SELECT *
    FROM tubo_payments
    WHERE pawn_id = ?
    ORDER BY date_paid DESC
";
$stmtTubo = $pdo->prepare($sqlTubo);
$stmtTubo->execute([$pawn_id]);
$tuboHistory = $stmtTubo->fetchAll(PDO::FETCH_ASSOC);

// Fetch partial payment history
$sqlPartial = "
    SELECT pp_id, DATE(created_at) AS date_paid, amount_paid, remaining_principal, notes
    FROM partial_payments
    WHERE pawn_id = ?
    ORDER BY created_at DESC
";
$stmtPartial = $pdo->prepare($sqlPartial);
$stmtPartial->execute([$pawn_id]);
$partialHistory = $stmtPartial->fetchAll(PDO::FETCH_ASSOC);

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "pawn" => $pawn,
    "branch_interest" => $branchInterest,
    "tubo_history" => $tuboHistory,
    "partial_history" => $partialHistory
]);
