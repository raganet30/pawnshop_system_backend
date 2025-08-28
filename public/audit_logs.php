<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include '../views/header.php';
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
                <h2>Audit Logs</h2>
                <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnModal">
                    <i class="bi bi-plus-circle"></i> 
                </button> -->
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                    <div class="col-md-3">
                        <label for="branchFilter" class="form-label">Branch:</label>
                        <select id="branchFilter" class="form-select">
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
                    <label for="fromDate" class="form-label">From:</label>
                    <input type="date" id="fromDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="toDate" class="form-label">To:</label>
                    <input type="date" id="toDate" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="actionTypeFilter" class="form-label">Action Type:</label>
                    <select id="actionTypeFilter" class="form-select">
                        <option value="">All Actions</option>
                        <!-- Options will be loaded dynamically via AJAX -->
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button id="filterBtn" class="btn btn-primary me-2">Filter</button>
                    <button id="resetBtn" class="btn btn-secondary">Reset</button>
                </div>
            </div>


            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Logs</div>
                <div class="card-body">
                    <table id="pawnTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date </th>
                                <th>Action Type</th>
                                <th>Description</th>
                                <th>Branch</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated dynamically via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<script>

    // DataTables AJAX init
   $(document).ready(function () {
    // Populate Action Type selector once
    $.ajax({
        url: "../api/audit_logs_list.php?action_types=1", // special param to fetch distinct types
        method: "GET",
        success: function (response) {
            if (response.actionTypes) {
                let select = $("#actionTypeFilter");
                response.actionTypes.forEach(type => {
                    select.append(`<option value="${type}">${type}</option>`);
                });
            }
        }
    });

    // Init DataTable
    let table = $('#pawnTable').DataTable({
        columnDefs: [
            { className: "text-center", targets: "_all" }
        ],
        ajax: {
            url: "../api/audit_logs_list.php",
            type: "GET",
            data: function (d) {
                d.branch_id   = $('#branchFilter').val();
                d.fromDate    = $('#fromDate').val();
                d.toDate      = $('#toDate').val();
                d.action_type = $('#actionTypeFilter').val();
            }
        }
    });

    // Filter button
    $('#filterBtn').on('click', function () {
        table.ajax.reload();
    });

    // Reset button
    $('#resetBtn').on('click', function () {
        $('#branchFilter').val('');
        $('#fromDate').val('');
        $('#toDate').val('');
        $('#actionTypeFilter').val('');
        table.ajax.reload();
    });

    // Optional: auto reload when selecting branch/action type
    $('#branchFilter, #actionTypeFilter').on('change', function () {
        table.ajax.reload();
    });
});


</script>