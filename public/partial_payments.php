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

            <!-- DataTable -->
            <table id="partialPaymentsTable" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Payment</th>
                        <th>Interest</th>
                        <th>Principal</th>
                        <th>Remaining Balance</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<script>
// Load DataTable
$(document).ready(function () {
    $('#partialPaymentsTable').DataTable({
    ajax: '../api/partial_payments_list.php',
    columns: [
        { data: 'serial', title: '#' },
        { data: 'date_paid', title: 'Date' },
        { data: 'customer', title: 'Customer' },
        { data: 'item', title: 'Item' },
        { data: 'amount_paid', title: 'Payment', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'interest_paid', title: 'Interest', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'principal_paid', title: 'Principal', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'remaining_balance', title: 'Remaining Balance', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'cashier', title: 'Cashier' }
    ],
    order: [[1, 'desc']], // default order by date_paid descending
});

});

</script>