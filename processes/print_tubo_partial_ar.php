<?php
session_start();

// Capture query parameters (from JS)
$type              = $_GET['type'] ?? 'Partial';
$receipt_no        = $_GET['receipt_no'] ?? 'N/A';
$customer_name     = $_GET['customer_name'] ?? '';
$item              = $_GET['item'] ?? '';
$amount_paid       = floatval($_GET['amount_paid'] ?? 0);
$remaining_balance = floatval($_GET['remaining_balance'] ?? 0);
$date_paid         = $_GET['date_paid'] ?? date("Y-m-d");

// Session branch + cashier info
$branch_name    = $_SESSION['user']['branch_name'] ?? "Branch Name";
$branch_address = $_SESSION['user']['branch_address'] ?? "Branch Address";
$branch_contact = $_SESSION['user']['branch_phone'] ?? "Contact No";
$cashier_name   = $_SESSION['user']['full_name'] ?? "Cashier";
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
    td, th {
      border: none;
      padding: 3px 5px;
      vertical-align: top;
    }
    .right { text-align: right; }
    .center { text-align: center; }
    hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
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
  <hr>
  <table>
    <tr>
      <td><b>ACK NO:</b> <?= htmlspecialchars($receipt_no) ?></td>
      <td><b>Date:</b> <?= date("m/d/Y", strtotime($date_paid)) ?></td>
    </tr>
    <tr>
      <td><b>Customer:</b> <?= htmlspecialchars($customer_name) ?></td>
     <td colspan="2"><b>Item:</b> <?= htmlspecialchars($item) ?></td>
    </tr>
   
  </table>

  <hr>
  <table>
    <tr>
      <td><b>Payment Type:</b></td>
      <td><?= ucfirst($type) ?> Payment</td>
    </tr>
    <tr>
      <td><b>Amount Paid:</b></td>
      <td>₱<?= number_format($amount_paid, 2) ?></td>
    </tr>
    <tr>
      <td><b>Remaining Balance:</b></td>
      <td>₱<?= number_format($remaining_balance, 2) ?></td>
    </tr>
  </table>

  <hr>
  <div class="center">
    <p style="margin:4px 0;">This is to acknowledge receipt of the above payment.</p>
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

  <small>Printed on: <?= date("m/d/Y H:i") ?></small>

</body>
</html>
