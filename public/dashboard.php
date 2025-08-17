<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';


?>



<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Dashboard</h4>
                <!-- <div>
              <a href="pawns.php"><button class="btn btn-success" data-section="pawn-add">New Pawn</button></a>
              <button class="btn btn-outline-secondary" id="refreshBtn" >Refresh</button>
            </div> -->
            </div>

            <!-- Summary Cards -->
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Pawned Items</div>
                        <h3 id="pawnedUnits">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Claimed Items</div>
                        <h3 id="claimedItems">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Forfeited Items</div>
                        <h3 id="forfeitedItems">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Pawned Items Total Value</div>
                        <h3 id="pawnedValue">₱0.00</h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Cash on Hand</div>
                        <h3 id="cashOnHand">₱0.00</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Daily Interest Accumulated</div>
                        <h3 id="dailyInterest">₱0.00</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted">Grand Total Interest Accumulated</div>
                        <h3 id="grandTotalInterest">₱0.00</h3>
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


            <!-- Recent Pawned Items Table -->
            <div class="card">
                <div class="card-header">Recent Pawn Transactions</div>
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
                            <!-- Filled dynamically by AJAX -->
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



    // Load Dashboard Stats
    // This function fetches the latest stats from the server
    function loadDashboardStats() {
        $.ajax({
            url: "dashboard_stats.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                $("#pawnedUnits").text(data.pawned_units);
                $("#pawnedValue").text("₱" + parseFloat(data.pawned_value).toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#cashOnHand").text("₱" + parseFloat(data.cash_on_hand).toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimedItems").text(data.claimed_qty);
                $("#forfeitedItems").text(data.forfeited_qty);
                $("#dailyInterest").text("₱" + parseFloat(data.daily_interest).toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#grandTotalInterest").text("₱" + parseFloat(data.grand_total_interest).toLocaleString(undefined, { minimumFractionDigits: 2 }));
            },
            error: function () {
                console.error("Failed to load dashboard stats.");
            }
        });
    }

    $(document).ready(function () {
        loadDashboardStats();

        // Optional auto-refresh every 30s
        // setInterval(loadDashboardStats, 30000);
    });


    // Load Recent Items and Monthly Trends
    // This function fetches recent pawned items and monthly trends data
    function loadDashboardData() {
        $.ajax({
            url: "dashboard_data.php",
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
                        "₱" + parseFloat(item.amount_pawned).toLocaleString(undefined, { minimumFractionDigits: 2 }),
                        statusBadge
                    ]);
                });

                table.draw();

                /* =====================
                   Update Monthly Trends
                   ===================== */
                const months = data.trend_data.map(row => row.month);
                const pawned = data.trend_data.map(row => parseFloat(row.total_pawned));
                const interest = data.trend_data.map(row => parseFloat(row.total_interest));

                monthlyTrendsChart.data.labels = months.map(m => {
                    const d = new Date(m + "-01");
                    return d.toLocaleString("default", { month: "short", year: "numeric" });
                });
                monthlyTrendsChart.data.datasets[0].data = pawned;
                monthlyTrendsChart.data.datasets[1].data = interest;
                monthlyTrendsChart.update();
            },
            error: function () {
                console.error("Failed to load dashboard data.");
            }
        });
    }

    $(document).ready(function () {
        // DataTable init (empty at first)
        $("#pawnedItemsTable").DataTable();

        // Load data initially
        loadDashboardData();

        // Auto refresh every 30s
        setInterval(loadDashboardData, 30000);
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
                    label: 'Interest Earned',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: { y: { beginAtZero: true } }
        }
    });

</script>