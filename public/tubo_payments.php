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
                <h2>Tubo Payments</h2>
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
            <table id="tuboPaymentsTable" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Owner</th>
                        <th>Payment Date</th>
                        <th>Covered Period</th>
                        <th>Months Covered</th>
                        <th>Interest Amount</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align:right">Total:</th>
                        <th></th>
                       
                    </tr>
                </tfoot>
                <tbody></tbody>
            </table>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script>
$(document).ready(function () {

    // Initialize DataTable
    let tuboTable = $("#tuboPaymentsTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "../api/tubo_payments_list.php",
            type: "POST",
            data: function(d) {
                d.branch_id = $("#branchFilter").val() || '';
                d.from_date = $("#fromDate").val() || '';
                d.to_date = $("#toDate").val() || '';
            }
        },
        columns: [
            { data: "tubo_id" },
            { data: "item" },
            { data: "owner" },
            { data: "date_paid" },
            { 
                data: null,
                render: function (data, type, row) {
                    return `${row.period_start} to ${row.period_end}`;
                }
            },
            { data: "months_covered" },
            { 
                data: "interest_amount",
                render: $.fn.dataTable.render.number(',', '.', 2, '₱')
            },
            {
                    data: null,
                    title: 'Action',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-secondary print-tubo"
                                    data-id="${row.tubo_id}"
                                    data-pawnid="${row.pawn_id}"
                                    data-owner="${row.owner}"
                                    data-item="${row.item}"
                                    data-datepaid="${row.date_paid}"
                                    data-periodstart="${row.period_start}"
                                    data-periodend="${row.period_end}"
                                    data-months="${row.months_covered}"
                                    data-interest="${row.interest_amount}"
                                    data-amountpawned="${row.original_amount_pawned}">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        `;
                    }

                }
        ],
        order: [[3, 'desc']], // order by date_paid
        footerCallback: function ( row, data, start, end, display ) {
            let api = this.api();

            // Total over all pages
            let total = api
                .column(6, { page:'current'} )
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            // Update footer
            $(api.column(6).footer()).html('₱' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
    });

    // Filter button
    $("#filterBtn").on("click", function() {
        tuboTable.ajax.reload();
    });

    // Reset button
    $("#resetBtn").on("click", function() {
        $("#branchFilter").val('');
        $("#fromDate").val('');
        $("#toDate").val('');
        tuboTable.ajax.reload();
    });

    // Optional: reload on branch/date change automatically
    $("#branchFilter, #fromDate, #toDate").on("change", function() {
        tuboTable.ajax.reload();
    });


    $(document).on("click", ".print-tubo", function() {
    const btn = $(this);

    // Build receipt no: pawnId-mmddyy
    let d = new Date(btn.data("datepaid"));
    let mm = String(d.getMonth() + 1).padStart(2, "0");
    let dd = String(d.getDate()).padStart(2, "0");
    let yy = String(d.getFullYear()).slice(-2);
    let receipt_no = String(btn.data("pawnid")).padStart(3, "0") + "-" + mm + dd + yy;

    const queryParams = $.param({
        receipt_no: receipt_no,
        customer_name: btn.data("owner"),              // ✅ match PHP
        item: btn.data("item"),
        date_paid: btn.data("datepaid"),
        period_start: btn.data("periodstart"),
        period_end: btn.data("periodend"),
        months_covered: btn.data("months"),
        interest_amount: btn.data("interest"),
        original_amount_pawned: btn.data("amountpawned") // ✅ match PHP
    });

    window.open(`../processes/print_tubo_payment_ar.php?${queryParams}`, "_blank");
});



});


</script>

