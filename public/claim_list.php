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

// branch id is set in the session for branch-specific views
$branch_id = $_SESSION['user']['branch_id'] ?? 1; // Default to branch 1 if not set


// Fetch pawned items (only status = 'claimed')
$stmt = $pdo->query("
    SELECT *
    FROM pawned_items
    WHERE status = 'claimed'AND is_deleted = 0 AND branch_id = $branch_id
    ORDER BY date_pawned DESC
");

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
    if ($_SESSION['user']['role'] === 'super_admin') {
        $actions .= '
        <li>
            <a class="dropdown-item editPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                <i class="bi bi-pencil-square text-primary"></i> Edit
            </a>
        </li>
    ';
    }



    // Delete (super_admin only)
    // if ($user_role === 'super_admin') {
    //     $actions .= '
    //         <li><hr class="dropdown-divider"></li>
    //         <li>
    //             <a class="dropdown-item deletePawnBtn text-danger" href="#" data-id="' . $row['pawn_id'] . '">
    //                 <i class="bi bi-trash"></i> Delete
    //             </a>
    //         </li>
    //     ';
    // }

    // Close dropdown
    $actions .= '</ul></div>';

    $rows[] = [
        $row['date_pawned'],
        $row['date_claimed'] ?? 'N/A', // Handle null date_claimed
        htmlspecialchars($row['owner_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        '₱' . number_format($row['amount_pawned'], 2),
        '₱' . number_format($row['interest_amount'], 2),
        htmlspecialchars($row['contact_no']),
        htmlspecialchars($row['notes']),
        $actions
    ];
}

echo json_encode(["data" => $rows]);
