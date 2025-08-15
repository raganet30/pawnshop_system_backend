<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';

/* =======================
   SUMMARY CARDS QUERIES
   ======================= */

// 1. Total Pawned Units
// $total_pawned_units = $pdo->query("SELECT COUNT(*) FROM pawned_items WHERE status = 'pawned'")->fetchColumn();

// 2. Total Value of Pawned Items
// $total_value_pawned = $pdo->query("SELECT COALESCE(SUM(amount_pawned),0) FROM pawned_items WHERE status = 'pawned'")->fetchColumn();

// Query to get total pawned units and total value
$pawned_stats = $pdo->query("
    SELECT 
        COUNT(*) AS total_units,
        COALESCE(SUM(amount_pawned), 0) AS total_value
    FROM pawned_items
    WHERE status = 'pawned'
")->fetch(PDO::FETCH_ASSOC);


// 3. Cash on Hand (Pawned Amounts + Claimed Interest)
$cash_on_hand = $pdo->query("
    SELECT 
        (SELECT COALESCE(SUM(amount_pawned),0) FROM pawned_items WHERE status = 'pawned') +
        (SELECT COALESCE(SUM(interest_amount),0) FROM pawned_items WHERE status = 'claimed')
")->fetchColumn();


// 4. Daily Interest Accumulated (Today's claimed interest)
$daily_interest = $pdo->query("
    SELECT COALESCE(SUM(interest_amount),0) FROM pawned_items 
    WHERE status = 'claimed' AND DATE(date_claimed) = CURDATE()
")->fetchColumn();

// 5. Daily Total Cash (Cash On Hand + Daily Interest)
$daily_total_cash = $cash_on_hand + $daily_interest;

// 6. Grand Total Interest Accumulated
$grand_total_interest = $pdo->query("SELECT COALESCE(SUM(interest_amount),0) FROM pawned_items WHERE status = 'claimed'")->fetchColumn();

// 7. Grand Total Cash (Grand Interest + Cash On Hand)
$grand_total_cash = $grand_total_interest + $cash_on_hand;

// 8. Forfeited Items Qty
$forfeited_qty = $pdo->query("SELECT COUNT(*) FROM pawned_items WHERE status = 'forfeited'")->fetchColumn();

/* =======================
   RECENT PAWNED ITEMS
   ======================= */
$recent_items = $pdo->query("
    SELECT date_pawned, owner_name, unit_description, category, amount_pawned, status 
    FROM pawned_items   where status = 'pawned'
    ORDER BY date_pawned DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Monthly trend: Pawned and Interest per month (last 12 months)
$trend_stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(date_pawned, '%Y-%m') AS month,
        COALESCE(SUM(amount_pawned), 0) AS total_pawned,
        COALESCE(SUM(interest_amount), 0) AS total_interest
    FROM pawned_items
    WHERE date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC
");

$trend_data = $trend_stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare arrays for Chart.js
$trend_months = [];
$trend_pawned = [];
$trend_interest = [];

foreach ($trend_data as $row) {
    $trend_months[] = date("M Y", strtotime($row['month'] . "-01"));
    $trend_pawned[] = (float)$row['total_pawned'];
    $trend_interest[] = (float)$row['total_interest'];
}


?>



<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
           <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Dashboard</h4>
            <div>
              <a href="#"><button class="btn btn-success" data-section="pawn-add">New Pawn</button></a>
              <button class="btn btn-outline-secondary" id="refreshBtn" >Refresh</button>
            </div>
          </div>

            <!-- Summary Cards -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Pawned Items</h5>
                            <p class="mb-1">Total Units: <strong><?= $pawned_stats['total_units'] ?> | </strong> Total Value: <strong>₱<?= number_format($pawned_stats['total_value'], 2) ?></strong></p>
                            <!-- <p class="mb-0"></p> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Cash on Hand</div><h3>₱<?= number_format($cash_on_hand, 2) ?></h3></div></div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Daily Interest Accumulated</div><h3>₱<?= number_format($daily_interest, 2) ?></h3></div></div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Daily Total Cash (COH+Intrest)</div><h3>₱<?= number_format($daily_total_cash, 2) ?></h3></div></div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Grand Total Interest</div><h3>₱<?= number_format($grand_total_interest, 2) ?></h3></div></div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Grand Total Cash</div><h3>₱<?= number_format($grand_total_cash, 2) ?></h3></div></div>
                <div class="col-md-3"><div class="card p-3 text-center"><div class="text-muted">Forfeited Items</div><h3><?= number_format($forfeited_qty) ?></h3></div></div>
            </div>

            <!-- Monthly Trend Chart -->
             <div class="card mb-4">
                <div class="card-header">Monthly Trends</div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="200"></canvas>
                </div>
            </div>

            
            <!-- Recent Pawned Items Table -->
            <div class="card">
                <div class="card-header">Recent Pawned Items</div>
                <div class="card-body">
                    <table id="pawnedItemsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Owner Name</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['date_pawned']) ?></td>
                                <td><?= htmlspecialchars($item['owner_name']) ?></td>
                                <td><?= htmlspecialchars($item['unit_description']) ?></td>
                                <td><?= htmlspecialchars($item['category']) ?></td>
                                <td>₱<?= number_format($item['amount_pawned'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $item['status'] == 'pawned' ? 'info' : 
                                        ($item['status'] == 'claimed' ? 'success' : 'secondary')
                                    ?>">
                                        <?= ucfirst($item['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<!-- Sidebar Toggle Script -->
<script>
    const wrapper = document.getElementById("wrapper");
    document.querySelectorAll("#sidebarToggle, #sidebarToggleTop").forEach(btn => {
        btn.addEventListener("click", () => {
            wrapper.classList.toggle("toggled");
        });
    });

    // DataTables init
    $(document).ready(function () {
        $('#pawnedItemsTable').DataTable();
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trend_months) ?>,
            datasets: [
                {
                    label: 'Pawned Items Value',
                    data: <?= json_encode($trend_pawned) ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Interest Earned',
                    data: <?= json_encode($trend_interest) ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>



