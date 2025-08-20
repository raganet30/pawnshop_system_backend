<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';

// role restriction
if ($_SESSION['user']['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit();
}



$global_cash = $pdo->query("SELECT COALESCE(SUM(cash_on_hand),0) FROM branches")->fetchColumn();

/* =======================
   BRANCH STATS
   ======================= */
$branch_stats = $pdo->query("
    SELECT b.branch_id, b.branch_name,
           SUM(CASE WHEN p.status = 'pawned' THEN 1 ELSE 0 END) AS total_pawned,
           SUM(CASE WHEN p.status = 'claimed' THEN 1 ELSE 0 END) AS claimed,
           SUM(CASE WHEN p.status = 'forfeited' THEN 1 ELSE 0 END) AS forfeited,
           COALESCE(SUM(p.amount_pawned),0) AS total_pawned_value,
           COALESCE(SUM(p.interest_amount),0) AS total_interest_amount,
           b.cash_on_hand
    FROM branches b
    LEFT JOIN pawned_items p 
        ON b.branch_id = p.branch_id 
        AND p.is_deleted = 0
    GROUP BY b.branch_id, b.branch_name
")->fetchAll(PDO::FETCH_ASSOC);



/* =======================
   MONTHLY TREND (ALL BRANCHES)
   ======================= */
$trend_stmt = $pdo->query("
    SELECT DATE_FORMAT(date_pawned, '%Y-%m') AS month,
           COALESCE(SUM(amount_pawned), 0) AS total_pawned,
           COALESCE(SUM(interest_amount), 0) AS total_interest
    FROM pawned_items
    WHERE date_pawned >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC
");
$trend_data = $trend_stmt->fetchAll(PDO::FETCH_ASSOC);

$trend_months = [];
$trend_pawned = [];
$trend_interest = [];
foreach ($trend_data as $row) {
    $trend_months[] = date("M Y", strtotime($row['month'] . "-01"));
    $trend_pawned[] = (float) $row['total_pawned'];
    $trend_interest[] = (float) $row['total_interest'];
}

?>
<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Super Admin Dashboard</h4>
            </div>

           
               

            <!-- Branch Summary Table -->
            <div class="card mb-4">
                <div class="card-header">Branch Summary</div>
                <div class="card-body">
                    <table id="branchSummaryTable" class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Branch</th>
                                <th>Total Pawned Items</th>
                                <th>Claimed</th>
                                <th>Forfeited</th>
                                <th>Total Pawned Value</th>
                                <th>Cash on Hand</th>
                                <th>Total Interest Accumulated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand_pawned = $grand_claimed = $grand_forfeited = $grand_value = $grand_coh = $grand_interest_amount = 0;
                            foreach ($branch_stats as $branch):
                                $grand_pawned += $branch['total_pawned'];
                                $grand_claimed += $branch['claimed'];
                                $grand_forfeited += $branch['forfeited'];
                                $grand_value += $branch['total_pawned_value'];
                                $grand_coh += $branch['cash_on_hand'];
                                $grand_interest_amount += $branch['total_interest_amount'];
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($branch['branch_name']) ?></td>
                                    <td><?= number_format($branch['total_pawned']) ?></td>
                                    <td><?= number_format($branch['claimed']) ?></td>
                                    <td><?= number_format($branch['forfeited']) ?></td>
                                    <td>₱<?= number_format($branch['total_pawned_value'], 2) ?></td>
                                    <td>₱<?= number_format($branch['cash_on_hand'], 2) ?></td>
                                    <td>₱<?= number_format($branch['total_interest_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>Totals</th>
                                <th><?= number_format($grand_pawned) ?></th>
                                <th><?= number_format($grand_claimed) ?></th>
                                <th><?= number_format($grand_forfeited) ?></th>
                                <th>₱<?= number_format($grand_value, 2) ?></th>
                                <th>₱<?= number_format($grand_coh, 2) ?></th>
                                 <th>₱<?= number_format($grand_interest_amount, 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>



            <!-- Monthly Trend Chart -->
            <div class="card mb-4">
                <div class="card-header">Monthly Trends (All Branches)</div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="200"></canvas>
                </div>
            </div>

            <!-- Branch Comparison Chart -->
            <div class="card mb-4">
                <div class="card-header">Branch Comparison</div>
                <div class="card-body">
                    <canvas id="branchComparisonChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Monthly Trends Chart
        new Chart(document.getElementById('monthlyTrendsChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($trend_months) ?>,
                datasets: [
                    {
                        label: 'Pawned Value',
                        data: <?= json_encode($trend_pawned) ?>,
                        borderColor: 'rgba(54,162,235,1)',
                        backgroundColor: 'rgba(54,162,235,0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Interest Earned',
                        data: <?= json_encode($trend_interest) ?>,
                        borderColor: 'rgba(255,99,132,1)',
                        backgroundColor: 'rgba(255,99,132,0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            }
        });

        // Branch Comparison Chart
        new Chart(document.getElementById('branchComparisonChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($branch_stats, 'branch_name')) ?>,
                datasets: [
                    {
                        label: 'Pawned Value',
                        data: <?= json_encode(array_column($branch_stats, 'total_pawned_value')) ?>,
                        backgroundColor: 'rgba(54,162,235,0.7)'
                    },
                    {
                        label: 'Cash on Hand',
                        data: <?= json_encode(array_column($branch_stats, 'cash_on_hand')) ?>,
                        backgroundColor: 'rgba(75,192,192,0.7)'
                    },
                    {
                        label: 'Interest Earned',
                        data: <?= json_encode(array_column($branch_stats, 'total_interest_amount')) ?>,
                        backgroundColor: 'rgba(255,99,132,0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#branchSummaryTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true
        });
    });
</script>