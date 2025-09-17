<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);


$user_role = $_SESSION['user']['role'];
$branch_id = $_SESSION['user']['branch_id'] ?? null;

// restrict: cashier/admin only see their branch; super_admin sees all
?>
<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Claims</h4>

            </div>

            <?php include '../views/filters.php'; ?>

            <!-- View Claim Modal -->
            <div class="modal fade" id="viewClaimModal" tabindex="-1" aria-labelledby="viewClaimModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">View Claimed Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="viewPawnId">

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label>Owner Name</label>
                                    <input type="text" class="form-control" id="viewOwnerName" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Unit Description</label>
                                    <input type="text" class="form-control" id="viewUnitDescription" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Date Pawned</label>
                                    <input type="text" class="form-control" id="viewDatePawned" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Date Claimed</label>
                                    <input type="text" class="form-control" id="viewDateClaimed" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Amount Pawned</label>
                                    <input type="text" class="form-control" id="viewAmountPawned" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Interest</label>
                                    <input type="text" class="form-control" id="viewInterest" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Penalty (if any)</label>
                                    <input type="text" class="form-control" id="viewPenalty" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Total Paid</label>
                                    <input type="text" class="form-control" id="viewTotalPaid" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Contact No.</label>
                                    <input type="text" class="form-control" id="viewContact" readonly>
                                </div>
                               

                                <!--  Tubo Payments History -->
                                <h6 class="mt-2">Tubo Payments History</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="tuboPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Covered Period</th>
                                                <th>Months Covered</th>
                                                <th>Interest Rate</th>
                                                <th>Interest Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- JS will populate this -->
                                        </tbody>
                                    </table>
                                </div>

                                <!--  Partial Payments History -->
                                <h6 class="mt-2">Partial Payments History</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="partialPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Amount Paid</th>
                                                <!-- <th>Interest Paid</th>
                                                <th>Principal Paid</th> -->
                                                <th>Remaining Principal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- JS will populate this -->
                                        </tbody>
                                    </table>
                                </div>

                                 <div class="col-md-3">
                                    <label>Claimant Photo</label>
                                    <img id="viewClaimPhoto" src="" class="img-fluid border rounded"
                                        alt="Claimant Photo">
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
            </div>



            <div class="card">

                <div class="card-header">Claimed Items</div>
                <div class="card-body">
                    <table id="claimsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th> <!-- Auto-increment -->
                                <th>Date Pawned</th>
                                <th>Date Claimed</th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Interest Amount</th>
                                <th>Penalty</th>
                                <th>Total Paid</th>
                                <th>Contact No.</th>
                                <?php if ($_SESSION['user']['role'] !== 'super_admin'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">TOTALS:</th>
                                <th id="totalPawned"></th>
                                <th id="totalInterest"></th>
                                <th id="totalPenalty"></th>
                                <th id="totalPaid"></th>
                                <th></th>
                                <?php if ($_SESSION['user']['role'] !== 'super_admin'): ?>
                                    <th></th>
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

<script src="../assets/js/receipt.js"></script>

<script>
    $(document).ready(function () {
        let userRole = "<?= $user_role ?>";

        let claimsTable = $("#claimsTable").DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "../api/claim_list.php",
                data: function (d) {
                    if (userRole === "super_admin") {
                        d.branch_id = $("#branchFilter").val();
                    }
                    d.start_date = $("#fromDate").val();
                    d.end_date = $("#toDate").val();
                },
                dataSrc: function (json) {
                    // Calculate totals
                    let totalPawned = 0, totalInterest = 0, totalPaid = 0; totalPenalty = 0;
                    json.data.forEach(row => {
                        totalPawned += parseFloat(row[5].replace(/[^0-9.-]+/g, "")) || 0;
                        totalInterest += parseFloat(row[6].replace(/[^0-9.-]+/g, "")) || 0;
                        totalPenalty += parseFloat(row[7].replace(/[^0-9.-]+/g, "")) || 0;
                        totalPaid += parseFloat(row[8].replace(/[^0-9.-]+/g, "")) || 0;
                    });

                    $("#totalPawned").text('₱' + totalPawned.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $("#totalInterest").text('₱' + totalInterest.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $("#totalPenalty").text('₱' + totalPenalty.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $("#totalPaid").text('₱' + totalPaid.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

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
                { data: 0, className: "text-center" }, // Date Pawned
                { data: 1, className: "text-center" }, // Date Claimed
                { data: 2 }, // Owner
                { data: 3 }, // Unit
                { data: 4 }, // Category
                { data: 5, className: "text-end" }, // Amount Pawned
                { data: 6, className: "text-end" }, // Interest Amount
                { data: 7, className: "text-end" }, // Penalty Amount
                { data: 8, className: "text-end" }, // Total Paid
                { data: 9, className: "text-center" }, // Contact No.
                <?php if ($_SESSION['user']['role'] !== 'super_admin'): ?>
                        { data: 10, orderable: false, className: "text-center" } // Actions
            <?php endif; ?>
            ],
            order: [[1, "desc"]],
            responsive: true,
            columnDefs: [
                { targets: "_all", className: "align-middle" }
            ]
        });







        // Branch filter (super admin only)
        <?php if ($user_role === 'super_admin'): ?>
            $("#branchFilter").on("change", function () {
                claimsTable.ajax.reload();
            });
        <?php endif; ?>

        // Date filters
        $("#filterBtn").on("click", function () {
            claimsTable.ajax.reload();
        });
        $("#resetBtn").on("click", function () {
            $("#fromDate, #toDate").val('');
            <?php if ($user_role === 'super_admin'): ?>
                $("#branchFilter").val('');
            <?php endif; ?>
            claimsTable.ajax.reload();
        });
    });


   // Handle View button
$(document).on("click", ".viewClaimBtn", function (e) {
    e.preventDefault();
    const pawnId = $(this).data("id");

    $.ajax({
        url: "../api/claim_view.php",
        type: "GET",
        data: { pawn_id: pawnId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                const d = response.data;

                // Fill in claim details
                $("#viewPawnId").val(d.pawn_id);
                $("#viewOwnerName").val(d.full_name);
                $("#viewUnitDescription").val(d.unit_description);
                $("#viewDatePawned").val(d.date_pawned);
                $("#viewDateClaimed").val(d.date_claimed);
                $("#viewAmountPawned").val("₱" + parseFloat(d.amount_pawned).toFixed(2));
                $("#viewInterest").val("₱" + parseFloat(d.interest_amount).toFixed(2));
                $("#viewTotalPaid").val("₱" + parseFloat(d.total_paid).toFixed(2));
                $("#viewPenalty").val("₱" + parseFloat(d.penalty_amount).toFixed(2));
                $("#viewContact").val(d.contact_no);

                // Show claimant photo
                if (d.photo_path && d.photo_path !== "") {
                    $("#viewClaimPhoto").attr("src", "../" + d.photo_path);
                } else {
                    $("#viewClaimPhoto").attr("src", "assets/img/no-photo.png");
                }

                // --- Populate Partial Payments ---
                let ppHtml = "";
if (d.partial_payments && d.partial_payments.length > 0) {
    d.partial_payments.forEach((p, i) => {
        ppHtml += `
            <tr>
                <td>${i + 1}</td>
                <td>${p.date_paid}</td>
                <td>₱${parseFloat(p.amount_paid).toFixed(2)}</td>
                <td>₱${parseFloat(p.remaining_balance).toFixed(2)}</td>
                <td>${p.remarks}</td>
            </tr>
        `;
    });
} else {
                    ppHtml = `<tr><td colspan="7" class="text-center">No partial payments</td></tr>`;
                }
                $("#partialPaymentsTable tbody").html(ppHtml);

                // --- Populate Tubo Payments ---
                let tuboHtml = "";
                if (d.tubo_payments && d.tubo_payments.length > 0) {
                    d.tubo_payments.forEach((t,i) => {
                        tuboHtml += `
                            <tr>
                                <td>${i + 1}</td>
                                <td>${t.date_paid}</td>
                                <td>${t.period_start} to ${t.period_end}</td>
                                <td>${t.months_covered} mo(s)</td>
                                <td>₱${parseFloat(t.interest_amount).toFixed(2)}</td>
                                <td>${t.remarks}</td>
                            </tr>
                        `;
                    });
                } else {
                    tuboHtml = `<tr><td colspan="5" class="text-center">No tubo payments</td></tr>`;
                }
                $("#tuboPaymentsTable tbody").html(tuboHtml);

                // Show modal
                $("#viewClaimModal").modal("show");
            } else {
                alert(response.message);
            }
        }
    });
});







    //revert claim function
    $(document).on("click", ".revertClaimBtn", function (e) {
        e.preventDefault();
        let pawn_id = $(this).data("id");

        Swal.fire({
            title: "Revert Claim?",
            text: "This will move the item back to pawned items and deduct cash on hand.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Revert"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/claim_revert_process.php", { pawn_id: pawn_id }, function (resp) {
                    if (resp.status === "success") {
                        Swal.fire("Reverted!", resp.message, "success").then(() => {
                            // Reload claims table
                            $("#claimsTable").DataTable().ajax.reload();

                            // If pawn table exists (in pawns.php), reload it too
                            if ($("#pawnTable").length) {
                                $("#pawnTable").DataTable().ajax.reload();
                            }
                        });
                    } else {
                        Swal.fire("Error", resp.message, "error");
                    }
                }, "json");
            }
        });
    });






    
   // Handle manual print from dropdown
$(document).on("click", ".printClaimBtn", function (e) {
    e.preventDefault();

    let pawnId = $(this).data("id");

    if (pawnId) {
        let printUrl = "../processes/receipt_print.php?pawn_id=" + pawnId;
        window.open(printUrl, "_blank"); // open receipt in new tab for printing
    }
});


</script>