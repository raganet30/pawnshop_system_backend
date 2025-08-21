<?php
session_start();
require_once "../config/db.php";

// Make sure user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

// Get search term if any
$term = trim($_GET['search'] ?? '');

try {
    if ($term === '') {
        // No search term: return all active customers
        $stmt = $pdo->prepare("SELECT customer_id, full_name, contact_no, address 
                               FROM customers 
                               ORDER BY full_name ASC");
        $stmt->execute();
    } else {
        // Search by term (partial match)
        $stmt = $pdo->prepare("SELECT customer_id, full_name, contact_no, address 
                               FROM customers 
                               WHERE full_name LIKE ? 
                               ORDER BY full_name ASC");
        $stmt->execute(["%$term%"]);
    }

    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON
    echo json_encode($customers);

} catch (Exception $e) {
    echo json_encode([]);
}
