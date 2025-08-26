<?php 
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

try {
    $pdo->beginTransaction();

    $branch_id  = $_SESSION['user']['branch_id'];
    $user_id    = $_SESSION['user']['id'];
    $full_name  = $_SESSION['user']['name'];

    // Pawn item fields
    $unit_description = $_POST['unit_description'] ?? '';
    $category         = $_POST['category'] ?? '';
    $amount_pawned    = (float) ($_POST['amount_pawned'] ?? 0);
    $notes            = $_POST['notes'] ?? null;
    $date_pawned      = $_POST['date_pawned'] ?? date("Y-m-d");

    // --- 1. Lock COH row ---
    $stmt = $pdo->prepare("SELECT cash_on_hand FROM branches WHERE branch_id = ? FOR UPDATE");
    $stmt->execute([$branch_id]);
    $current_cash = $stmt->fetchColumn();

    if ($current_cash === false) {
        throw new Exception("Branch cash record not found.");
    }

    if ($current_cash < $amount_pawned) {
        throw new Exception("Insufficient cash on hand. Available: ₱" . number_format($current_cash, 2));
    }

    // --- 2. Handle Customer ---
    $customer_id   = $_POST['customer_id'] ?? null; // from Select2
    $customer_name = trim($_POST['customer_name'] ?? '');
    $contact_no    = trim($_POST['contact_no'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    if (!empty($customer_id) && is_numeric($customer_id)) {
        // Existing customer selected
        $customer_id = (int) $customer_id;
    } else {
        // New customer: ensure name is provided
        if (empty($customer_name)) {
            throw new Exception("Customer name is required for new customer.");
        }

        // Check if customer with same name + contact already exists
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE full_name = ? AND contact_no = ? LIMIT 1");
        $stmt->execute([$customer_name, $contact_no]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $customer_id = $customer['customer_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO customers (full_name, contact_no, address, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$customer_name, $contact_no, $address]);
            $customer_id = $pdo->lastInsertId();
        }
    }

    // --- 3. Insert pawn item ---
    $stmt = $pdo->prepare("INSERT INTO pawned_items 
        (branch_id, customer_id, unit_description, category, amount_pawned, notes, date_pawned, created_by, status, is_deleted) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pawned', 0)");
    $stmt->execute([
        $branch_id,
        $customer_id,
        $unit_description,
        $category,
        $amount_pawned,
        $notes,
        $date_pawned,
        $user_id
    ]);

    // --- 4. Deduct COH ---
    $updateCash = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?");
    $updateCash->execute([$amount_pawned, $branch_id]);



    // insert into cash_ledger
    

    // --- 5. Log Audit ---
    $customerLabel = !empty($customer_name) ? $customer_name : "Customer #$customer_id";
    $description = sprintf(
        "%s added a new pawn item for %s (Unit: %s, Category: %s, Amount: ₱%s)",
        $full_name,
        $customerLabel,
        $unit_description,
        $category,
        number_format($amount_pawned, 2)
    );

    logAudit($pdo, $user_id, $branch_id, 'Add Pawned Item', $description);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Pawn item added successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
