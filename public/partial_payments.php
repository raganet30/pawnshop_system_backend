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
                <h2>Partial Payments</h2>
            </div>

            <!-- Date Filters -->
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
                <div class="col-md-2 d-flex align-items-end">
                    <button id="filterBtn" class="btn btn-primary me-2">Filter</button>
                    <button id="resetBtn" class="btn btn-secondary">Reset</button>
                </div>
            </div>

            <!-- DataTable -->
            <table id="partialPaymentsTable" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Payment Amount</th>
                        <!-- <th>Interest</th>
                        <th>Principal</th> -->
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Cashier</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right">Totals:</th>
                        <th></th> <!-- Payment total -->

                        <th colspan="4"></th>
                    </tr>
                </tfoot>
                <tbody></tbody>
            </table>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<script>
    // Load DataTable
    $(document).ready(function () {
        let table = $('#partialPaymentsTable').DataTable({
            ajax: {
                url: '../api/partial_payments_list.php',
                type: 'POST',
                data: function (d) {
                    d.branch_id = $('#branchFilter').val();
                    d.from_date = $('#fromDate').val();
                    d.to_date = $('#toDate').val();
                }
            },
            columns: [
                { data: 'serial', title: '#' },
                {
                    data: 'date_paid',
                    title: 'Date',
                    render: function (data) {
                        if (!data) return '';
                        let date = new Date(data);
                        let y = date.getFullYear();
                        let m = String(date.getMonth() + 1).padStart(2, '0');
                        let d = String(date.getDate()).padStart(2, '0');
                        return `${y}-${m}-${d}`;
                    }
                },
                { data: 'customer', title: 'Customer' },
                { data: 'item', title: 'Item' },
                { data: 'amount_paid', title: 'Payment', render: d => '₱' + parseFloat(d).toFixed(2) },

                { data: 'remaining_balance', title: 'Remaining Balance', render: d => '₱' + parseFloat(d).toFixed(2) },
                {
                    data: 'status',
                    title: 'Status',
                    render: function (data) {
                        if (data === 'active') {
                            return '<span class="badge bg-success">Active</span>';
                        } else if (data === 'settled') {
                            return '<span class="badge bg-info">Settled</span>';
                        }
                        return '<span class="badge bg-dark">Unknown</span>';
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-sm btn-secondary print-receipt" 
                                data-id="${row.id}" 
                                data-type="partial">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    `;
                    }
                },
                { data: 'cashier', title: 'Cashier' }
            ],
            order: [[1, 'desc']],
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();

                // Helper function to clean values
                let intVal = function (i) {
                    return typeof i === 'string'
                        ? i.replace(/[₱,]/g, '') * 1
                        : typeof i === 'number'
                            ? i
                            : 0;
                };

                // Compute totals
                let totalPayment = api.column(4).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let totalInterest = api.column(5).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let totalPrincipal = api.column(6).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer cells
                $(api.column(4).footer()).html('₱' + totalPayment.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                // $(api.column(5).footer()).html('₱' + totalInterest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                // $(api.column(6).footer()).html('₱' + totalPrincipal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
            }




        });



        //  Handle Print button click
        $('#partialPaymentsTable').on('click', '.print-receipt', function () {
            let rowData = table.row($(this).closest('tr')).data();

            // Build query params for receipt
            let queryParams = {
                receipt_no: rowData.receipt_no, // backend should return this
                customer_name: rowData.customer,
                item: rowData.item,
                date_paid: rowData.date_paid,
                partial_amount: parseFloat(rowData.amount_paid).toFixed(2),
                interest_paid: parseFloat(rowData.interest_paid).toFixed(2),
                principal_paid: parseFloat(rowData.principal_paid).toFixed(2),
                remaining_balance: parseFloat(rowData.remaining_balance).toFixed(2),
                original_amount_pawned: parseFloat(rowData.original_amount_pawned ?? 0).toFixed(2)

            };

            // Open receipt in new tab
            let printUrl = "../processes/print_tubo_partial_ar.php?" + $.param(queryParams);
            window.open(printUrl, "_blank", "width=800,height=600");
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
            table.ajax.reload();
        });
    });


</script>