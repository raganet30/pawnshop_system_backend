<?php
session_start();
require_once "../config/db.php";

if (!isset($_GET['pawn_id']) || !is_numeric($_GET['pawn_id'])) {
    die("Invalid Pawn ID");
}

$pawn_id = intval($_GET['pawn_id']);

// Fetch the latest claim for this pawn
$query = "
    SELECT 
        c.claim_id, c.date_claimed, c.months, c.interest_rate, c.interest_amount,
        c.principal_amount, c.penalty_amount, c.total_paid, c.notes,
        p.pawn_id, p.unit_description, p.category, p.amount_pawned, 
        p.date_pawned, p.original_amount_pawned,
        cust.full_name, cust.address, cust.contact_no
    FROM claims c
    INNER JOIN pawned_items p ON c.pawn_id = p.pawn_id
    INNER JOIN customers cust ON p.customer_id = cust.customer_id
    WHERE c.pawn_id = ?
    ORDER BY c.date_claimed DESC
    LIMIT 1
";

$stmt = $pdo->prepare($query);
$stmt->execute([$pawn_id]);
$claim = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$claim) {
    die("No claim found for this pawn record.");
}

// Fetch payment history (partial + tubo + final claim)
$history = [];
$payments = $pdo->query("
    SELECT created_at AS date_paid, amount_paid, interest_paid, principal_paid, 0 AS penalty, remaining_principal, 'Partial Payment' AS remarks
    FROM partial_payments WHERE pawn_id = {$pawn_id}
    UNION ALL
    SELECT date_paid, interest_amount, interest_amount, 0, 0, 0, 'Tubo Payment'
    FROM tubo_payments WHERE pawn_id = {$pawn_id}
    UNION ALL
    SELECT date_claimed, total_paid, interest_amount, principal_amount, penalty_amount, 0, 'Full Settlement'
    FROM claims WHERE pawn_id = {$pawn_id} AND claim_id = {$claim['claim_id']}
    ORDER BY date_paid ASC
");

while ($row = $payments->fetch(PDO::FETCH_ASSOC)) {
    $history[] = $row;
}

// Session branch + cashier info
$branch_name    = $_SESSION['user']['branch_name'] ?? "Branch Name";
$branch_address = $_SESSION['user']['branch_address'] ?? "Branch Address";
$branch_contact = $_SESSION['user']['branch_phone'] ?? "Contact No";
$cashier_name   = $_SESSION['user']['full_name'] ?? "Cashier";

?>
<!DOCTYPE html>
<html>
<head>
  <title>Pawn Claim Receipt</title>
  <style>
    body {
      font-family: "Courier New", monospace;
      font-size: 12px;
      margin: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
    }
    td, th {
      border: none;
      padding: 3px 5px;
      vertical-align: top;
    }
    .right { text-align: right; }
    .center { text-align: center; }
    hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
  </style>
</head>
<body onload="window.print()">

  <div class="center">
    <h3 style="margin:0;">LD GADGET PAWNSHOP</h3>
    <div><?= htmlspecialchars($branch_name) ?></div>
    <div><?= htmlspecialchars($branch_address) ?></div>
    <div>Cell: <?= htmlspecialchars($branch_contact) ?></div>
  </div>

  <hr>
  <table>
    <tr>
      <td><b>OR NO:</b> <?= $claim['claim_id'] ."-". date("mdy", strtotime($claim['date_pawned'])) ?>
</td>
      <td><b>Item:</b> <?= htmlspecialchars($claim['unit_description']) ?></td>
    </tr>
    <tr>
      <td><b>Customer:</b> <?= htmlspecialchars($claim['full_name']) ?></td>
      <td><b>Category:</b> <?= htmlspecialchars($claim['category']) ?></td>
    </tr>
    <tr>
      <td><b>Address:</b> <?= htmlspecialchars($claim['address']) ?></td>
      <td><b>Amount Pawned:</b> ₱<?= number_format($claim['original_amount_pawned'], 2) ?></td>
    </tr>
    <tr>
      <td><b>Date Pawned:</b> <?= date("m/d/Y", strtotime($claim['date_pawned'])) ?></td>
      <td><b>Interest Rate:</b> <?= $claim['interest_rate'] *100 ?>%</td>
    </tr>
    <tr>
      <td><b>Date Claimed:</b> <?= date("m/d/Y", strtotime($claim['date_claimed'])) ?></td>
    </tr>
  </table>

  <hr>
  <div class="section-title">Payment History</div>
  <table>
    <tr>
      <th>Date</th>
      <th class="right">Payment</th>
      <th class="right">Interest</th>
      <th class="right">Principal</th>
      <th class="right">Penalty</th>
      <th class="right">Balance</th>
      <th>Remarks</th>
    </tr>
    <?php foreach ($history as $h): ?>
      <tr>
        <td><?= date("m/d/Y", strtotime($h['date_paid'])) ?></td>
        <td class="right">₱<?= number_format($h['amount_paid'], 2) ?></td>
        <td class="right">₱<?= number_format($h['interest_paid'], 2) ?></td>
        <td class="right">₱<?= number_format($h['principal_paid'], 2) ?></td>
        <td class="right">₱<?= number_format($h['penalty'], 2) ?></td>
        <td class="right">₱<?= number_format($h['remaining_principal'], 2) ?></td>
        <td><?= $h['remarks'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <hr>
  <table>
    <tr><td><b>Amount Pawned:</b> ₱<?= number_format($claim['original_amount_pawned'], 2) ?></td></tr>
    <?php
      $total_interest_paid = 0;
      $total_interest_amount = 0;
      foreach ($history as $h) {
        if (isset($h['interest_paid'])) {
          $total_interest_paid += $h['interest_paid'];
        }
        elseif (isset($h['interest_amount'])) {
          $total_interest_amount += $h['interest_amount'];
        }
      }
    ?>
    <tr><td><b>Total Interest:</b> ₱<?= number_format($claim['interest_amount'] + $total_interest_paid + $total_interest_amount, 2) ?></td></tr>
    <tr><td><b>Total Penalty:</b> ₱<?= number_format($claim['penalty_amount'], 2) ?></td></tr>
    <tr><td><b>Total Paid:</b> ₱<?= number_format($claim['penalty_amount'] + $claim['interest_amount'] + $claim['original_amount_pawned'] + $total_interest_paid + $total_interest_amount, 2) ?></td></tr>
  </table>

  <small>Cashier: <?= htmlspecialchars($cashier_name) ?></small><br>
  <small>Printed on: <?= date("m/d/Y H:i") ?></small>

  <br>
  <div class="center">***** THANK YOU *****</div>

</body>
</html>
