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

<?php
$highlightPawnId = $_GET['id'] ?? '';
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
                <h2>Pawns</h2>
                <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'cashier'): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnModal">
                        <i class="bi bi-plus-circle"></i> Add Pawn
                    </button>
                <?php endif; ?>
            </div>

            <!-- view pawn modal -->
            <?php include '../public/modals/view_pawn_modal.php'; ?>


            <!-- add pawn modal -->
            <?php include '../public/modals/add_pawn_modal.php'; ?>


            <!-- edit pawn modal -->
            <?php include '../public/modals/edit_pawn_modal.php'; ?>


            <!-- add pawn amount modal -->
            <?php include '../public/modals/add_pawn_amount_modal.php'; ?>


            <!-- claim pawn modal -->
            <?php include '../public/modals/claim_pawn_modal.php'; ?>


            <!-- partial payment modal -->
            <?php include '../public/modals/partial_payment_modal.php'; ?>


            <!-- tubo payment modal -->
            <?php include '../public/modals/tubo_payment_modal.php'; ?>


            <!-- forfeit pawn modal -->
            <?php include '../public/modals/forfeit_pawn_modal.php'; ?>



            <!-- add branch filtering when super admin is the user -->
            <!-- DataTable -->
            <!-- Branch filter for Super Admin -->
            <!-- Filters -->
            <?php include '../views/filters.php'; ?>

            <!-- Pawned Items Table -->
            <div class="card">
                <div class="card-header">Pawned Items</div>
                <div class="card-body">
                    <table id="pawnTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Pawned</th>
                                <th>Unpaid Months</th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Contact No.</th>
                                <th>Address</th>
                                <th>Notes</th>
                                <?php if (in_array($_SESSION['user']['role'], ['admin', 'cashier'])): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">TOTAL PAWNED AMOUNT:</th>
                                <th id="totalPawned"></th>
                                <?php if (in_array($_SESSION['user']['role'], ['admin', 'cashier'])): ?>
                                    <th colspan="4"></th>
                                <?php endif; ?>
                                <?php if (in_array($_SESSION['user']['role'], ['super_admin'])): ?>
                                    <th colspan="3"></th>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script src="../assets/js/pawn_view.js"></script>
<script src="../assets/js/pawn_add.js"></script>
<script src="../assets/js/pawn_edit.js"></script>
<script src="../assets/js/pawn_claim.js"></script>
<script src="../assets/js/money_separator.js"></script>
<script src="../assets/js/receipt.js"></script>
<script src="../assets/js/pawn_partial_payment.js"></script>
<script src="../assets/js/pawn_tubo_payment.js"></script>
<script src="../assets/js/add_pawn_amount.js"></script>

<script>

    // pawns.php
    // DataTables AJAX init
    $(document).ready(function () {

        // Get pawn_id from URL (if any)
        function getPawnIdFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const id = params.get("id");

            if (id) {
                // Remove ?id=... from the URL (without reload)
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }

            return id;
        }


        let pawnTable = $('#pawnTable').DataTable({
            columnDefs: [
                { className: "text-center", targets: "_all" }
            ],
            ajax: {
                url: "../api/pawn_list.php",
                data: function (d) {
                    <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                        d.branch_id = $('#branchFilter').val();
                    <?php endif; ?>
                    d.start_date = $('#fromDate').val();
                    d.end_date = $('#toDate').val();

                    //  Add pawn_id filter only if it exists in the URL
                    let pawnId = getPawnIdFromUrl();
                    if (pawnId) {
                        d.pawn_id = pawnId;
                    }
                },
                dataSrc: function (json) {
                    // Populate total pawned in footer
                    $('#totalPawned').text('₱' + json.total_pawned);
                    return json.data;
                }
            },
            columns: [
                {
                    title: "#",
                    data: null,
                    className: "text-center",
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Auto-increment
                    }
                },
                { title: "Date Pawned" },
                { title: "Unpaid Months" },
                { title: "Owner" },
                { title: "Unit" },
                { title: "Category" },
                { title: "Amount Pawned" },
                { title: "Contact No." },
                { title: "Address" },
                { title: "Notes" },
                <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'cashier'): ?>
                    { title: "Actions", orderable: false }
        <?php endif; ?>
            ]
        });


        // Filter button click
        $('#filterBtn').on('click', function () {
            pawnTable.ajax.reload();
        });

        // Reset button click
        $('#resetBtn').on('click', function () {
            $('#fromDate, #toDate').val('');
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                $('#branchFilter').val('');
            <?php endif; ?>
            pawnTable.ajax.reload();
        });

        <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
            $('#branchFilter').on('change', function () {
                pawnTable.ajax.reload();
            });
        <?php endif; ?>
    });



    // Open Forfeit Modal
    $(document).on("click", ".forfeitPawnBtn", function (e) {
        e.preventDefault();
        const pawnId = $(this).data("id");

        $.get("../api/pawn_get.php", { pawn_id: pawnId }, function (data) {
            if (data.status !== "success") {
                Swal.fire("Error", data.message || "Failed to fetch pawn details", "error");
                return;
            }

            const pawn = data.pawn;

            // Calculate months
            const datePawned = new Date(pawn.date_pawned);
            const now = new Date();
            const days = Math.floor((now - datePawned) / (1000 * 60 * 60 * 24));
            const months = Math.max(1, Math.ceil(days / 30));

            // Restriction: only allow forfeiture if >= 2 months
            if (months < 2) {
                Swal.fire(
                    "Not Eligible",
                    "Pawned item can only be forfeited after 2 months.",
                    "info"
                );
                return; // stop further execution
            }

            // Populate modal fields
            $("#forfeitPawnId").val(pawn.pawn_id);
            $("#forfeitOwnerName").val(pawn.customer_name);
            $("#forfeitUnit").val(pawn.unit_description);
            $("#forfeitDatePawned").val(pawn.date_pawned);
            $("#forfeitMonths").val(months + " month(s)");
            $("#forfeitAmount").val("₱" + parseFloat(pawn.amount_pawned).toLocaleString(undefined, { minimumFractionDigits: 2 }));
            $("#forfeitNotes").val(pawn.notes || "");
            $("#forfeitReason").val(""); // clear previous reason

            $("#forfeitPawnModal").modal("show");
        }, "json");
    });

    // Handle Forfeit Form Submit
    $("#forfeitPawnForm").on("submit", function (e) {
        e.preventDefault();
        const monthsText = $("#forfeitMonths").val(); // e.g., "3 month(s)"
        const months = parseInt(monthsText);

        if (months < 2) {
            Swal.fire("Error", "Pawned item can only be forfeited after 2 months.", "error");
            return;
        }

        const reason = $("#forfeitReason").val().trim();
        if (!reason) {
            Swal.fire("Error", "Please provide a reason for forfeiture.", "error");
            return;
        }

        const formData = $(this).serialize();

        Swal.fire({
            title: "Confirm Forfeit?",
            text: "This will mark the item as forfeited and update cash flow.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Forfeit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/pawn_forfeit_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Forfeited!", response.message, "success").then(() => {
                            $("#forfeitPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message || "Failed to forfeit pawn", "error");
                    }
                }, "json");
            }
        });
    });


    // delete pawn
    $(document).on("click", ".deletePawnBtn", function (e) {
        e.preventDefault();
        const pawnId = $(this).data("id");

        Swal.fire({
            title: "Move to Trash?",
            text: "This pawn record will be moved to trash but not permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Move it",
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/pawn_delete.php", { pawn_id: pawnId }, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Trashed!", response.message, "success");
                        $("#pawnTable").DataTable().ajax.reload();
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }, "json");
            }
        });
    });

    // money separator script add pawn
    attachCurrencyFormatter(
        document.getElementById('addAmountPawnedVisible'),
        document.getElementById('addAmountPawned')
    );


    //money separator script edit pawn
    attachCurrencyFormatter(
        document.getElementById('editAmountPawnedVisible'),
        document.getElementById('editAmountPawned')
    );




    // function getUnpaidMonths(pawn, data, todayLocal = new Date()) {
    //     let unpaidMonths = 0;

    //     // -------- Step 1: if NO partials, NO tubo --------
    //     if (!pawn.has_partial_payments && !pawn.has_tubo_payments && pawn.status == 'pawned') {
    //         let startDate = new Date(pawn.current_due_date);

    //         if (todayLocal > startDate) {
    //             unpaidMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
    //                            (todayLocal.getMonth() - startDate.getMonth());

    //             if (todayLocal.getDate() > startDate.getDate()) unpaidMonths++;
    //             if (unpaidMonths < 1) unpaidMonths = 1;
    //         }
    //     }

    //     // -------- Step 2: if HAS tubo payments --------
    //     else if (pawn.has_tubo_payments && pawn.status == 'pawned') {
    //         let lastTuboEnd = null;
    //         if (Array.isArray(data.tubo_history) && data.tubo_history.length > 0) {
    //             let lastTuboIndex = data.tubo_history.length - 1;
    //             lastTuboEnd = new Date(data.tubo_history[lastTuboIndex].new_due_date);
    //         }

    //         if (lastTuboEnd) {
    //             if (todayLocal <= lastTuboEnd) {
    //                 unpaidMonths = 0; // still covered
    //             } else {
    //                 let startDate = new Date(lastTuboEnd);

    //                 unpaidMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
    //                                (todayLocal.getMonth() - startDate.getMonth());

    //                 if (todayLocal.getDate() > startDate.getDate()) unpaidMonths++;
    //                 if (unpaidMonths < 1) unpaidMonths = 1;
    //             }
    //         }
    //     }

    //     // -------- Step 3: Partial payments --------
    //     else if (pawn.has_partial_payments) {
    //         let currentDueDate = new Date(pawn.current_due_date);

    //         if (currentDueDate && todayLocal <= currentDueDate) {
    //             unpaidMonths = 0; // still covered
    //         } else if (currentDueDate) {
    //             let startDate = new Date(currentDueDate);

    //             unpaidMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
    //                            (todayLocal.getMonth() - startDate.getMonth());

    //             if (todayLocal.getDate() > startDate.getDate()) unpaidMonths++;
    //             if (unpaidMonths < 1) unpaidMonths = 1;
    //         }
    //     }

    //     // -------- Step 4: Both tubo + partial payments --------
    //     else if (pawn.has_partial_payments && pawn.has_tubo_payments) {
    //         let currentDueDate = pawn.current_due_date ? new Date(pawn.current_due_date) : null;
    //         let tuboDueDate = (data.tubo_history && data.tubo_history.length > 0)
    //             ? new Date(data.tubo_history[data.tubo_history.length - 1].new_due_date)
    //             : null;

    //         // pick whichever is later
    //         let latestDueDate = null;
    //         if (currentDueDate && tuboDueDate) {
    //             latestDueDate = (tuboDueDate > currentDueDate) ? tuboDueDate : currentDueDate;
    //         } else {
    //             latestDueDate = currentDueDate || tuboDueDate;
    //         }

    //         if (latestDueDate && todayLocal <= latestDueDate) {
    //             unpaidMonths = 0;
    //         } else if (latestDueDate) {
    //             let startDate = new Date(latestDueDate);

    //             unpaidMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
    //                            (todayLocal.getMonth() - startDate.getMonth());

    //             if (todayLocal.getDate() > startDate.getDate()) unpaidMonths++;
    //             if (unpaidMonths < 1) unpaidMonths = 1;
    //         }
    //     }

    //     return unpaidMonths;
    // }






</script>