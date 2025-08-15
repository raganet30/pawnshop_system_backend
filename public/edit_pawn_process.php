<?php

require_once "../config/db.php";
session_start();

// Allow only logged-in users
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}


// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pawn_id            = $_POST['pawn_id'] ?? null;
    $date_pawned        = $_POST['date_pawned'] ?? '';
    $owner_name         = $_POST['owner_name'] ?? '';
    $contact_no         = $_POST['contact_no'] ?? '';
    $unit_description   = $_POST['unit_description'] ?? '';
    $category           = $_POST['category'] ?? '';
    $amount_pawned      = $_POST['amount_pawned'] ?? '';
    $notes             = $_POST['notes'] ?? '';

    if (!$pawn_id) {
        echo json_encode(['status' => 'error', 'message' => 'Pawn ID is required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE pawned_items
            SET date_pawned = ?,
                owner_name = ?,
                contact_no = ?,
                unit_description = ?,
                category = ?,
                amount_pawned = ?,
                notes = ?
            WHERE pawn_id = ?
        ");
        $stmt->execute([
            $date_pawned,
            $owner_name,
            $contact_no,
            $unit_description,
            $category,
            $amount_pawned,
            $notes,
            $pawn_id
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Pawn item updated successfully']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
