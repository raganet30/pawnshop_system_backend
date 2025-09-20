<?php
session_start();
require_once "../config/db.php";

// Check login
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit;
}

$branch_id = $_POST['branch_id'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

try {
    $query = "
        SELECT 
    tp.tubo_id,
    tp.pawn_id,
    tp.branch_id,
    tp.date_paid,
    tp.period_start,
    tp.period_end,
    tp.months_covered,
    tp.interest_amount,
    pi.unit_description AS item,
    pi.original_amount_pawned,
    c.full_name AS owner
FROM tubo_payments tp
INNER JOIN pawned_items pi ON tp.pawn_id = pi.pawn_id
INNER JOIN customers c ON pi.customer_id = c.customer_id

    ";

    $params = [];

    // Branch filter
if (!empty($branch_id)) {
    $query .= " AND tp.branch_id = :branch_id";
    $params['branch_id'] = $branch_id;
} elseif ($_SESSION['user']['role'] !== 'super_admin') {
    // Non-super_admins can only see their branch
    $query .= " AND tp.branch_id = :user_branch_id";
    $params['user_branch_id'] = $_SESSION['user']['branch_id'];
}
// super_admin with empty branch_id => no filter, get all branches


    // Date filters
    if (!empty($from_date)) {
        $query .= " AND DATE(tp.date_paid) >= :from_date";
        $params['from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $query .= " AND DATE(tp.date_paid) <= :to_date";
        $params['to_date'] = $to_date;
    }

    $query .= " ORDER BY tp.date_paid DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add serial numbers
    $data = [];
    $counter = 1;
    foreach ($payments as $row) {
        $row['serial'] = $counter++;
        $data[] = $row;
    }

    echo json_encode(["data" => $data]);

} catch (Exception $e) {
    echo json_encode(["data" => []]);
}
