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

$user_role = $_SESSION['user']['role'] ?? 'cashier';
$session_branch_id = $_SESSION['user']['branch_id'] ?? null;

// Super admin can filter branch via AJAX request
$selected_branch_id = $_GET['branch_id'] ?? null;

// Date filters
$start_date = $_GET['start_date'] ?? null;
$end_date   = $_GET['end_date'] ?? null;

// Apply branch filter
$params = [];
if ($user_role === 'super_admin') {
    if (!empty($selected_branch_id)) {
        $where = "WHERE p.status = 'pawned' AND p.is_deleted = 0 AND p.branch_id = ?";
        $params[] = $selected_branch_id;
    } else {
        $where = "WHERE p.status = 'pawned' AND p.is_deleted = 0";
    }
} else {
    // Non-super_admin users are locked to their session branch
    $where = "WHERE p.status = 'pawned' AND p.is_deleted = 0 AND p.branch_id = ?";
    $params[] = $session_branch_id;
}

// Apply date filters
if ($start_date) {
    $where .= " AND DATE(p.date_pawned) >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $where .= " AND DATE(p.date_pawned) <= ?";
    $params[] = $end_date;
}

// Fetch pawned items
$sql = "
    SELECT 
        p.pawn_id,
        p.date_pawned,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.notes,
        c.full_name,
        c.contact_no,
        c.address
    FROM pawned_items p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    $where
    ORDER BY p.date_pawned DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = [];
$totalPawned = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalPawned += floatval($row['amount_pawned']);

    // Build actions dropdown (only if user has access)
    $actions = '';
    if (in_array($user_role, ['admin', 'cashier'])) {
        $actions .= '
            <a href="#" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots fs-5"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
        ';

        // Edit (admin only)
        if ($user_role === 'admin') {
            $actions .= '
                <li>
                    <a class="dropdown-item editPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                        <i class="bi bi-pencil-square text-primary"></i> Edit
                    </a>
                </li>
            ';
        }

        // Claim (admin + cashier)
        $actions .= '
            <li>
                <a class="dropdown-item claimPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-cash-coin text-success"></i> Claim
                </a>
            </li>
        ';

        // Forfeit + Delete (admin only)
        if ($user_role === 'admin') {
            $actions .= '
                <li>
                    <a class="dropdown-item forfeitPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Forfeit
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item deletePawnBtn text-danger" href="#" data-id="' . $row['pawn_id'] . '">
                        <i class="bi bi-trash"></i> Move to Trash
                    </a>
                </li>
            ';
        }

        $actions .= '</ul>';
    }


    // Build row for DataTable
    $rowData = [
        null,
        $row['date_pawned'],
        htmlspecialchars($row['full_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        '₱' . number_format($row['amount_pawned'], 2),
        htmlspecialchars($row['contact_no']),
        htmlspecialchars($row['address']),
        htmlspecialchars($row['notes']),
    ];

    // Append Actions column only if applicable
    if (in_array($user_role, ['admin', 'cashier'])) {
        $rowData[] = $actions;
    }

    $rows[] = $rowData;
}

// Return JSON including total pawned
echo json_encode([
    "data" => $rows,
    "total_pawned" => number_format($totalPawned, 2)
]);
