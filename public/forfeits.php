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
        $('#pawnTable').DataTable({
            columnDefs: [
                { className: "text-center", targets: "_all" } // applies to ALL columns
            ],
            "ajax": "../api/forfeit_list.php",
            "columns": [
                { "title": "Date Pawned" },
                { "title": "Date Forfeited" },
                { "title": "Owner" },
                { "title": "Unit" },
                { "title": "Category" },
                { "title": "Amount Pawned" },
                { "title": "Contact No." },
                { "title": "Reason" },
                { "title": "Actions", "orderable": false }
            ]
        });
    });


    // Revert Forfeited Item to Pawned
$(document).on("click", ".revertForfeitBtn", function(e) {
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
            $.post("../processes/forfeit_revert_process.php", { pawn_id: pawn_id }, function(resp) {
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