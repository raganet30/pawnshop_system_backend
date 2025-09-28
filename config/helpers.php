<?php
// helpers.php


// function to log audit entries
function logAudit($pdo, $user_id, $branch_id, $action_type, $description) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, branch_id, action_type, description) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $branch_id, $action_type, $description]);
    } catch (Exception $e) {
        error_log("Audit Log Error: " . $e->getMessage());
        // don’t throw, so main transaction isn’t blocked by log failure
    }
}


// funtion to apply branch filtering for pawned items
// Returns SQL WHERE clause and binds parameters
// $role: user role (e.g., 'super_admin', 'branch_user')
// $branch_id: branch ID to filter by
// &$params: reference to array where parameters will be stored

function branchFilter($role, $branch_id, &$params) {
    if ($role === 'super_admin') {
        $params = []; // no filter for super admin
        return "WHERE status = 'pawned' AND is_deleted = 0";
    } else {
        $params = [$branch_id];
        return "WHERE status = 'pawned' AND is_deleted = 0 AND branch_id = ?";
    }
}


function insertCashLedger($pdo, $branch_id, $txn_type, $direction, $amount, $ref_table, $ref_id, $description, $notes, $user_id) {
    $stmt = $pdo->prepare("
        INSERT INTO cash_ledger 
            (branch_id, txn_type, direction, amount, ref_table, ref_id, description, notes, created_at, user_id)
        VALUES 
            (:branch_id, :txn_type, :direction, :amount, :ref_table, :ref_id, :description, :notes, NOW(), :user_id)
    ");
    $stmt->execute([
        'branch_id'   => $branch_id,
        'txn_type'    => $txn_type,    // e.g. 'pawn'
        'direction'   => $direction,   // 'out' or 'in'
        'amount'      => $amount,
        'ref_table'   => $ref_table,   // e.g. 'pawned_items'
        'ref_id'      => $ref_id,
        'description' => $description, // human readable e.g. "Pawn Add (ID #100)"
        'notes'       => $notes,       // details e.g. "iPhone 12 128GB"
        'user_id'     => $user_id
    ]);
}


// function to adjust branch cash on hand
/**
 * Update Cash on Hand (COH) for a branch
 *
 * @param PDO    $pdo        Database connection
 * @param int    $branch_id  Branch ID
 * @param float  $amount     Amount to update
 * @param string $operation  "add" or "subtract"
 *
 * @return bool True on success, false on failure
 */
function updateCOH(PDO $pdo, int $branch_id, float $amount, string $operation = 'add'): bool {
    if ($amount <= 0) return false; // prevent negative or zero updates

    // Determine SQL operation
    if ($operation === 'subtract') {
        $sql = "UPDATE branches SET cash_on_hand = cash_on_hand - ? WHERE branch_id = ?";
    } else {
        $sql = "UPDATE branches SET cash_on_hand = cash_on_hand + ? WHERE branch_id = ?";
    }

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$amount, $branch_id]);
}



function formatDateMDY($date) {
    if (!$date || $date === "0000-00-00") return "";
    return date('m-d-Y', strtotime($date));
}



function getInterestRate(PDO $pdo, int $branch_id, string $item_type = "default"): float {
    $stmt = $pdo->prepare("SELECT interest_rate, custom_interest_rate1 FROM branches WHERE branch_id = ?");
    $stmt->execute([$branch_id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        return 0.06; // fallback default 6%
    }

    // Normalize input
    // $item_type = strtolower(trim($item_type));

    switch ($item_type) {
        case "Motorcycle": // match your <select> option
            return floatval($branch['custom_interest_rate1'] ?? $branch['interest_rate']);
        default:
            return floatval($branch['interest_rate']);
    }
}


// functions to get the receipt header from settings table
function getReceiptHeader($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT shop_name, fb_page_name FROM settings LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                "shop_name"   => $row['shop_name'] ?? "LD Gadget Pawnshop",
                "fb_page_name"=> $row['fb_page_name'] ?? "LD Gadget Pawnshop"
            ];
        } else {
            // fallback if no settings row
            return [
                "shop_name"   => "LD Gadget Pawnshop",
                "fb_page_name"=> "LD Gadget Pawnshop"
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching receipt header: " . $e->getMessage());
        return [
            "shop_name"   => "LD Gadget Pawnshop",
            "fb_page_name"=> "LD Gadget Pawnshop"
        ];
    }
}



// low cash alert threshold

