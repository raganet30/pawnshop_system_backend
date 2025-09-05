<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';
// session checker
require_once "../processes/session_check.php"; 
checkSessionTimeout($pdo);


$user = $_SESSION['user'];
$branch_id = $user['branch_id'];

// Fetch current branch details (interest rate + cash on hand)
$stmt = $pdo->prepare("SELECT interest_rate, cash_on_hand FROM branches WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent cash adjustments
$stmt = $pdo->prepare("
    SELECT created_at, amount, direction, notes 
    FROM cash_ledger 
    WHERE branch_id = ? AND txn_type = 'adjustment'
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute([$branch_id]);
$adjustments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../views/sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <!-- Top Navigation -->
        <?php include '../views/topbar.php'; ?>

        <!-- Main Content -->
        <div class="d-flex justify-content-between mb-3">
            <h4>Cash On Hand / Interest Rate</h4>
        </div>
        <div class="card p-3">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <h5>Interest Rate</h5>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label>Monthly Interest Rate (%)</label>
                            <input id="interestRateInput" type="number" class="form-control"
                                value="<?= htmlspecialchars($branch['interest_rate']) ?>" min="1" step="0.1">
                        </div>
                        <div class="col-md-6">
                            <?php if ($_SESSION['user']['role'] === 'cashier'): ?>
                                <button id="saveInterestBtn" class="btn btn-primary w-100" disabled>Save Rate</button>
                                <!-- <small class="text-muted">Only Admin can update rate</small> -->
                            <?php else: ?>
                                <button id="saveInterestBtn" class="btn btn-primary w-100">Save Rate</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Cash Management (Left) -->
                <div class="col-md-6">
                    <h5>Cash On Hand Management</h5>
                    <div class="card p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Current Cash on Hand:</span>
                            <strong id="currentCashDisplay">
                                ₱<?= number_format($branch['cash_on_hand'], 2) ?>
                            </strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Adjustment Amount (₱)</label>
                                <input id="cashAdjustmentAmountVisible" type="text" class="form-control"
                                    placeholder="0.00" required>
                                <input type="hidden" id="cashAdjustmentAmount" name="amount">
                            </div>

                            <div class="col-md-6">
                                <label>Action</label>
                                <select id="cashAdjustmentType" class="form-control">
                                    <option value="add">Add to Cash</option>
                                    <option value="subtract">Deduct from Cash</option>
                                    <option value="set">Set Exact Amount</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Notes</label>
                                <input id="cashAdjustmentNotes" type="text" class="form-control"
                                    placeholder="Notes for adjustment" required>
                            </div>
                            <div class="col-md-6">
                                <label></label>
                                <button id="saveCashAdjustment" class="btn btn-primary w-100">Apply Adjustment</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Adjustments (Right) -->
                <div class="col-md-6">
                    <h5>Recent Adjustments</h5>
                    <div class="card p-3">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table id="cashAdjustmentTable" class="table table-sm table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>User</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="adjustmentsBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>




            </div>
        </div>


        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script src="../assets/js/money_separator.js"></script>

<script>

    // script to save insterest rate
    document.getElementById("saveInterestBtn")?.addEventListener("click", function () {
        const rate = document.getElementById("interestRateInput").value;

        Swal.fire({
            title: "Save Interest Rate?",
            text: "This will update the monthly interest rate.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, save it"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("../processes/save_interest.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "rate=" + encodeURIComponent(rate)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Saved!", data.message, "success").then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    })
                    .catch(() => {
                        Swal.fire("Error", "Something went wrong.", "error");
                    });
            }
        });
    });


    // function sa fetch recent adjustment
    function loadAdjustments() {
        fetch('../api/fetch_cash_adjustment.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('adjustmentsBody');
                tbody.innerHTML = '';

                if (!data || data.length === 0) {
                    tbody.innerHTML = `<tr>
                    <td colspan="4" class="text-center text-muted">No recent adjustments</td>
                </tr>`;
                    return;
                }

                data.forEach(adj => {
                    let displayAmount = parseFloat(adj.amount).toFixed(2);
                    let badgeClass = 'bg-primary';
                    let sign = '';

                    const dir = (adj.direction || '').toLowerCase(); // normalize

                    if (dir === 'in' || dir === 'add') {
                        badgeClass = 'bg-success';
                        sign = '+';
                    } else if (dir === 'out' || dir === 'subtract') {
                        badgeClass = 'bg-danger';
                        sign = '−';
                    }

                    // Show delta for 'set' adjustments if needed
                    if (dir === 'set') {
                        badgeClass = 'bg-warning';
                        // optionally calculate delta if passed from backend
                    }

                    const row = `
        <tr title="${adj.notes ?? ''}">
            <td>${new Date(adj.created_at).toLocaleString()}</td>
            <td>${sign}₱${displayAmount}</td>
            <td><span class="badge ${badgeClass}">${adj.direction}</span></td>
            <td>${adj.notes ?? ''}</td>
            <td>${adj.full_name ?? ''}</td>
        </tr>
    `;
                    tbody.innerHTML += row;
                });

            })
            .catch(err => {
                console.error("Error fetching adjustments:", err);
            });
    }

    attachCurrencyFormatter(
    document.getElementById('cashAdjustmentAmountVisible'),
    document.getElementById('cashAdjustmentAmount')
);

    // Load adjustments on page load
    document.addEventListener('DOMContentLoaded', loadAdjustments);


    // update cash on hand management
    document.getElementById('saveCashAdjustment').addEventListener('click', function () {
        let amount = parseFloat(document.getElementById('cashAdjustmentAmount').value);
        let action = document.getElementById('cashAdjustmentType').value;
        let notes = document.getElementById('cashAdjustmentNotes').value.trim();

        if (!amount || parseFloat(amount) <= 0 && action !== 'set') {
            Swal.fire("Invalid Amount", "Please enter a valid adjustment amount.", "warning");
            return;
        }

        if (!notes) {
            Swal.fire("Notes Required", "Please provide a reason for the adjustment.", "warning");
            return;
        }

        // Compute delta for 'set'
        let currentCOH = parseFloat(document.getElementById('currentCashDisplay').innerText.replace(/[₱,]/g, ''));
        let delta = amount;
        let direction = action;

        if (action === 'set') {
            delta = Math.abs(amount - currentCOH);
            if (delta === 0) {
                Swal.fire("No Change", "COH is already the specified amount.", "info");
                return;
            }
            direction = amount > currentCOH ? 'in' : 'out';
        }

        Swal.fire({
            title: "Confirm Adjustment",
            html: `Action: <b>${action}</b><br>
               Amount: <b>₱${delta.toFixed(2)}</b> (<b>${direction}</b>)`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, proceed",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('amount', amount);
                formData.append('action', action);
                formData.append('notes', notes);

                fetch('../processes/save_cash_adjustment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire("Success", "Cash adjustment applied!", "success");

                            // Update display
                            document.getElementById('currentCashDisplay').innerText = "₱" + data.new_coh;

                            // Reload adjustments table
                            loadAdjustments();

                            // Reset form
                            document.getElementById('cashAdjustmentAmount').value = '';
                            document.getElementById('cashAdjustmentNotes').value = '';
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire("Error", "Something went wrong.", "error");
                    });
            }
        });
    });





</script>