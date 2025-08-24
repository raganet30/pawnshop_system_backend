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


?>


<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <h4>Main Dashboard</h4>

            <!-- Branch Summary Table -->
            <div class="card mb-4">
                <div class="card-header">Branch Summary</div>
                <div class="card-body">
                    <table id="branchSummaryTable" class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Branch</th>
                                <th>Pawned Items</th>
                                <th>Claimed</th>
                                <th>Forfeited</th>
                                <th>Total Pawned Value</th>
                                <th>Cash on Hand</th>
                                <th>Total Income</th>
                            </tr>
                        </thead>
                        <tbody id="branchSummaryBody"></tbody>
                        <tfoot class="table-dark" id="branchSummaryFooter"></tfoot>
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
    let monthlyChart, branchChart;

    // 🔹 Animate numbers smoothly
    function animateValue(el, start, end, duration = 800) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            el.innerText = (start + (end - start) * progress).toLocaleString(undefined, { maximumFractionDigits: 0 });
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    }

    async function loadDashboardSuper() {
        try {
            const res = await fetch("dashboard_super_data.php");
            const data = await res.json();

            // Populate Branch Summary Table
            const tbody = document.getElementById("branchSummaryBody");
            const tfoot = document.getElementById("branchSummaryFooter");
            tbody.innerHTML = "";
            let totals = { pawned: 0, claimed: 0, forfeited: 0, value: 0, coh: 0, income: 0 };

            data.branch_stats.forEach(branch => {
                totals.pawned += parseInt(branch.total_pawned);
                totals.claimed += parseInt(branch.claimed);
                totals.forfeited += parseInt(branch.forfeited);
                totals.value += parseFloat(branch.total_pawned_value);
                totals.coh += parseFloat(branch.cash_on_hand);
                totals.income += parseFloat(branch.total_income);

                tbody.innerHTML += `
                <tr>
                    <td>${branch.branch_name}</td>
                    <td>${branch.total_pawned}</td>
                    <td>${branch.claimed}</td>
                    <td>${branch.forfeited}</td>
                    <td>₱${Number(branch.total_pawned_value).toLocaleString()}</td>
                    <td>₱${Number(branch.cash_on_hand).toLocaleString()}</td>
                    <td>₱${Number(branch.total_income).toLocaleString()}</td>
                </tr>
            `;
            });

            // Totals row (with span IDs for animation)
            tfoot.innerHTML = `
            <tr>
                <th>Totals</th>
                <th><span id="totPawned">0</span></th>
                <th><span id="totClaimed">0</span></th>
                <th><span id="totForfeited">0</span></th>
                <th>₱<span id="totValue">0</span></th>
                <th>₱<span id="totCoh">0</span></th>
                <th>₱<span id="totIncome">0</span></th>
            </tr>
        `;


                

            // Animate totals
            animateValue(document.getElementById("totPawned"), parseInt(document.getElementById("totPawned").innerText.replace(/,/g, "")) || 0, totals.pawned);
            animateValue(document.getElementById("totClaimed"), parseInt(document.getElementById("totClaimed").innerText.replace(/,/g, "")) || 0, totals.claimed);
            animateValue(document.getElementById("totForfeited"), parseInt(document.getElementById("totForfeited").innerText.replace(/,/g, "")) || 0, totals.forfeited);
            animateValue(document.getElementById("totValue"), parseInt(document.getElementById("totValue").innerText.replace(/,/g, "")) || 0, totals.value);
            animateValue(document.getElementById("totCoh"), parseInt(document.getElementById("totCoh").innerText.replace(/,/g, "")) || 0, totals.coh);
            animateValue(document.getElementById("totIncome"), parseInt(document.getElementById("totIncome").innerText.replace(/,/g, "")) || 0, totals.income);

            // Monthly Trends Chart Data
            const months = data.trend_data.map(r => new Date(r.month + "-01").toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));
            const pawnedVals = data.trend_data.map(r => parseFloat(r.total_pawned));
            const incomeVals = data.trend_data.map(r => parseFloat(r.total_income));

            if (!monthlyChart) {
                monthlyChart = new Chart(document.getElementById("monthlyTrendsChart"), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            { label: "Pawned Value", data: pawnedVals, borderColor: "blue", backgroundColor: "rgba(54,162,235,0.2)", fill: true },
                            { label: "Income", data: incomeVals, borderColor: "red", backgroundColor: "rgba(255,99,132,0.2)", fill: true }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            } else {
                monthlyChart.data.labels = months;
                monthlyChart.data.datasets[0].data = pawnedVals;
                monthlyChart.data.datasets[1].data = incomeVals;
                monthlyChart.update();
            }

            // Branch Comparison Chart Data
            const branchNames = data.branch_stats.map(r => r.branch_name);
            const branchPawned = data.branch_stats.map(r => r.total_pawned_value);
            const branchCOH = data.branch_stats.map(r => r.cash_on_hand);
            const branchIncome = data.branch_stats.map(r => r.total_income);

            if (!branchChart) {
                branchChart = new Chart(document.getElementById("branchComparisonChart"), {
                    type: "bar",
                    data: {
                        labels: branchNames,
                        datasets: [
                            { label: "Pawned Value", data: branchPawned, backgroundColor: "rgba(54,162,235,0.7)" },
                            { label: "Cash on Hand", data: branchCOH, backgroundColor: "rgba(75,192,192,0.7)" },
                            { label: "Income", data: branchIncome, backgroundColor: "rgba(255,99,132,0.7)" }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            } else {
                branchChart.data.labels = branchNames;
                branchChart.data.datasets[0].data = branchPawned;
                branchChart.data.datasets[1].data = branchCOH;
                branchChart.data.datasets[2].data = branchIncome;
                branchChart.update();
            }

        } catch (err) {
            console.error("Error loading dashboard_super:", err);
        }
    }

    // Initial load
    loadDashboardSuper();
    // Refresh every 1 min
    setInterval(loadDashboardSuper, 60000);
</script>