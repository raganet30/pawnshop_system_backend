<?php
require_once "../config/db.php";

try {
    $pdo->beginTransaction();

    $queries = [
        "DELETE FROM audit_logs",
        "DELETE FROM cash_ledger",
        "DELETE FROM claims",
        "DELETE FROM forfeitures",
        "DELETE FROM tubo_payments",
        "DELETE FROM partial_payments",
        "DELETE FROM pawned_items",
        "UPDATE branches SET cash_on_hand = 0",
        "DELETE FROM customers"
    ];

    foreach ($queries as $sql) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    $pdo->commit();
    
    // Return JSON instead of plain text
    echo json_encode(["success" => true, "message" => "All tables cleared successfully!"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
