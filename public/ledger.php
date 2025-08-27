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
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL IN:</th>
                                <th colspan="2" id="totalIn" class="text-success"></th>
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL OUT:</th>
                                <th colspan="2" id="totalOut" class="text-danger"></th>
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">BALANCE:</th>
                                <th colspan="2" id="balance" class="text-primary"></th>
                                <th colspan="3"></th>
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
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: ':not(:last)' } // exclude Action
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: ':not(:last)' } // exclude Action
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-secondary btn-sm',
                    exportOptions: { columns: ':not(:last)' } // exclude Action
                },
                {
                    extend: 'pageLength',
                    text: '<i class="bi bi-list"></i> Rows',
                    className: 'btn btn-info btn-sm',
                    exportOptions: { columns: ':not(:last)' } // exclude Action
                }

            ],
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
                { title: "Date" },
                { title: "Branch", visible: <?php echo $_SESSION['user']['role'] === 'super_admin' ? 'true' : 'false'; ?> },
                { title: "Txn Type" },
                {
                    title: "Direction",
                    render: function (data) {
                        return data.toLowerCase() === 'in'
                            ? '<span class="badge bg-success">IN</span>'
                            : '<span class="badge bg-danger">OUT</span>';
                    }
                },
                { title: "Amount" },
                { title: "Reference" },
                { title: "Description" },
                { title: "User" }
            ],
            footerCallback: function (row, data) {
                let intVal = i => typeof i === 'string' ? i.replace(/[\₱,]/g, '') * 1 : i || 0;
                let totalIn = 0, totalOut = 0;

                data.forEach(row => {
                    let direction = row[3].toLowerCase();
                    let amount = intVal(row[4]);
                    if (direction.includes("in")) totalIn += amount;
                    if (direction.includes("out")) totalOut += amount;
                });

                $('#totalIn').html("₱" + totalIn.toLocaleString());
                $('#totalOut').html("₱" + totalOut.toLocaleString());
                $('#balance').html("₱" + (totalIn - totalOut).toLocaleString());
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