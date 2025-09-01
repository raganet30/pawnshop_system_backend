<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");

    exit();
}
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);

?>


<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../views/sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <!-- Top Navigation -->
        <?php include '../views/topbar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Reports</h2>


            </div>

            <div class="container-fluid py-4">
                <!-- <h3 class="mb-4">Reports</h3> -->

                <!-- Tabs for Reports -->
                <ul class="nav nav-tabs mb-3" id="reportsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pawned-tab" data-bs-toggle="tab" data-bs-target="#pawned"
                            type="button" role="tab" aria-controls="pawned" aria-selected="true">Pawned Items</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="claimed-tab" data-bs-toggle="tab" data-bs-target="#claimed"
                            type="button" role="tab" aria-controls="claimed" aria-selected="false">Claims</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="forfeited-tab" data-bs-toggle="tab" data-bs-target="#forfeited"
                            type="button" role="tab" aria-controls="forfeited" aria-selected="false">Forfeited</button>
                    </li>
                </ul>

                <div class="tab-content" id="reportsTabContent">
                    <!-- Pawned Items Tab -->
                    <div class="tab-pane fade show active" id="pawned" role="tabpanel" aria-labelledby="pawned-tab">
                        <div class="row mb-3">
                            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                                <div class="col-md-3">
                                    <label for="pawned_branchFilter" class="form-label">Branch:</label>
                                    <select id="pawned_branchFilter" class="form-select">
                                        <option value="">All Branches</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name");
                                        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $branch['branch_id'] . '">' . htmlspecialchars($branch['branch_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-2">
                                <label for="pawned_fromDate" class="form-label">From:</label>
                                <input type="date" id="pawned_fromDate" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="pawned_toDate" class="form-label">To:</label>
                                <input type="date" id="pawned_toDate" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="pawned_filterBtn" class="btn btn-primary me-2">Filter</button>
                                <button id="pawned_resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>



                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="pawned_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="pawned_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="pawned_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="pawnedTable"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Owner</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Contact No.</th>
                                                <th>Address</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Total Pawned Amount</th>
                                                <th id="pawned_total_amount">0.00</th>
                                                <th colspan="3"></th>

                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <!-- Populate via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Claims Tab -->
                    <div class="tab-pane fade" id="claimed" role="tabpanel" aria-labelledby="claimed-tab">
                        <div class="row mb-3">
                            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                                <div class="col-md-3">
                                    <label for="claimed_branchFilter" class="form-label">Branch:</label>
                                    <select id="claimed_branchFilter" class="form-select">
                                        <option value="">All Branches</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name");
                                        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $branch['branch_id'] . '">' . htmlspecialchars($branch['branch_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-2">
                                <label for="claimed_fromDate" class="form-label">From:</label>
                                <input type="date" id="claimed_fromDate" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="claimed_toDate" class="form-label">To:</label>
                                <input type="date" id="claimed_toDate" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="claimed_filterBtn" class="btn btn-primary me-2">Filter</button>
                                <button id="claimed_resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="claimed_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="claimed_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="claimed_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="claimedTable"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Date Claimed</th>
                                                <th>Owner Name</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Interest Amount</th>
                                                <th>Total Paid</th>
                                                <th>Contact No.</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-end">Totals</th>
                                                <th id="claimed_total_pawned">0.00</th>
                                                <th id="claimed_total_interest">0.00</th>
                                                <th id="claimed_total_paid">0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <!-- Populate via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Forfeited Tab -->
                    <div class="tab-pane fade" id="forfeited" role="tabpanel" aria-labelledby="forfeited-tab">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                                <div class="col-md-3">
                                    <label for="forfeited_branch_filter" class="form-label">Branch:</label>
                                    <select id="forfeited_branch_filter" class="form-select">
                                        <option value="">All Branches</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name");
                                        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $branch['branch_id'] . '">' . htmlspecialchars($branch['branch_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-2">
                                <label for="forfeited_from_date" class="form-label">From:</label>
                                <input type="date" id="forfeited_from_date" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <label for="forfeited_to_date" class="form-label">To:</label>
                                <input type="date" id="forfeited_to_date" class="form-control">
                            </div>

                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button id="forfeited_filter_btn" class="btn btn-primary">Filter</button>
                                <button id="forfeited_reset_btn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <!-- Export & Table -->
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="forfeited_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="forfeited_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="forfeited_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="forfeitedTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Date Forfeited</th>
                                                <th>Owner</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Contact No.</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-end">Totals</th>
                                                <th id="forfeited_total_amount">0.00</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <!-- Populated via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>



        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script>
    const userRole = "<?php echo $_SESSION['user']['role']; ?>";
    const userBranch = "<?php echo $_SESSION['user']['branch_name'] ?? ''; ?>";
</script>
<script src="../assets/js/pawned_items_report.js"></script>
<script src="../assets/js/claimed_items_report.js"> </script>
<script src="../assets/js/forfeited_items_report.js"></script>