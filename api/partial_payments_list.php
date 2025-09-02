<?php
session_start();
require_once "../config/db.php";

// Check login
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            pp.pp_id,
            pp.created_at AS date_paid,
            c.full_name AS customer,
            pi.unit_description AS item,
            pp.amount_paid,
            pp.interest_paid,
            pp.principal_paid,
            pp.remaining_principal AS remaining_balance,
            u.username AS cashier
        FROM partial_payments pp
        INNER JOIN pawned_items pi ON pp.pawn_id = pi.pawn_id
        INNER JOIN customers c ON pi.customer_id = c.customer_id
        INNER JOIN users u ON pp.user_id = u.user_id
        WHERE pi.branch_id = :branch_id
        ORDER BY pp.created_at DESC
    ");
    $stmt->execute(['branch_id' => $_SESSION['user']['branch_id']]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add serial number and formatted date
    $data = [];
    $counter = 1;
    foreach ($payments as $row) {
        $row['serial'] = $counter++;
        $row['date_paid'];
        $data[] = $row;
    }

    echo json_encode(["data" => $data]);

} catch (Exception $e) {
    echo json_encode(["data" => []]);
}
?>
