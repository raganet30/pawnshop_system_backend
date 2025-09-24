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

// --- Fetch claim details ---
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
        c.notes,
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

$cashierName = $_SESSION['user']['full_name'] ?? "Cashier";

if ($data) {
    // --- Fetch partial payments history ---
    $stmt2 = $pdo->prepare("
        SELECT 
            created_at AS date_paid,
            amount_paid,
            interest_paid,
            principal_paid,
            0 AS penalty_paid, -- no penalties in partial payments
            remaining_principal AS remaining_balance,
            'Partial payment' AS remarks
        FROM partial_payments
        WHERE pawn_id = ?
        ORDER BY created_at ASC
    ");
    $stmt2->execute([$pawn_id]);
    $partialPayments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // --- Append final settlement row ---
    $finalRow = [
        "date_paid"        => $data['date_claimed'],
        "amount_paid"      => $data['total_paid'],
        "interest_paid"    => $data['interest_amount'],
        "principal_paid"   => $data['amount_pawned'], // or remaining principal if tracked
        "penalty_paid"     => $data['penalty_amount'],
        "remaining_balance"=> 0,
        "remarks"          => "Full settlement"
    ];
    $partialPayments[] = $finalRow;

    // --- Fetch tubo payments history ---
    $stmt3 = $pdo->prepare("
        SELECT 
            date_paid,
            months_covered,
            period_start,
            period_end,
            interest_rate,
            interest_amount,
            'Tubo payment' AS remarks
        FROM tubo_payments
        WHERE pawn_id = ?
        ORDER BY date_paid ASC
    ");
    $stmt3->execute([$pawn_id]);
    $tuboPayments = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Attach histories to response
    $data['partial_payments'] = $partialPayments;
    $data['tubo_payments']    = $tuboPayments;

    // Extra print info
    $data['cashier']    = $cashierName;
    $data['printed_at'] = date("Y-m-d H:i:s");

    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "Claim not found"]);
}
