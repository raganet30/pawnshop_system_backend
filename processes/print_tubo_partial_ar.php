<?php
session_start();

// Capture query parameters (from JS)
$type = $_GET['type'] ?? 'Partial';
$receipt_no = $_GET['receipt_no'] ?? 'N/A';
$customer_name = $_GET['customer_name'] ?? '';
$item = $_GET['item'] ?? '';
$date_paid = $_GET['date_paid'] ?? date("Y-m-d");
$amount_pawned = floatval($_GET['amount_pawned'] ?? 0); // New field


// Partial fields
$partial_amount = floatval($_GET['partial_amount'] ?? 0);
$remaining_balance = floatval($_GET['remaining_balance'] ?? 0);

// Tubo fields
$tubo_amount = floatval($_GET['tubo_amount'] ?? 0);
$covered_from = $_GET['covered_from'] ?? '';
$covered_to = $_GET['covered_to'] ?? '';


// Session branch + cashier info
$branch_name = $_SESSION['user']['branch_name'] ?? "Branch Name";
$branch_address = $_SESSION['user']['branch_address'] ?? "Branch Address";
$branch_contact = $_SESSION['user']['branch_phone'] ?? "Contact No";
$cashier_name = $_SESSION['user']['full_name'] ?? "Cashier";
?>
<!DOCTYPE html>
<html>

<head>
  <title>Acknowledgment Receipt</title>
  <style>
    body {
      font-family: "Courier New", monospace;
      font-size: 12px;
      margin: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
    }

    td,
    th {
      border: none;
      padding: 3px 5px;
      vertical-align: top;
    }

    .right {
      text-align: right;
    }

    .center {
      text-align: center;
    }

    hr {
      border: none;
      border-top: 1px dashed #000;
      margin: 6px 0;
    }
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
  <div class="center"><b>ACKNOWLEDGMENT RECEIPT</b></div>
<br>

  <table>
    <tr>
      <td><b>ACK NO:</b> <?= htmlspecialchars($receipt_no) ?></td>
      <td><b>Date:</b> <?= date("m/d/Y", strtotime($date_paid)) ?></td>
    </tr>
    <tr>
      <td><b>Customer:</b> <?= htmlspecialchars($customer_name) ?></td>
      <td><b>Item:</b> <?= htmlspecialchars($item) ?></td>
    </tr>

    <tr>
      <td><b>Amount Pawned: </b>₱<?= number_format($amount_pawned, 2) ?> </td>
   
  </table>

  <hr>
  <table>
    <?php if ($partial_amount > 0): ?>
      <tr>
        <td><b>Partial Payment:</b></td>
        <td>₱<?= number_format($partial_amount, 2) ?></td>
      </tr>
      <tr>
        <td><b>Remaining Balance:</b></td>
        <td>₱<?= number_format($remaining_balance, 2) ?></td>
      </tr>
    <?php endif; ?>

    <?php if ($tubo_amount > 0): ?>
    
      <tr>
        <td>
          <b>Tubo Payment:</b></td>
        <td>₱<?= number_format($tubo_amount, 2) ?></td>
      </tr>
      <tr>
        <td><b>Covered Months</b></td>
        <td class="left">
          <?php if (!empty($_GET['covered_from']) && !empty($_GET['covered_to'])): ?>
            <?= date("m/d/Y", strtotime($_GET['covered_from'])) ?>
            to
            <?= date("m/d/Y", strtotime($_GET['covered_to'])) ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>

    <?php endif; ?>
  </table>

  <hr>
  <div class="center">
    <p style="margin:4px 0;">
      This acknowledges receipt of the above payment(s) from <b><?= htmlspecialchars($customer_name) ?></b>
      for the pawned item <b><?= htmlspecialchars($item) ?></b>.
    </p>
    <p style="margin:4px 0;">Not valid as Claim Receipt.</p>
  </div>

  <br><br>
  <table>
    <tr>
      <td class="center">
        _________________________<br>
        Customer Signature
      </td>
      <td class="center">
        _________________________<br>
        Cashier
      </td>
    </tr>
  </table>

  <small>Cashier: <?= htmlspecialchars($cashier_name) ?></small><br>
  <small>Printed on: <?= date("m/d/Y H:i") ?></small>

</body>

</html>