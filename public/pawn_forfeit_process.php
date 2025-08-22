<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');

// --- Auth ---
if (!isset($_SESSION['user'])) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$user = $_SESSION['user'];
// print_r($_SESSION['user']);



if (!in_array($user['role'], ['admin'])) {
    echo json_encode(["status"=>"error","message"=>"Access denied: forfeit is admin/super admin only."]);
    exit;
}

$pawn_id = isset($_POST['pawn_id']) ? (int)$_POST['pawn_id'] : 0;
$reason  = trim($_POST['forfeitReason'] ?? '');
if ($pawn_id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Pawn ID is required."]);
    exit;
}

// if ($reason === '') {
//     echo json_encode(["status"=>"error","message"=>"Please provide a reason for forfeiture."]);
//     exit;
// }

try {
    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? LIMIT 1");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status"=>"error","message"=>"Pawn record not found for ID: $pawn_id"]);
        exit;
    }

    // --- Status check ---
    if (strtolower($pawn['status']) !== 'pawned') {
        echo json_encode(["status"=>"error","message"=>"Only items with status 'pawned' can be forfeited. Current status: {$pawn['status']}"]);
        exit;
    }

    // --- Calculate months pawned ---
    $datePawned = new DateTime($pawn['date_pawned']);
    $now = new DateTime();
    $months = max(1, ceil($datePawned->diff($now)->days / 30));

    // --- Restriction: 2-month minimum ---
    if ($months < 2) {
        echo json_encode(["status"=>"error","message"=>"Pawned item can only be forfeited after 2 months."]);
        exit;
    }

    $amount_pawned = (float)$pawn['amount_pawned'];
    $branch_id     = (int)$pawn['branch_id'];
    $user_id       = (int)$user['id'];

    // --- Begin transaction ---
    $pdo->beginTransaction();

    // --- 1) Insert into forfeitures table ---
    $stmt = $pdo->prepare("
        INSERT INTO forfeitures (pawn_id, branch_id, date_forfeited, reason, notes, created_at)
        VALUES (?, ?, NOW(), ?, ?, NOW())
    ");
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $reason,
        $pawn['notes'] ?? ""
    ]);

    // --- 2) Update pawned_items status only ---
    $stmt = $pdo->prepare("UPDATE pawned_items SET status='forfeited' WHERE pawn_id=?");
    $stmt->execute([$pawn_id]);

    // --- 3) Update branch cash on hand ---
    $stmt = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand + ? WHERE branch_id = ?");
    $stmt->execute([$amount_pawned, $branch_id]);

    // --- 4) Insert into cash ledger ---
    $stmt = $pdo->prepare("
        INSERT INTO cash_ledger (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id, created_at)
        VALUES (?, 'forfeit', 'in', ?, 'forfeitures', LAST_INSERT_ID(), 'Pawn forfeited', ?, NOW())
    ");
    $stmt->execute([$branch_id, $amount_pawned, $user_id]);

    // --- 5) Insert into audit logs ---
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action_type, description, branch_id, created_at)
        VALUES (?, 'forfeit', ?, ?, NOW())
    ");

    
    $stmt->execute([
        $user_id,
        "Pawn ID {$pawn_id} forfeited. Reason: {$reason}",
        $branch_id
    ]);

    // --- Commit transaction ---
    $pdo->commit();

    echo json_encode([
        "status"  => "success",
        "message" => "Pawn forfeited. Cash on Hand +₱" . number_format($amount_pawned, 2)
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status"=>"error","message"=>"Transaction failed: ".$e->getMessage()]);
}
