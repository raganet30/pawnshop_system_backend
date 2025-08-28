<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Print Table</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    tfoot td {
      font-weight: bold;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>

<button class="no-print" onclick="printTable()">Print</button>

<table id="reportTable">
  <thead>
    <tr>
      <th>Date</th>
      <th>Txn Type</th>
      <th>Direction</th>
      <th>Amount</th>
      <th>Reference</th>
      <th>Description</th>
      <th>User</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>2025-08-28 20:00:39</td>
      <td>Coh_adjustment</td>
      <td>IN</td>
      <td>₱5,000.00</td>
      <td>branches (#1)</td>
      <td>Set COH Adjustment</td>
      <td>admin_main</td>
    </tr>
    <tr>
      <td>2025-08-28 20:01:15</td>
      <td>Pawn</td>
      <td>OUT</td>
      <td>₱1,000.00</td>
      <td>pawned_items (#272)</td>
      <td>Pawn Add (ID #272)</td>
      <td>admin_main</td>
    </tr>
    <tr>
      <td>2025-08-28 20:01:43</td>
      <td>Pawn</td>
      <td>OUT</td>
      <td>₱1,500.00</td>
      <td>pawned_items (#273)</td>
      <td>Pawn Add (ID #273)</td>
      <td>admin_main</td>
    </tr>
    <tr>
      <td>2025-08-28 20:02:01</td>
      <td>Claim</td>
      <td>IN</td>
      <td>₱1,060.00</td>
      <td>claims (#272)</td>
      <td>Claim (ID #151)</td>
      <td>admin_main</td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" style="text-align:right;">TOTAL IN:</td>
      <td colspan="4" style="color:green;">₱6,060</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:right;">TOTAL OUT:</td>
      <td colspan="4" style="color:red;">₱2,500</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:right;">BALANCE:</td>
      <td colspan="4" style="color:blue;">₱3,560</td>
    </tr>
  </tfoot>
</table>

<script>
  function printTable() {
    window.print();
  }
</script>

</body>
</html>
