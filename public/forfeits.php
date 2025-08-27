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
                <h2>Forfeits</h2>
                <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnModal">
                    <i class="bi bi-plus-circle"></i> 
                </button> -->
            </div>

            <?php include '../views/filters.php'; ?>


            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Forfeited Items</div>
                <div class="card-body">
                    <table id="pawnTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Date Forfeited/th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Contact No.</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">TOTAL:</th>
                                <th></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>

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
        let table = $('#pawnTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: ':not(:last-child)' } // exclude Actions
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-secondary btn-sm',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'pageLength',
                    className: 'btn btn-info btn-sm'
                }
            ],
            ajax: {
                url: '../api/forfeit_list.php',
                data: function (d) {
                    d.branch_id = $('#branchFilter').val();
                    d.fromDate = $('#fromDate').val();
                    d.toDate = $('#toDate').val();
                }
            },
            columnDefs: [
                { className: "text-center", targets: "_all" }
            ],
            columns: [
                { "title": "Date Pawned" },
                { "title": "Date Forfeited" },
                { "title": "Owner" },
                { "title": "Unit" },
                { "title": "Category" },
                { "title": "Amount Pawned" },
                { "title": "Contact No." },
                { "title": "Reason" },
                { "title": "Actions", "orderable": false }
            ],
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();

                // remove ₱ and commas then sum
                let intVal = function (i) {
                    return typeof i === 'string'
                        ? i.replace(/[\₱,]/g, '') * 1
                        : typeof i === 'number'
                            ? i : 0;
                };

                // Total of Amount Pawned (column 5)
                let totalPawned = api
                    .column(5, { page: 'current' })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $(api.column(5).footer()).html('₱' + totalPawned.toLocaleString());
            }
        });

        // Auto reload when selecting branch
        $('#branchFilter').on('change', function () {
            table.ajax.reload();
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


    // Revert Forfeited Item to Pawned
    $(document).on("click", ".revertForfeitBtn", function (e) {
        e.preventDefault();
        let pawn_id = $(this).data("id");

        Swal.fire({
            title: "Revert Forfeit?",
            text: "This will move the item back to pawned items and adjust cash on hand.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Revert"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/forfeit_revert_process.php", { pawn_id: pawn_id }, function (resp) {
                    if (resp.status === "success") {
                        Swal.fire("Reverted!", resp.message, "success");
                        $("#pawnTable").DataTable().ajax.reload();
                    } else {
                        Swal.fire("Error", resp.message, "error");
                    }
                }, "json")
                    .fail(() => Swal.fire("Error", "Server error while processing revert.", "error"));
            }
        });
    });







</script>