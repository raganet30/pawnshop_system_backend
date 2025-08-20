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


// funtion to apply branch filtering

function branchFilter($role, $branch_id, &$params) {
    if ($role === 'super_admin') {
        $params = []; // no filter for super admin
        return "WHERE status = 'pawned' AND is_deleted = 0";
    } else {
        $params = [$branch_id];
        return "WHERE status = 'pawned' AND is_deleted = 0 AND branch_id = ?";
    }
}


