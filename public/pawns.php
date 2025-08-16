<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include '../views/header.php';
?>
<style>
    #wrapper {
        display: flex;
        width: 100%;
    }

    #sidebar-wrapper {
        min-width: 250px;
        max-width: 250px;
        transition: all 0.3s;
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -250px;
    }

    #page-content-wrapper {
        flex: 1;
    }
</style>

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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnModal">
                    <i class="bi bi-plus-circle"></i> Add Pawn
                </button>
            </div>

            <!-- Add Pawn Modal -->
            <div class="modal fade" id="addPawnModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="addPawnForm" method="POST" action="pawn_add_process.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Pawn Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control" name="owner_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Contact No.</label>
                                        <input type="text" class="form-control" name="contact_no" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" name="unit_description" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Category</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Gadgets">Gadgets</option>
                                            <option value="Computer">Computer</option>
                                            <option value="Camera">Camera</option>
                                            <option value="Vehicle">Vehicle</option>
                                            <option value="Appliances">Appliances</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Amount Pawned</label>
                                        <input type="number" step="0.01" class="form-control" name="amount_pawned"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Note</label>
                                        <input type="text" class="form-control" name="notes" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Date Pawned</label>
                                        <input type="date" class="form-control" name="date_pawned"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <!-- Claim Pawn Modal -->
            <div class="modal fade" id="claimPawnModal" tabindex="-1" aria-labelledby="claimPawnModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="claimPawnForm" method="POST" action="pawn_claim_process.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="claimPawnModalLabel">Claim Pawned Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="pawn_id" id="claimPawnId">
                                <input type="hidden" name="claimantPhoto" id="claimantPhoto">
                                <!-- hidden field for captured photo -->

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control" id="claimOwnerName" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Unit Description</label>
                                        <input type="text" class="form-control" id="claimUnitDescription" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Date Pawned</label>
                                        <input type="text" class="form-control" id="claimDatePawned" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Amount Pawned</label>
                                        <input type="text" class="form-control" id="claimAmountPawned" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Months</label>
                                        <input type="text" class="form-control" id="claimMonths" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Interest</label>
                                        <input type="text" class="form-control" id="claimInterest" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Total Payment</label>
                                        <input type="text" class="form-control" id="claimTotal" readonly>
                                    </div>
                                </div>

                                <hr>

                                <!-- Claimant Photo Capture -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Live Camera</label>
                                        <video id="cameraStream" width="100%" height="240" autoplay playsinline></video>
                                        <button type="button" class="btn btn-sm btn-primary mt-2" id="capturePhotoBtn">
                                            <i class="bi bi-camera"></i> Capture Photo
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Captured Photo</label>
                                        <canvas id="capturedCanvas" width="320" height="240"
                                            class="border d-block mb-2"></canvas>
                                        <p class="text-muted small">Captured photo will appear here.</p>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Confirm Claim</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Pawn Modal -->
            <div class="modal fade" id="editPawnModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="editPawnForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Pawn</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="pawn_id" id="editPawnId">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control" name="owner_name" id="editOwnerName"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Contact No.</label>
                                        <input type="text" class="form-control" name="contact_no" id="editContactNo">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" name="unit_description"
                                            id="editUnitDesc" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Category</label>
                                        <select name="category" id="editCategory" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Gadgets">Gadgets</option>
                                            <option value="Computer">Computer</option>
                                            <option value="Camera">Camera</option>
                                            <option value="Vehicle">Vehicle</option>
                                            <option value="Appliances">Appliances</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Amount Pawned</label>
                                        <input type="number" step="0.01" class="form-control" name="amount_pawned"
                                            id="editAmountPawned" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Note</label>
                                        <input type="text" class="form-control" name="notes" id="editNotes">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Date Pawned</label>
                                        <input type="date" class="form-control" name="date_pawned" id="editDatePawned"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>






            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Pawned Items</div>
                <div class="card-body">
                    <table id="pawnTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Contact No.</th>
                                <th>Notes</th>
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

<!-- Sidebar Toggle Script -->
<script>
    const wrapper = document.getElementById("wrapper");
    document.querySelectorAll("#sidebarToggle, #sidebarToggleTop").forEach(btn => {
        btn.addEventListener("click", () => {
            wrapper.classList.toggle("toggled");
        });
    });

    // DataTables AJAX init
    $(document).ready(function () {
        $('#pawnTable').DataTable({
            "ajax": "pawn_list.php",
            "columns": [
                { "title": "Date Pawned" },
                { "title": "Owner" },
                { "title": "Unit" },
                { "title": "Category" },
                { "title": "Amount Pawned" },
                { "title": "Contact No." },
                { "title": "Notes" },
                { "title": "Actions", "orderable": false }
            ]
        });
    });


    // add pawn script
    $(document).ready(function () {
        // Handle Add Pawn form submission
        $("#addPawnForm").on("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Confirm Add Pawn?",
                text: "This will save the pawned item.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, Save it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "pawn_add_process.php",
                        type: "POST",
                        data: $(this).serialize(),
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                Swal.fire("Success", response.message, "success");
                                $("#addPawnModal").modal("hide");
                                $("#addPawnForm")[0].reset();
                                $("#pawnTable").DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error", response.message, "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });
    });



    // When clicking Claim button
    $(document).on("click", ".claimPawnBtn", function (e) {
        e.preventDefault();
        const pawnId = $(this).data("id");

        $.ajax({
            url: "pawn_get.php",
            type: "GET",
            data: { pawn_id: pawnId },
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    Swal.fire("Error", data.error, "error");
                    return;
                }

                // Calculate months
                const datePawned = new Date(data.date_pawned);
                const now = new Date();
                const days = Math.floor((now - datePawned) / (1000 * 60 * 60 * 24));
                const months = Math.max(1, Math.ceil(days / 30));

                // Interest (assume branch interest_rate in %)
                const interestRate = parseFloat(data.interest_rate) || 6; // %
                const principal = parseFloat(data.amount_pawned);
                const interest = principal * (interestRate / 100) * months;
                const total = principal + interest;

                // Fill visible fields
                $("#claimPawnId").val(data.pawn_id);
                $("#claimOwnerName").val(data.owner_name);
                $("#claimUnitDescription").val(data.unit_description);
                $("#claimDatePawned").val(data.date_pawned);
                $("#claimAmountPawned").val(principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimMonths").val(months + " month(s)");
                $("#claimInterest").val("₱" + interest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimTotal").val("₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));

                // Fill hidden fields for backend
                $("#claimInterestRate").val(interestRate);
                $("#claimInterestValue").val(interest.toFixed(2));
                $("#claimPrincipalValue").val(principal.toFixed(2));
                $("#claimTotalValue").val(total.toFixed(2));
                $("#claimMonthsValue").val(months);

                $("#claimPawnModal").modal("show");
            },
            error: function () {
                Swal.fire("Error", "Unable to fetch pawn details.", "error");
            }
        });
    });

    // Submit claim form
    // Submit claim form
    $("#claimPawnForm").on("submit", function (e) {
        e.preventDefault();

        // ✅ Ensure claimant photo is captured
        if (!$("#claimantPhoto").val()) {
            Swal.fire("Error", "Please capture claimant photo before submitting.", "error");
            return false;
        }

        const formData = $(this).serialize();

        Swal.fire({
            title: "Confirm Claim?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Claim it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("pawn_claim_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Claimed!", response.message, "success").then(() => {
                            $("#claimPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }, "json");
            }
        });
    });






    // Webcam Capture for Claimant Photo
    // Initialize webcam stream and capture functionality
    let cameraStream = document.getElementById("cameraStream");
    let capturedCanvas = document.getElementById("capturedCanvas");
    let capturePhotoBtn = document.getElementById("capturePhotoBtn");
    let hiddenPhotoInput = document.getElementById("claimantPhoto");

    // Start webcam when modal opens
    $("#claimPawnModal").on("shown.bs.modal", function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                cameraStream.srcObject = stream;
            })
            .catch((err) => {
                Swal.fire("Camera Error", "Unable to access camera: " + err, "error");
            });
    });

    // Capture photo
    capturePhotoBtn.addEventListener("click", () => {
        let context = capturedCanvas.getContext("2d");
        context.drawImage(cameraStream, 0, 0, capturedCanvas.width, capturedCanvas.height);

        // Save to hidden input as base64
        let photoData = capturedCanvas.toDataURL("image/png");
        hiddenPhotoInput.value = photoData;
        Swal.fire("Success", "Photo captured!", "success");
    });

    // Stop camera when modal closes
    $("#claimPawnModal").on("hidden.bs.modal", function () {
        let stream = cameraStream.srcObject;
        if (stream) {
            let tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
        }
        cameraStream.srcObject = null;
    });




// Open Edit Modal and load pawn details
$(document).on("click", ".editPawnBtn", function (e) {
    e.preventDefault();
    const pawnId = $(this).data("id");

    $.ajax({
        url: "pawn_get.php",
        type: "GET",
        data: { pawn_id: pawnId },
        dataType: "json",
        success: function (data) {
            if (data.error) {
                Swal.fire("Error", data.error, "error");
                return;
            }

            // Populate modal fields
            $("#editPawnId").val(data.pawn_id);
            $("#editOwnerName").val(data.owner_name);
            $("#editContactNo").val(data.contact_no);
            $("#editUnitDesc").val(data.unit_description);
            $("#editCategory").val(data.category);
            $("#editAmountPawned").val(data.amount_pawned);
            $("#editNotes").val(data.notes);
            $("#editDatePawned").val(data.date_pawned);

            $("#editPawnModal").modal("show");
        },
        error: function () {
            Swal.fire("Error", "Failed to fetch pawn details.", "error");
        }
    });
});


// Submit Edit Form
// Handle Edit Pawn Form Submission
$("#editPawnForm").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Confirm Edit?",
        text: "This will adjust Cash on Hand if the amount changes.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Save Changes",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "pawn_edit_process.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $("#editPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Something went wrong.", "error");
                }
            });
        }
    });
});


</script>