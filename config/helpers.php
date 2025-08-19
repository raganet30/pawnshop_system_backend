<?php
// helpers.php

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
