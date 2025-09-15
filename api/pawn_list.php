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
$end_date = $_GET['end_date'] ?? null;

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
        p.current_due_date,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.interest_rate,
        p.original_amount_pawned,
        p.has_partial_payments,
        p.has_tubo_payments,
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



    // Build actions dropdown (only if user has access)
    $actions = '';
    if (in_array($user_role, ['admin', 'cashier'])) {
        $actions .= '
           <a href="#" class="text-secondary text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots fs-5"></i>
            </a>

           <a href="#" 
            class="text-info text-decoration-none viewPawnBtn" 
            data-id="' . $row['pawn_id'] . '">
            <i class="bi bi-eye"></i>
            </a>



           <a href="../processes/pawn_item_print.php?id=' . $row['pawn_id'] . '" 
            target="_blank" 
            class="text-primary text-decoration-none">
            <i class="bi bi-printer"></i>
            </a>

           

                  
            <ul class="dropdown-menu dropdown-menu-end">
        ';

        // Edit (admin only)
        if ($user_role === 'admin') {
            // Check if pawn has partials or tubo
            $isReadOnly = $row['has_partial_payments'] == 1 || $row['has_tubo_payments'] == 1;

            if ($isReadOnly) {
                // Show as read-only (disabled edit)
                $actions .= '
            <li>
                <a class="dropdown-item disabled text-muted" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-pencil-square"></i> Edit (Locked)
                </a>
            </li>
        ';
            } else {
                // Allow edit
                $actions .= '
            <li>
                <a class="dropdown-item editPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-pencil-square text-primary"></i> Edit
                </a>
            </li>
        ';
            }
        }


        // Claim (admin + cashier)
        $actions .= '
            <li>
                <a class="dropdown-item claimPawnBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-cash-coin text-success"></i> Claim
                </a>
            </li>

       
            <li>
                <a class="dropdown-item addPawnAmountBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-plus-circle text-success"></i> Add Amount
                </a>

            <li>
                <a class="dropdown-item addTuboPaymentBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-cash text-primary"></i> Tubo Payment
                </a>
            </li>

            <li>
                <a class="dropdown-item addPartialPaymentBtn" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-wallet2 text-info"></i> Partial Payment
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
    ';

            // Move to Trash only if no partial/tubo payments
            if ($row['has_partial_payments'] == 0 && $row['has_tubo_payments'] == 0) {
                $actions .= '
            <li>
                <a class="dropdown-item deletePawnBtn text-danger" href="#" data-id="' . $row['pawn_id'] . '">
                    <i class="bi bi-trash"></i> Move to Trash
                </a>
            </li>
        ';
            } else {
                $actions .= '
            <li>
                <a class="dropdown-item disabled text-muted" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-trash"></i> Move to Trash (Locked)
                </a>
            </li>
        ';
            }
        }


        $actions .= '</ul>';
    }



    // Build row for DataTable

    $totalPawned += floatval($row['amount_pawned']);

    $datePawned = new DateTime($row['date_pawned']);
    $today = new DateTime();
    $daysDiff = $datePawned->diff($today)->days;



  $today = new DateTime();
$unpaidMonths = 0;

// 1. Get latest tubo payment (if any)
$tuboStmt = $pdo->prepare("
    SELECT new_due_date, period_end 
    FROM tubo_payments 
    WHERE pawn_id = ? 
    ORDER BY new_due_date DESC 
    LIMIT 1
");
$tuboStmt->execute([$row['pawn_id']]);
$lastTuboRow = $tuboStmt->fetch(PDO::FETCH_ASSOC);

$lastTuboPeriodEnd = $lastTuboRow['period_end'] ?? null;

// 2. Determine effective start date
if ($row['has_partial_payments'] == 0 && $row['has_tubo_payments'] == 0) {
    // No payments at all → start from pawn date
    $startDate = new DateTime($row['date_pawned']);
} else {
    // With payments → start from end of last tubo coverage
    if (!empty($lastTuboPeriodEnd) && $lastTuboPeriodEnd != '0000-00-00') {
        $startDate = new DateTime($lastTuboPeriodEnd);
    } else {
        // fallback to current_due_date or pawn date
        $startDate = new DateTime($row['current_due_date'] ?? $row['date_pawned']);
    }
}

// 3. Calculate unpaid months
if ($today > $startDate) {
    $interval = $startDate->diff($today);
    $unpaidMonths = $interval->y * 12 + $interval->m;

    // Count partial month as full
    if ($interval->d > 0) {
        $unpaidMonths++;
    }

    // Ensure minimum 1 month if no payments
    if ($row['has_partial_payments'] == 0 && $row['has_tubo_payments'] == 0) {
        $unpaidMonths = max($unpaidMonths, 1);
    }
} else {
    // Still before start date
    $unpaidMonths = ($row['has_partial_payments'] == 0 && $row['has_tubo_payments'] == 0) ? 1 : 0;
}



    $rowData = [
        null,
        formatDateMDY($row['date_pawned']),
        // $months . ' month(s)', // <-- new row showing months since pawned
        $unpaidMonths . ' mo(s) unpaid', // Placeholder for Unpaid Months (to be computed client-side)
        htmlspecialchars($row['full_name']),
        htmlspecialchars($row['unit_description']),
        htmlspecialchars($row['category']),
        // $amountDisplay,
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
