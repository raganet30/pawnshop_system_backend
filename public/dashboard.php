<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);

// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);




// role restriction: only admin/cashier allowed here
if ($_SESSION['user']['role'] == 'super_admin') {
    header("Location: ../public/dashboard_super.php");
    exit();
}
?>

<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Dashboard</h4>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-primary border-4">
                        <i class="bi bi-box-seam card-icon text-primary"></i>
                        <div class="card-label">Pawned Items</div>
                        <h3 id="pawnedUnits" class="card-value">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-success border-4">
                        <i class="bi bi-check-circle card-icon text-success"></i>
                        <div class="card-label">Claimed Items</div>
                        <h3 id="claimedItems" class="card-value">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-danger border-4">
                        <i class="bi bi-exclamation-triangle card-icon text-danger"></i>
                        <div class="card-label">Forfeited Items</div>
                        <h3 id="forfeitedItems" class="card-value">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-warning border-4">
                        <i class="bi bi-cash-coin card-icon text-warning"></i>
                        <div class="card-label">Pawned Items Total Value</div>
                        <h3 id="pawnedValue" class="card-value">₱0.00</h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-info border-4">
                        <i class="bi bi-wallet2 card-icon text-info"></i>
                        <div class="card-label">Cash on Hand</div>
                        <h3 id="cashOnHand" class="card-value">₱0.00</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-secondary border-4">
                        <i class="bi bi-graph-up card-icon text-secondary"></i>
                        <div class="card-label">Daily Income</div>
                        <h3 id="dailyInterest" class="card-value">₱0.00</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card p-3 text-center shadow-sm border-start border-dark border-4">
                        <i class="bi bi-bar-chart-line card-icon text-dark"></i>
                        <div class="card-label">Grand Total Income</div>
                        <h3 id="grandTotalInterest" class="card-value">₱0.00</h3>
                    </div>
                </div>
            </div>


            <!-- Monthly Trend Chart -->
            <div class="card mb-4">
                <div class="card-header">Monthly Trends</div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="200"></canvas>
                </div>
            </div>

            <!-- Upcoming Due Items -->
            <div class="card">
                <div class="card-header">Upcoming Due Items</div>
                <div class="card-body">
                    <table id="upcomingDueItemsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Pawned</th>
                                <th>Owner</th>
                                <th>Item</th>
                                <th>Category</th>   
                                <th>Amount Pawned</th>
                                <th>Months Period</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>


            <!-- Recent Pawned Items Table -->
            <!-- <div class="card">
                <div class="card-header">Recent Pawn Transactions</div>
                <div class="card-body">
                    <table id="pawnedItemsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Customer</th>
                                <th>Item Description</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div> -->


            
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>



<script>
    // Animate numbers smoothly
    function animateValue(id, start, end, duration, prefix = "", decimals = 0) {
        let obj = document.getElementById(id);
        let range = end - start;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let value = start + progress * range;

            // Format number
            let formattedValue = prefix + value.toLocaleString(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });

            obj.innerText = formattedValue;

            if (progress < 1) window.requestAnimationFrame(step);
        }
        window.requestAnimationFrame(step);
    }

    // Load Dashboard Stats
    function loadDashboardStats() {
        $.ajax({
            url: "../api/dashboard_stats.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                // Pawned Units
                animateValue("pawnedUnits",
                    parseInt($("#pawnedUnits").text().replace(/,/g, '')) || 0,
                    data.pawned_units, 600);

                // Pawned Value
                animateValue("pawnedValue",
                    parseFloat($("#pawnedValue").text().replace(/[^0-9.-]+/g, "")) || 0,
                    data.pawned_value, 600, "₱", 2);
                    
                // Cash on Hand
                animateValue("cashOnHand",
                    parseFloat($("#cashOnHand").text().replace(/[^0-9.-]+/g, "")) || 0,
                    data.cash_on_hand, 600, "₱", 2);

                // Claimed Items
                animateValue("claimedItems",
                    parseInt($("#claimedItems").text().replace(/,/g, '')) || 0,
                    data.claimed_qty, 600);

                // Forfeited Items
                animateValue("forfeitedItems",
                    parseInt($("#forfeitedItems").text().replace(/,/g, '')) || 0,
                    data.forfeited_qty, 600);

                // Daily Interest
                animateValue("dailyInterest",
                    parseFloat($("#dailyInterest").text().replace(/[^0-9.-]+/g, "")) || 0,
                    data.daily_interest, 600, "₱", 2);

                // Grand Total Interest
                animateValue("grandTotalInterest",
                    parseFloat($("#grandTotalInterest").text().replace(/[^0-9.-]+/g, "")) || 0,
                    data.grand_total_interest, 600, "₱", 2);
            },
            error: function () {
                console.error("Failed to load dashboard stats.");
            }
        });
    }

    // Load Recent Items + Trends
    function loadDashboardData() {
        $.ajax({
            url: "../api/dashboard_data.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                /* =====================
                   Fill Recent Items Table
                   ===================== */
                const table = $("#pawnedItemsTable").DataTable();
                table.clear();

                data.recent_items.forEach(item => {
                    let statusBadge =
                        item.status === "pawned"
                            ? `<span class="badge bg-info">Pawned</span>`
                            : item.status === "claimed"
                                ? `<span class="badge bg-success">Claimed</span>`
                                : `<span class="badge bg-secondary">${item.status}</span>`;

                    table.row.add([
                        item.date_pawned,
                        item.owner_name,
                        item.unit_description,
                        item.category,
                        "₱" + parseFloat(item.original_amount_pawned).toLocaleString(undefined, { minimumFractionDigits: 2 }),
                        statusBadge
                    ]);
                });

                table.draw();

                /* =====================
                   Update Monthly Trends
                   ===================== */
                const months = data.trend_data.map(row => row.month);
                const pawned = data.trend_data.map(row => parseFloat(row.total_pawned));
                const income = data.trend_data.map(row => parseFloat(row.total_income));

                monthlyTrendsChart.data.labels = months.map(m => {
                    const d = new Date(m + "-01");
                    return d.toLocaleString("default", { month: "short", year: "numeric" });
                });

                monthlyTrendsChart.data.datasets[0].data = pawned;
                monthlyTrendsChart.data.datasets[1].data = income;

                // 🔑 Force resize fix
                monthlyTrendsChart.resize();
                monthlyTrendsChart.update();
            },
            error: function () {
                console.error("Failed to load dashboard data.");
            }
        });
    }

    $(document).ready(function () {
        $("#pawnedItemsTable").DataTable();

        loadDashboardStats();
        loadDashboardData();

        // Auto-refresh every 5 min.
        setInterval(() => {
            loadDashboardStats();
            loadDashboardData();
        }, 300000);
    });

    /* =====================
       Chart.js Instance
       ===================== */
    let ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
    let monthlyTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Pawned Items Value',
                    data: [],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Income',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            maintainAspectRatio: false, // ✅ Prevents stretching
            responsive: true,
            plugins: { legend: { position: 'top' } },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>