<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$daysAhead = $_GET['days'] ?? 30; // default 30 days
$branch_id = $_SESSION['user']['branch_id'];
$role = $_SESSION['user']['role'] ?? "";

// Base query
$sql = "SELECT 
            p.pawn_id,
            p.date_pawned,
            c.full_name AS owner,
            c.contact_no,   
            p.unit_description AS item,
            p.category,
            p.amount_pawned,
            TIMESTAMPDIFF(MONTH, p.date_pawned, CURDATE()) AS months_period,
            p.current_due_date AS due_date,
            DATE_ADD(p.current_due_date, INTERVAL 1 MONTH) AS maturity_date,
            DATEDIFF(DATE_ADD(p.current_due_date, INTERVAL 1 MONTH), CURDATE()) AS days_before_maturity,
            CASE 
                WHEN DATE_ADD(p.current_due_date, INTERVAL 1 MONTH) < CURDATE() THEN 'Overdue'
                ELSE 'Active'
            END AS status,
            b.branch_name
        FROM pawned_items p
        LEFT JOIN customers c ON p.customer_id = c.customer_id
        LEFT JOIN branches b ON p.branch_id = b.branch_id
        WHERE p.status = 'pawned' AND p.is_deleted = 0 ";

// Add branch condition if not super admin
$params = [];
if ($role !== 'super_admin') {
    $sql .= " AND p.branch_id = ? ";
    $params[] = $branch_id;
}

// Add maturity filters (upcoming + overdue)
$sql .= " AND (
                DATE_ADD(p.current_due_date, INTERVAL 1 MONTH) 
                    BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                OR DATE_ADD(p.current_due_date, INTERVAL 1 MONTH) < CURDATE()
            )
        ORDER BY maturity_date ASC";

$params[] = $daysAhead;

// Execute
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format for DataTable
$data = [];
$counter = 1;
foreach ($result as $row) {
    $entry = [
        "#" => $counter++,
        "date_pawned" => $row['date_pawned'],
        "owner" => $row['owner'],
        "contact_no" => $row['contact_no'] ?? '',
        "item" => $row['item'],
        "category" => $row['category'],
        "amount_pawned" => number_format($row['amount_pawned'], 2),
        "months_period" => $row['months_period'],
        "due_date" => $row['due_date'],
        "maturity_date" => $row['maturity_date'],
        "days_before_maturity" => $row['days_before_maturity'],
        "status" => $row['status']
    ];

    //  Add branch column only if user is super_admin
    if ($role === 'super_admin') {
        $entry["branch"] = $row['branch_name'];
    }

    $data[] = $entry;
}

echo json_encode(["data" => $data]);
