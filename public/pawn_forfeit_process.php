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
if (!in_array($user['role'], ['admin','super_admin'])) {
    echo json_encode(["status"=>"error","message"=>"Access denied: forfeit is admin/super admin only."]);
    exit;
}

$pawn_id = isset($_POST['pawn_id']) ? (int)$_POST['pawn_id'] : 0;
if ($pawn_id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Pawn ID is required."]);
    exit;
}

try {
    // 1) Fetch by pawn_id only (no status restriction yet)
    $stmt = $pdo->prepare("SELECT pawn_id, branch_id, status, amount_pawned FROM pawned_items WHERE pawn_id = ? AND is_deleted = 0 LIMIT 1");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status"=>"error","message"=>"Pawn record not found for ID: $pawn_id"]);
        exit;
    }

    // 2) Optional: Enforce branch scoping (recommended)
    if ((int)$pawn['branch_id'] !== (int)$user['branch_id']) {
        echo json_encode(["status"=>"error","message"=>"This item belongs to a different branch."]);
        exit;
    }

    // 3) Status check with a clear message
    $status = strtolower(trim($pawn['status'])); // normalize just in case
    if ($status !== 'pawned') {
        echo json_encode([
            "status"=>"error",
            "message"=>"Only items with status 'pawned' can be forfeited. Current status: {$pawn['status']}."
        ]);
        exit;
    }

    $amount_pawned = (float)$pawn['amount_pawned'];
    $branch_id     = (int)$pawn['branch_id'];
    // $user_id       = (int)$user['user_id'];
    $user_id   = $_SESSION['user']['id'];

    // 4) Transaction: update item, update COH, ledger
    $pdo->beginTransaction();

    // If you have a date_forfeited column, uncomment the next line and comment the following one.
    // $upd = $pdo->prepare("UPDATE pawned_items SET status='forfeited', date_forfeited=NOW() WHERE pawn_id=?");
    $upd = $pdo->prepare("UPDATE pawned_items SET status='forfeited' WHERE pawn_id=?");
    $upd->execute([$pawn_id]);

    $updCoh = $pdo->prepare("UPDATE branches SET cash_on_hand = cash_on_hand + ? WHERE branch_id = ?");
    $updCoh->execute([$amount_pawned, $branch_id]);

    $insLedger = $pdo->prepare("
        INSERT INTO cash_ledger (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id, created_at)
        VALUES (?, 'forfeit', 'in', ?, 'pawned_items', ?, 'Pawn forfeited', ?, NOW())
    ");
    $insLedger->execute([$branch_id, $amount_pawned, $pawn_id, $user_id]);

    $pdo->commit();

    echo json_encode([
        "status"  => "success",
        "message" => "Pawn forfeited. Cash on Hand +₱" . number_format($amount_pawned, 2)
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["status"=>"error","message"=>"Transaction failed: ".$e->getMessage()]);
}
