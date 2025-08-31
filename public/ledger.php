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
                <h2>Cash Ledger</h2>
            </div>


            <?php include '../views/filters.php'; ?>

            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Ledger Records
                </div>
                <div class="card-body">
                    <table id="cashLedgerTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Txn Type</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated dynamically via DataTables AJAX -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end"></th>
                                <th colspan="2" class="text-success"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL IN:</th>
                                <th colspan="2" id="totalIn" class="text-success"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL OUT:</th>
                                <th colspan="2" id="totalOut" class="text-danger"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">BALANCE:</th>
                                <th colspan="2" id="balance" class="text-primary"></th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<script>
    $(document).ready(function () {
       let table = $('#cashLedgerTable').DataTable({
    columnDefs: [{ className: "text-center", targets: "_all" }],
    ajax: {
        url: "../api/cash_ledger_list.php",
        data: function (d) {
            d.fromDate = $('#fromDate').val();
            d.toDate = $('#toDate').val();
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                d.branch_id = $('#branchFilter').val();
            <?php endif; ?>
        }
    },
    columns: [
        {
            title: "#",
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1; // auto-increment
            },
            className: "text-center"
        },
        { title: "Date", data: 1 },
        { 
            title: "Branch", 
            data: 2, 
            visible: <?php echo $_SESSION['user']['role'] === 'super_admin' ? 'true' : 'false'; ?> 
        },
        { title: "Txn Type", data: 3 },
        {
            title: "Direction",
            data: 4,
            render: function (data) {
                return data.toLowerCase() === 'in'
                    ? '<span class="badge bg-success">IN</span>'
                    : '<span class="badge bg-danger">OUT</span>';
            }
        },
        { title: "Amount", data: 5 },
        { title: "Reference", data: 6 },
        { title: "Description", data: 7 },
        { title: "User", data: 8 }
    ],
    footerCallback: function (row, data) {
        let intVal = i => typeof i === 'string' ? i.replace(/[\₱,]/g, '') * 1 : i || 0;
        let totalIn = 0, totalOut = 0;

        data.forEach(row => {
            let direction = row[4].toLowerCase(); // column index for Direction
            let amount = intVal(row[5]);          // column index for Amount
            if (direction === "in") totalIn += amount;
            else if (direction === "out") totalOut += amount;
        });

        $('#totalIn').html("₱" + totalIn.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
        $('#totalOut').html("₱" + totalOut.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
        $('#balance').html("₱" + (totalIn - totalOut).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
    }
});


        <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
            // Auto-load ledger on page load
            table.ajax.reload();

            // Reload table when branch changes
            $('#branchFilter').on('change', function () {
                table.ajax.reload();
            });
        <?php endif; ?>

        // Filter button
        $('#filterBtn').on('click', function () {
            table.ajax.reload();
        });

        // Reset button
        $('#resetBtn').on('click', function () {
            $('#fromDate').val('');
            $('#toDate').val('');
            table.ajax.reload();
        });
    });


</script>