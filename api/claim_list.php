<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";



header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

$user_role  = $_SESSION['user']['role'] ?? 'cashier';
$branch_id  = $_SESSION['user']['branch_id'] ?? null;

// --- Branch filter for Super Admin ---
$filter_branch = $_GET['branch_id'] ?? null;

// --- Date filters ---
$start_date = $_GET['start_date'] ?? null;
$end_date   = $_GET['end_date'] ?? null;

$query = "
    SELECT 
        p.date_pawned AS `date_pawned`,
        c.date_claimed AS `date_claimed`,
        cu.full_name AS `owner_name`,
        p.unit_description AS `unit_description`,
        p.category AS `category`,
        p.amount_pawned AS `amount_pawned`,
        c.interest_amount AS `interest_amount`,
        c.penalty_amount as `penalty_amount`,
        c.total_paid AS `total_paid`,
        cu.contact_no AS `contact_no`,
        p.pawn_id,
        c.branch_id
    FROM claims c
    JOIN pawned_items p ON c.pawn_id = p.pawn_id
    JOIN customers cu ON p.customer_id = cu.customer_id
    WHERE 1=1
";

$params = [];

// If not super_admin, restrict to their branch
if ($user_role !== 'super_admin') {
    $query .= " AND c.branch_id = ? ";
    $params[] = $branch_id;
} else {
    // super admin can filter by branch
    if (!empty($filter_branch)) {
        $query .= " AND c.branch_id = ? ";
        $params[] = $filter_branch;
    }
}

// Apply date filtering
if (!empty($start_date)) {
    $query .= " AND c.date_claimed >= ? ";
    $params[] = $start_date;
}
if (!empty($end_date)) {
    $query .= " AND c.date_claimed <= ? ";
    $params[] = $end_date;
}

$query .= " ORDER BY c.date_claimed DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $actions = '';
    if ($user_role == 'admin' || $user_role == 'cashier') {
        $actions = '
            <div class="dropdown">
                <a href="#" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item viewClaimBtn" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-eye text-info"></i> View Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item printClaimBtn" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-printer"></i> Print Receipt
                        </a>
                    </li>
                </ul>
            </div>
        ';
    }

    if ($user_role == 'admin') {
        $actions = '
            <div class="dropdown">
                <a href="#" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item viewClaimBtn" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-eye text-info"></i> View Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item printClaimBtn" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-printer"></i> Print Receipt
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item revertClaimBtn text-warning" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-arrow-counterclockwise"></i> Revert to Pawned
                        </a>
                    </li>
                </ul>
            </div>
        ';
    }

    $rows[] = [
        htmlspecialchars(formatDateMDY($row['date_pawned'])),
        htmlspecialchars(formatDateMDY($row['date_claimed'])),
        htmlspecialchars($row['owner_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        '₱'.number_format($row['amount_pawned'],2),
        '₱'.number_format($row['interest_amount'],2),
        '₱'.number_format($row['penalty_amount'],2),
        '₱'.number_format($row['total_paid'],2),
        htmlspecialchars($row['contact_no']),
        $actions
    ];
}

echo json_encode(["data" => $rows]);
