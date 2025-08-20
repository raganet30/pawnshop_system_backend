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
$branch_id = $_SESSION['user']['branch_id'] ?? null;

// branch filtering
// This will set $params to an empty array for super_admin, or to [branch_id] for other roles
$params = [];
$where = branchFilter($user_role, $branch_id, $params);

// Build query dynamically
$sql = "SELECT * FROM pawned_items $where ORDER BY date_pawned DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Start dropdown
    $actions = '
         <a href="#" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots fs-5"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
    ';

    // Only super_admin can see Edit
    if ($user_role === 'admin') {
        $actions .= '
        <li>
            <a class="dropdown-item editPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                <i class="bi bi-pencil-square text-primary"></i> Edit
            </a>
        </li>
        ';
    }

    if ($user_role === 'admin' || $user_role === 'cashier') {

        $actions .= '
        <li>
            <a class="dropdown-item claimPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                <i class="bi bi-cash-coin text-success"></i> Claim
            </a>
        </li>
    ';
    }

    // Forfeit (admin only)
    if (in_array($user_role, ['admin'])) {
        $actions .= '
            <li>
                <a class="dropdown-item forfeitPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Forfeit
                </a>
            </li>
        ';

        $actions .= '
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item deletePawnBtn text-danger" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-trash"></i> Move to Trash
                </a>
            </li>
        ';
    }

    // Close dropdown
    $actions .= '</ul>';

    $rows[] = [
        $row['date_pawned'],
        htmlspecialchars($row['owner_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        '₱' . number_format($row['amount_pawned'], 2),
        htmlspecialchars($row['contact_no']),
        htmlspecialchars($row['notes']),
        $actions
    ];
}

echo json_encode(["data" => $rows]);
