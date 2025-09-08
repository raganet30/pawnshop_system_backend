<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php"; // updateCOH(), insertCashLedger(), logAudit()

header('Content-Type: application/json');

try {
    // --- Authentication ---
    if (!isset($_SESSION['user'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }

    $pawn_id = $_POST['pawn_id'] ?? null;
    $partial_amount = isset($_POST['partial_amount']) ? floatval($_POST['partial_amount']) : 0;
    $notes = $_POST['notes'] ?? "";
    $user_id = $_SESSION['user']['id'];
    $branch_id = $_SESSION['user']['branch_id'];

    if (!$pawn_id || $partial_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID or amount."]);
        exit;
    }

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawned item not found or already closed."]);
        exit;
    }

    $current_principal = floatval($pawn['amount_pawned']);

    // --- Get branch interest rate ---
    $stmt = $pdo->prepare("SELECT interest_rate FROM branches WHERE branch_id = ?");
    $stmt->execute([$branch_id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);
    $interest_rate = floatval($branch['interest_rate'] ?? 0.06); // decimal (not %)

    $today = new DateTime();

    // =======================
    // Fetch Tubo + Partial History
    // =======================

    // Last tubo
    $stmt = $pdo->prepare("SELECT new_due_date FROM tubo_payments WHERE pawn_id = ? ORDER BY date_paid DESC LIMIT 1");
    $stmt->execute([$pawn_id]);
    $lastTubo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Last partial
    $stmt = $pdo->prepare("SELECT created_at AS date_paid FROM partial_payments WHERE pawn_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$pawn_id]);
    $lastPartial = $stmt->fetch(PDO::FETCH_ASSOC);

    // =======================
    // Interest Calculation
    // =======================

    // --- Default ---
$interest_due = 0;
$startDate = new DateTime($pawn['date_pawned']);

// --- Check tubo payments ---
$stmtTubo = $pdo->prepare("SELECT * FROM tubo_payments WHERE pawn_id = ? ORDER BY date_paid DESC LIMIT 1");
$stmtTubo->execute([$pawn_id]);
$lastTubo = $stmtTubo->fetch(PDO::FETCH_ASSOC);

$today = new DateTime();

if ($lastTubo) {
    $tuboDue = new DateTime($lastTubo['new_due_date']);
    if ($tuboDue >= $today) {
        // Covered by tubo → waive interest
        $interest_due = 0;
    } else {
        $startDate = $tuboDue;
    }
}

// --- Check partial payments ---
$stmtPartial = $pdo->prepare("SELECT * FROM partial_payments WHERE pawn_id = ? ORDER BY created_at DESC LIMIT 1");
$stmtPartial->execute([$pawn_id]);
$lastPartial = $stmtPartial->fetch(PDO::FETCH_ASSOC);

if ($lastPartial) {
    $partialDate = new DateTime($lastPartial['created_at']);
    $daysSincePartial = $partialDate->diff($today)->days;

    if ($daysSincePartial < 31) {
        // Waive interest if partial was within 30 days
        $interest_due = 0;
        $startDate = $partialDate;
    } elseif ($partialDate > $startDate) {
        // Otherwise shift start date to partial
        $startDate = $partialDate;
    }
}

// --- Compute interest only if not waived ---
if ($interest_due === 0 && (!$lastTubo || $today > new DateTime($lastTubo['new_due_date'])) && (!$lastPartial || $today > (new DateTime($lastPartial['created_at']))->modify('+30 days'))) {
    $days_diff = $startDate->diff($today)->days;
    $months = max(1, ceil($days_diff / 31));
    $interest_due = round($current_principal * $interest_rate * $months, 2);
}


    // =======================
    // New Principal + Total Paid
    // =======================

    $new_principal = $current_principal - $partial_amount;
    if ($new_principal < 0) $new_principal = 0;

    $total_paid = $partial_amount + $interest_due;

    // =======================
    // Insert into partial_payments
    // =======================
    $stmt = $pdo->prepare("
        INSERT INTO partial_payments
        (pawn_id, branch_id, amount_paid, interest_paid, principal_paid, remaining_principal, user_id, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $pawn_id,
        $branch_id,
        $total_paid,
        $interest_due,
        $partial_amount,
        $new_principal,
        $user_id,
        $notes
    ]);
    $pp_id = $pdo->lastInsertId();

    // =======================
    // Update pawned_items
    // =======================
    $stmt = $pdo->prepare("UPDATE pawned_items SET amount_pawned = ?, has_partial_payments = 1, updated_by = ?, updated_at = NOW() WHERE pawn_id = ?");
    $stmt->execute([$new_principal, $user_id, $pawn_id]);

    // =======================
    // Update branch cash on hand
    // =======================
    updateCOH($pdo, $branch_id, $total_paid, 'add');

    // =======================
    // Ledger entry
    // =======================
    $ledgerNotes = "Partial payment: Principal ₱" . number_format($partial_amount, 2) . " | Interest ₱" . number_format($interest_due, 2);
    insertCashLedger(
        $pdo,
        $branch_id,
        "partial_payment",
        "in",
        $total_paid,
        "partial_payments",
        $pp_id,
        "Partial Payment (Pawn ID #$pawn_id)",
        $ledgerNotes,
        $user_id
    );

    // =======================
    // Audit log
    // =======================
    $log_desc = sprintf(
        "Partial payment recorded. Pawn ID: %d, Total Amount Paid: ₱%s (Partial ₱%s + Interest ₱%s), New Principal: ₱%s",
        $pawn_id,
        number_format($total_paid, 2),
        number_format($partial_amount, 2),
        number_format($interest_due, 2),
        number_format($new_principal, 2)
    );
    logAudit($pdo, $user_id, $branch_id, 'Partial Payment', $log_desc);

    echo json_encode([
        "status" => "success",
        "message" => "Partial payment of ₱" . number_format($partial_amount, 2) . " saved!<br>
                      Interest: ₱" . number_format($interest_due, 2) . "<br>
                      Remaining Principal: ₱" . number_format($new_principal, 2) . "<br>
                      Cash On Hand: +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id,
        "new_principal" => $new_principal
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
