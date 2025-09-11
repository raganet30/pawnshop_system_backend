<?php
session_start();
require_once "../config/db.php";

// --- Validate ---
if (!isset($_GET['pawn_id']) || !is_numeric($_GET['pawn_id'])) {
    die("Invalid request.");
}
$pawn_id = intval($_GET['pawn_id']);

// --- Pawn + Customer ---
$sql = "
    SELECT 
        p.*,
        c.full_name, c.contact_no, c.address
    FROM pawned_items p
    JOIN customers c ON p.customer_id = c.customer_id
    WHERE p.pawn_id = ?
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pawn_id]);
$pawn = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pawn) die("Pawn record not found.");

// --- Claim record (latest if multiple) ---
$sqlClaim = "
    SELECT *
    FROM claims
    WHERE pawn_id = ?
    ORDER BY date_claimed DESC
    LIMIT 1
";
$stmtClaim = $pdo->prepare($sqlClaim);
$stmtClaim->execute([$pawn_id]);
$claim = $stmtClaim->fetch(PDO::FETCH_ASSOC);

// --- Tubo history ---
$sqlTubo = "
    SELECT *
    FROM tubo_payments
    WHERE pawn_id = ?
    ORDER BY date_paid ASC
";
$stmtTubo = $pdo->prepare($sqlTubo);
$stmtTubo->execute([$pawn_id]);
$tuboHistory = $stmtTubo->fetchAll(PDO::FETCH_ASSOC);

// --- Partial history ---
$sqlPartial = "
    SELECT *
    FROM partial_payments
    WHERE pawn_id = ?
    ORDER BY created_at ASC
";
$stmtPartial = $pdo->prepare($sqlPartial);
$stmtPartial->execute([$pawn_id]);
$partialHistory = $stmtPartial->fetchAll(PDO::FETCH_ASSOC);

// --- Totals ---
$totalTubo = 0;
foreach ($tuboHistory as $tubo) {
    $totalTubo += $tubo['interest_amount'];
}

$totalPartial = 0;
$totalInterestPaid = 0;
$totalPrincipalPaid = 0;
$remainingPrincipal = $pawn['amount_pawned'];
foreach ($partialHistory as $pp) {
    $totalPartial += $pp['amount_paid'];
    $totalInterestPaid += $pp['interest_paid'];
    $totalPrincipalPaid += $pp['principal_paid'];
    $remainingPrincipal = $pp['remaining_principal']; // last recorded
}

$grandTotal = $totalTubo + $totalPartial;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pawn Claim Receipt</title>
    <style>
        body { font-family: monospace; font-size: 12px; }
        .receipt { width: 280px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 4px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="center">
            <h3>Pawnshop Claim Receipt</h3>
        </div>
        <div class="line"></div>
        <strong>Customer:</strong> <?= htmlspecialchars($pawn['full_name']) ?><br>
        <strong>Contact:</strong> <?= htmlspecialchars($pawn['contact_no']) ?><br>
        <strong>Address:</strong> <?= htmlspecialchars($pawn['address']) ?><br>
        <div class="line"></div>
        <strong>Item:</strong> <?= htmlspecialchars($pawn['unit_description']) ?><br>
        <strong>Category:</strong> <?= htmlspecialchars($pawn['category']) ?><br>
        <strong>Amount Pawned:</strong> ₱<?= number_format($pawn['amount_pawned'], 2) ?><br>
        <strong>Date Pawned:</strong> <?= htmlspecialchars($pawn['date_pawned']) ?><br>
        <strong>Due Date:</strong> <?= htmlspecialchars($pawn['current_due_date']) ?><br>
        <div class="line"></div>

        <?php if ($claim): ?>
        <strong>Claim Info:</strong><br>
        Date Claimed: <?= $claim['date_claimed'] ?><br>
        Months: <?= $claim['months'] ?><br>
        Interest: ₱<?= number_format($claim['interest_amount'], 2) ?><br>
        Principal: ₱<?= number_format($claim['principal_amount'], 2) ?><br>
        Penalty: ₱<?= number_format($claim['penalty_amount'], 2) ?><br>
        Total Paid: ₱<?= number_format($claim['total_paid'], 2) ?><br>
        <div class="line"></div>
        <?php endif; ?>

        <strong>Tubo Payments:</strong><br>
        <?php if ($tuboHistory): ?>
            <?php foreach ($tuboHistory as $t): ?>
                <?= $t['date_paid'] ?> — ₱<?= number_format($t['interest_amount'], 2) ?> (New Due: <?= $t['new_due_date'] ?>)<br>
            <?php endforeach; ?>
        <?php else: ?>None<br><?php endif; ?>
        <div class="line"></div>

        <strong>Partial Payments:</strong><br>
        <?php if ($partialHistory): ?>
            <?php foreach ($partialHistory as $pp): ?>
                <?= $pp['created_at'] ?> — ₱<?= number_format($pp['amount_paid'], 2) ?> 
                (Interest: ₱<?= number_format($pp['interest_paid'], 2) ?>, Principal: ₱<?= number_format($pp['principal_paid'], 2) ?>)<br>
            <?php endforeach; ?>
        <?php else: ?>None<br><?php endif; ?>
        <div class="line"></div>

        <strong>Totals:</strong><br>
        Total Tubo Paid: ₱<?= number_format($totalTubo, 2) ?><br>
        Total Partial Paid: ₱<?= number_format($totalPartial, 2) ?><br>
        Total Interest Paid: ₱<?= number_format($totalInterestPaid, 2) ?><br>
        Total Principal Paid: ₱<?= number_format($totalPrincipalPaid, 2) ?><br>
        Remaining Principal: ₱<?= number_format($remainingPrincipal, 2) ?><br>
        <div class="line"></div>
        <strong>Grand Total Paid: ₱<?= number_format($grandTotal, 2) ?></strong><br>

        <div class="line"></div>
        <div class="center">*** Thank you! ***</div>
    </div>
</body>
</html>
