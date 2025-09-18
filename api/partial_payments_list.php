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
            pp.pp_id,
            pp.pawn_id,
            pp.created_at AS date_paid,
            c.full_name AS customer,
            pi.unit_description AS item,
            pi.original_amount_pawned AS original_amount_pawned,
            pp.amount_paid,
            pp.interest_paid,
            pp.principal_paid,
            pp.remaining_principal AS remaining_balance,
            pp.status,
            u.username AS cashier
        FROM partial_payments pp
        INNER JOIN pawned_items pi ON pp.pawn_id = pi.pawn_id
        INNER JOIN customers c ON pi.customer_id = c.customer_id
        INNER JOIN users u ON pp.user_id = u.user_id
        WHERE 1=1
    ";

    $params = [];


    // Branch filter
    if ($branch_id !== '') { // instead of !empty()
        $query .= " AND pi.branch_id = :branch_id";
        $params['branch_id'] = $branch_id;
    } else {
        // Non-super_admin users are restricted to their branch
        if ($_SESSION['user']['role'] !== 'super_admin') {
            $query .= " AND pi.branch_id = :user_branch_id";
            $params['user_branch_id'] = $_SESSION['user']['branch_id'];
        }
    }


    // Date filters
    if (!empty($from_date)) {
        $query .= " AND DATE(pp.created_at) >= :from_date";
        $params['from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $query .= " AND DATE(pp.created_at) <= :to_date";
        $params['to_date'] = $to_date;
    }

    $query .= " ORDER BY pp.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Add serial + computed receipt_no
    $data = [];
    $counter = 1;
    foreach ($payments as $row) {
        $row['serial'] = $counter++;

        //  Auto-generate receipt_no here (pawnId-mmddyy)
        $d = new DateTime($row['date_paid']);
        $mm = $d->format('m');
        $dd = $d->format('d');
        $yy = $d->format('y');
        $receipt_no = str_pad($row['pawn_id'], 3, '0', STR_PAD_LEFT) . '-' . $mm . $dd . $yy;
        $row['receipt_no'] = $receipt_no;

        $data[] = $row;
    }



    // // Add serial numbers
    // $data = [];
    // $counter = 1;
    // foreach ($payments as $row) {
    //     $row['serial'] = $counter++;
    //     $data[] = $row;
    // }

    echo json_encode(["data" => $data]);

} catch (Exception $e) {
    echo json_encode(["data" => []]);
}
