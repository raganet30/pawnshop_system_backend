<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(["data" => []]);
    exit();
}

$user_role = $_SESSION['user']['role'] ?? 'cashier';

// Fetch pawned items (only status = 'pawned')
$stmt = $pdo->query("
    SELECT *
    FROM pawned_items
    WHERE status = 'pawned'
    ORDER BY date_pawned DESC
");

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Start dropdown
    $actions = '
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
    ';

    // Only super_admin can see Edit
    if ($_SESSION['user']['role'] === 'super_admin') {
        $actions .= '
        <li>
            <a class="dropdown-item editPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                <i class="bi bi-pencil-square text-primary"></i> Edit
            </a>
        </li>
    ';
    }


    // Claim (all roles, only if status = pawned — already filtered)
    $actions .= '
        <li>
            <a class="dropdown-item claimPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                <i class="bi bi-cash-coin text-success"></i> Claim
            </a>
        </li>
    ';

    // Forfeit (admin + super_admin only)
    if (in_array($user_role, ['admin', 'super_admin'])) {
        $actions .= '
            <li>
                <a class="dropdown-item forfeitPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Forfeit
                </a>
            </li>
        ';
    }

    // Delete (super_admin only)
    if ($user_role === 'super_admin') {
        $actions .= '
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item deletePawnBtn text-danger" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-trash"></i> Delete
                </a>
            </li>
        ';
    }

    // Close dropdown
    $actions .= '</ul></div>';

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
