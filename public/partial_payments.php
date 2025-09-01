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
            <table id="partialPaymentsTable" class="table table-bordered table-striped">
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
$('#partialPaymentsTable').DataTable({
    ajax: 'api/partial_payments_list.php',
    columns: [
        { data: 'date_paid' },
        { data: 'customer' },
        { data: 'item' },
        { data: 'amount_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'interest_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'principal_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'remaining_balance', render: d => '₱' + parseFloat(d).toFixed(2) },
        { data: 'cashier' }
    ]
});


<script>