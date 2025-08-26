<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

// --- Ensure user is logged in ---
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

$user_role  = $_SESSION['user']['role'] ?? 'cashier';
$branch_id  = $_SESSION['user']['branch_id'] ?? null;

// Branch filter (if super admin)
$filter_branch = $_GET['branch_id'] ?? null;

$query = "
    SELECT 
        f.pawn_id,
        f.date_forfeited,
        f.reason,
        f.notes,
        p.date_pawned,
        p.unit_description,
        p.category,
        p.amount_pawned,
        cu.full_name AS owner_name,
        cu.contact_no
    FROM forfeitures f
    JOIN pawned_items p ON f.pawn_id = p.pawn_id
    JOIN customers cu ON p.customer_id = cu.customer_id
    WHERE 1=1
";

$params = [];

// Restrict by branch if not super admin
if ($user_role !== 'super_admin') {
    $query .= " AND f.branch_id = ? ";
    $params[] = $branch_id;
} else {
    if (!empty($filter_branch)) {
        $query .= " AND f.branch_id = ? ";
        $params[] = $filter_branch;
    }
}

$query .= " ORDER BY f.date_forfeited DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $actions = '';
    if ($user_role !== 'cashier') {
        $actions = '
            <div class="dropdown">
                <a href="#" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item revertForfeitBtn text-warning" href="#" data-id="'.$row['pawn_id'].'">
                            <i class="bi bi-arrow-counterclockwise"></i> Revert to Pawn Items
                        </a>
                    </li>
                </ul>
            </div>
        ';
    }

    $rows[] = [
        htmlspecialchars($row['date_pawned']),
        htmlspecialchars($row['date_forfeited']),
        htmlspecialchars($row['owner_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        '₱'.number_format($row['amount_pawned'],2),
        htmlspecialchars($row['contact_no']),
        htmlspecialchars($row['reason']),
        $actions
    ];
}

echo json_encode(["data" => $rows]);
