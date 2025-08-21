<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
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
                <h2>Pawns</h2>
                <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'cashier'): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnModal">
                        <i class="bi bi-plus-circle"></i> Add Pawn
                    </button>
                <?php endif; ?>
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

                                    <!-- Owner Details -->
                                    <!-- Customer Selection/Add New -->
                                    <div class="col-md-12">
                                        <label>Customer</label>
                                        <select id="customer_id" name="customer_id" class="form-control" required>
                                            <option value="">-- Select Customer --</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="addNewCustomer">
                                            <label class="form-check-label" for="addNewCustomer">
                                                Add New Customer
                                            </label>
                                        </div>
                                    </div>

                                    <div id="newCustomerFields" class="row g-3" style="display:none;">
                                        <div class="col-md-6">
                                            <label>Full Name</label>
                                            <input type="text" class="form-control" name="customer_name" >
                                        </div>
                                        <div class="col-md-6">
                                            <label>Contact No.</label>
                                            <input type="text" class="form-control" name="contact_no"
                                                placeholder="09XXXXXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address"
                                                placeholder="Customer Address">
                                        </div>


                                    </div>


                                    <!-- Pawn Item Details -->
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
                                        <!-- Visible input with formatting -->
                                        <input type="text" class="form-control" id="addAmountPawnedVisible"
                                            placeholder="0.00" required>
                                        <!-- Hidden input for raw numeric value -->
                                        <input type="hidden" name="amount_pawned" id="addAmountPawned">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Note</label>
                                        <input type="text" class="form-control" name="notes">
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
                                        <!-- Visible formatted input -->
                                        <input type="text" class="form-control" id="editAmountPawnedVisible"
                                            placeholder="0.00" required>
                                        <!-- Hidden raw value for submission -->
                                        <input type="hidden" name="amount_pawned" id="editAmountPawned">


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

            <!-- Forfeit Modal -->
            <div class="modal fade" id="forfeitPawnModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="forfeitPawnForm" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Forfeit Pawned Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="pawn_id" id="forfeitPawnId">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control" id="forfeitOwnerName" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" id="forfeitUnit" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Date Pawned</label>
                                        <input type="text" class="form-control" id="forfeitDatePawned" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Months</label>
                                        <input type="text" class="form-control" id="forfeitMonths" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Amount Pawned</label>
                                        <input type="text" class="form-control" id="forfeitAmount" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Confirm Forfeit</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>





            <!-- add branch filtering when super admin is the user -->
            <!-- DataTable -->
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                <div class="mb-3">
                    <label for="branchFilter" class="form-label">Select Branch</label>
                    <select id="branchFilter" class="form-select" style="width: 250px;">
                        <option value="">All Branches</option>
                        <?php
                        // Load branches from DB
                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name");
                        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $branch['branch_id'] . '">' . htmlspecialchars($branch['branch_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>

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
                                <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'cashier'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
        let pawnTable = $('#pawnTable').DataTable({
            columnDefs: [
                { className: "text-center", targets: "_all" } // applies to ALL columns
            ],
            ajax: {
                url: "pawn_list.php",
                data: function (d) {
                    <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                        d.branch_id = $('#branchFilter').val(); // send selected branch ID
                    <?php endif; ?>
                }
            },
            columns: [
                { title: "Date Pawned" },
                { title: "Owner" },
                { title: "Unit" },
                { title: "Category" },
                { title: "Amount Pawned" },
                { title: "Contact No." },
                { title: "Notes" },
                <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'cashier'): ?>
                                                            { title: "Actions", orderable: false }
            <?php endif; ?>
            ]
        });

        <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
            // Reload table when branch is changed
            $('#branchFilter').on('change', function () {
                pawnTable.ajax.reload();
            });
        <?php endif; ?>
    });





    // add pawn script
    // $(document).ready(function () {
    //     // Handle Add Pawn form submission
    //     $("#addPawnForm").on("submit", function (e) {
    //         e.preventDefault();

    //         Swal.fire({
    //             title: "Confirm Add Pawn?",
    //             text: "This will save the pawned item.",
    //             icon: "question",
    //             showCancelButton: true,
    //             confirmButtonText: "Yes, Save it!",
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: "pawn_add_process.php",
    //                     type: "POST",
    //                     data: $(this).serialize(),
    //                     dataType: "json",
    //                     success: function (response) {
    //                         if (response.status === "success") {
    //                             Swal.fire("Success", response.message, "success");
    //                             $("#addPawnModal").modal("hide");
    //                             $("#addPawnForm")[0].reset();
    //                             $("#pawnTable").DataTable().ajax.reload();
    //                         } else {
    //                             Swal.fire("Error", response.message, "error");
    //                         }
    //                     },
    //                     error: function () {
    //                         Swal.fire("Error", "Something went wrong.", "error");
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // });



    // load customer search with Select2
    // $(document).ready(function () {
    //     // Initialize Select2 when modal is shown
    //     $('#addPawnModal').on('shown.bs.modal', function () {
    //         if (!$('#customer_id').hasClass("select2-hidden-accessible")) {
    //             $('#customer_id').select2({
    //                 placeholder: 'Search or add new customer',
    //                 dropdownParent: $('#addPawnModal'), // important for modals
    //                 ajax: {
    //                     url: 'customer_search.php',
    //                     dataType: 'json',
    //                     delay: 250,
    //                     data: function (params) {
    //                         return { term: params.term };
    //                     },
    //                     processResults: function (data) {
    //                         return { results: data };
    //                     }
    //                 },
    //                 minimumInputLength: 1,
    //                 allowClear: true,
    //                 templateResult: formatCustomer,
    //                 templateSelection: formatCustomerSelection
    //             });

    //             console.log("✅ Select2 initialized on #customer_id");
    //         }
    //     });

    //     // Format customer in dropdown
    //     function formatCustomer(customer) {
    //         if (customer.loading) return customer.text;
    //         return $('<span>' + customer.text + ' (' + customer.contact_no + ')</span>');
    //     }

    //     function formatCustomerSelection(customer) {
    //         return customer.text || customer.id;
    //     }

    //     // Show new customer fields if adding new
    //     $('#customer_id').on('select2:select', function (e) {
    //         let data = e.params.data;
    //         if (data.isNew) {
    //             $('#newCustomerFields').show();
    //             $('input[name="contact_no"]').val('');
    //             $('input[name="address"]').val('');
    //         } else {
    //             $('#newCustomerFields').hide();
    //             $('input[name="contact_no"]').val(data.contact_no);
    //             $('input[name="address"]').val(data.address);
    //         }
    //     });

    //     // Add Pawn form submission
    //     $("#addPawnForm").on("submit", function (e) {
    //         e.preventDefault();
    //         Swal.fire({
    //             title: "Confirm Add Pawn?",
    //             text: "This will save the pawned item.",
    //             icon: "question",
    //             showCancelButton: true,
    //             confirmButtonText: "Yes, Save it!",
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: "pawn_add_process.php",
    //                     type: "POST",
    //                     data: $(this).serialize(),
    //                     dataType: "json",
    //                     success: function (response) {
    //                         if (response.status === "success") {
    //                             Swal.fire("Success", response.message, "success");
    //                             $("#addPawnModal").modal("hide");
    //                             $("#addPawnForm")[0].reset();
    //                             $("#pawnTable").DataTable().ajax.reload();
    //                         } else {
    //                             Swal.fire("Error", response.message, "error");
    //                         }
    //                     },
    //                     error: function () {
    //                         Swal.fire("Error", "Something went wrong.", "error");
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // });



    // $(document).ready(function() {

    //     $('#addPawnModal').on('shown.bs.modal', function () {
    //         $('#customer_id').select2({
    //             placeholder: 'Search or add new customer',
    //             dropdownParent: $('#addPawnModal'),
    //             ajax: {
    //                 url: 'customer_search.php',
    //                 dataType: 'json',
    //                 delay: 250,
    //                 data: function (params) {
    //                     return { term: params.term };
    //                 },
    //                 processResults: function (data) {
    //                     return {
    //                         results: data.map(function(item) {
    //                             return {
    //                                 id: item.id,
    //                                 text: item.text,
    //                                 contact_no: item.contact_no,
    //                                 address: item.address
    //                             };
    //                         })
    //                     };
    //                 }
    //             },
    //             minimumInputLength: 1,
    //             tags: true,              // <-- enable new entries
    //             createTag: function (params) {
    //                 return {
    //                     id: params.term,
    //                     text: params.term,
    //                     isNew: true            // mark it as a new customer
    //                 };
    //             },
    //             allowClear: true
    //         });
    //     });

    //     // When selecting a customer
    //     $('#customer_id').on('select2:select', function (e) {
    //         let data = e.params.data;
    //         if (data.isNew) {
    //             // Show extra fields for new customer
    //             $('#newCustomerFields').show();
    //             $('input[name="contact_no"]').val('');
    //             $('input[name="address"]').val('');
    //         } else {
    //             $('#newCustomerFields').hide();
    //             $('input[name="contact_no"]').val(data.contact_no || '');
    //             $('input[name="address"]').val(data.address || '');
    //         }
    //     });

    //     // When clearing selection
    //     $('#customer_id').on('select2:clear', function() {
    //         $('#newCustomerFields').show();
    //         $('input[name="contact_no"]').val('');
    //         $('input[name="address"]').val('');
    //     });

    // });




    $(document).ready(function () {

        $('#addPawnModal').on('shown.bs.modal', function () {

            $('#customer_id').select2({
                placeholder: 'Search for customer...',
                dropdownParent: $('#addPawnModal'),
                ajax: {
                    url: 'customer_search.php',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term || '' }; // pass search term
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.customer_id,
                                    text: item.full_name,
                                    contact_no: item.contact_no,
                                    address: item.address
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0, // show all customers immediately
                templateResult: formatCustomer,
                templateSelection: formatCustomerSelection,
                allowClear: true
            });

            // Format how each customer appears in the dropdown
            function formatCustomer(customer) {
                if (customer.loading) return customer.text;

                // Create a jQuery object so HTML renders correctly
                var $container = $(
                    "<div class='select2-result-customer'>" +
                    "<div class='fw-bold'>" + customer.text + "</div>" +
                    (customer.address ? "<small>Address: " + customer.address + "</small>" : "") +
                    (customer.contact_no ? " | <small>Contact: " + customer.contact_no + "</small>" : "") +

                    "</div>"
                );

                return $container;
            }

            // Format selected customer (shown in the input)
            function formatCustomerSelection(customer) {
                return customer.text || customer.id || '';
            }


        });

        // Toggle new customer fields and required attribute
        $('#addNewCustomer').change(function () {
            if ($(this).is(':checked')) {
                // Show new customer fields
                $('#newCustomerFields').show();
                $('#customer_id').prop('disabled', true);

                // Make customer_name required
                $('input[name="customer_name"]').prop('required', true);
            } else {
                // Hide new customer fields
                $('#newCustomerFields').hide();
                $('#customer_id').prop('disabled', false);

                // Make customer_name optional
                $('input[name="customer_name"]').prop('required', false);
            }
        });


        // Fill contact/address on selection
        $('#customer_id').on('select2:select', function (e) {
            let data = e.params.data;
            $('input[name="contact_no"]').val(data.contact_no || '');
            $('input[name="address"]').val(data.address || '');
        });

        $('#customer_id').on('select2:clear', function () {
            $('input[name="contact_no"]').val('');
            $('input[name="address"]').val('');
        });

    });

    // Add Pawn form submission
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




    // When clicking Claim button
    $(document).ready(function () {
        $('#customer_id').select2({
            placeholder: 'Search or add new customer',
            ajax: {
                url: 'customer_search.php',
                dataType: 'json',
                delay: 250,
                data: params => ({ term: params.term }),
                processResults: data => ({ results: data })
            },
            minimumInputLength: 1,
            allowClear: true,
            tags: true  // allow entering new names
        });

        // Handle selection
        $('#customer_id').on('select2:select', function (e) {
            let data = e.params.data;

            if (data.id) {
                // Existing customer: hide extra fields, auto-fill contact & address
                $('#newCustomerFields').hide();
                $('input[name="contact_no"]').val(data.contact_no || '');
                $('input[name="address"]').val(data.address || '');
            } else {
                // New customer typed: show extra fields
                $('#newCustomerFields').show();
                $('input[name="contact_no"]').val('');
                $('input[name="address"]').val('');
            }
        });
    });






    $('#addAmountPawnedVisible').on('input', function () {
        let raw = $(this).val().replace(/[^0-9.]/g, '');
        $('#addAmountPawned').val(raw);
    });



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


    // Open Forfeit Modal
    $(document).on("click", ".forfeitPawnBtn", function (e) {
        e.preventDefault();
        const pawnId = $(this).data("id");

        $.get("pawn_get.php", { pawn_id: pawnId }, function (data) {
            if (data.error) {
                Swal.fire("Error", data.error, "error");
                return;
            }

            // Calculate months
            const datePawned = new Date(data.date_pawned);
            const now = new Date();
            const days = Math.floor((now - datePawned) / (1000 * 60 * 60 * 24));
            const months = Math.max(1, Math.ceil(days / 30));

            // Populate modal
            $("#forfeitPawnId").val(data.pawn_id);
            $("#forfeitOwnerName").val(data.owner_name);
            $("#forfeitUnit").val(data.unit_description);
            $("#forfeitDatePawned").val(data.date_pawned);
            $("#forfeitMonths").val(months + " month(s)");
            $("#forfeitAmount").val("₱" + parseFloat(data.amount_pawned).toLocaleString());

            $("#forfeitPawnModal").modal("show");
        }, "json");
    });

    // Handle Forfeit Form Submit
    $("#forfeitPawnForm").on("submit", function (e) {
        e.preventDefault();
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
                $.post("pawn_forfeit_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Forfeited!", response.message, "success").then(() => {
                            $("#forfeitPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message, "error");
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
                $.post("pawn_delete.php", { pawn_id: pawnId }, function (response) {
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





    // Format amount input for pawned items
    // This script formats the amount input for pawned items with thousand separators
    // while typing, and ensures the hidden input for submission is correctly formatted.
    /* Add Pawn: format amount input with thousand separators while typing.
       Visible input: #addAmountPawnedVisible
       Hidden input (submitted): #addAmountPawned
    */
    (function () {
        const visible = document.getElementById('addAmountPawnedVisible');
        const hidden = document.getElementById('addAmountPawned');

        if (!visible || !hidden) return;

        function formatCurrencyInput(raw) {
            if (!raw) return '';
            // Keep only digits and dot, allow single dot
            raw = raw.replace(/[^\d.]/g, '');
            const parts = raw.split('.');
            let intPart = parts[0].replace(/^0+(?=\d)/, ''); // remove leading zeros
            if (intPart === '') intPart = '0';
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts.length > 1) {
                // limit to 2 decimals
                parts[1] = parts[1].slice(0, 2);
                return intPart + '.' + parts[1];
            }
            return intPart;
        }

        function rawNumberString(formatted) {
            if (!formatted) return '';
            return formatted.replace(/,/g, '');
        }

        function syncHidden() {
            const formatted = visible.value;
            const rawStr = rawNumberString(formatted);
            if (rawStr === '' || rawStr === '.') {
                hidden.value = '';
                return;
            }
            // Ensure a valid number with max 2 decimals
            const normalized = parseFloat(rawStr);
            if (isNaN(normalized)) {
                hidden.value = '';
                return;
            }
            // Keep two decimals for submission
            hidden.value = normalized.toFixed(2);
        }

        visible.addEventListener('input', function (e) {
            const caret = this.selectionStart;
            const before = this.value;
            const formatted = formatCurrencyInput(before);
            this.value = formatted;

            // adjust caret roughly to end (accurate caret restoration with formatting is complex)
            this.selectionStart = this.selectionEnd = this.value.length;

            syncHidden();
        });

        // Format on blur to ensure two decimals
        visible.addEventListener('blur', function () {
            const raw = rawNumberString(this.value);
            if (raw === '' || raw === '.') {
                this.value = '';
                hidden.value = '';
                return;
            }
            const num = parseFloat(raw);
            if (isNaN(num)) {
                this.value = '';
                hidden.value = '';
                return;
            }
            // Format with 2 decimals and thousand separators
            const parts = num.toFixed(2).split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            this.value = parts.join('.');
            hidden.value = num.toFixed(2);
        });

        // Prevent non-numeric keys except control keys
        visible.addEventListener('keypress', function (e) {
            const allowed = /[0-9.]/
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            const char = String.fromCharCode(e.which);
            if (!allowed.test(char)) e.preventDefault();
            // prevent multiple dots
            if (char === '.' && this.value.includes('.')) e.preventDefault();
        });

        // Ensure hidden is synced before form submit (in case JS formatted after input)
        const addForm = document.getElementById('addPawnForm');
        if (addForm) {
            addForm.addEventListener('submit', function () {
                // trigger blur formatting and sync
                visible.dispatchEvent(new Event('blur'));
            });
        }
    })();


    // script to add money separators to input fields in editPawnModal

    (function () {
        const visible = document.getElementById('editAmountPawnedVisible');
        const hidden = document.getElementById('editAmountPawned');

        function formatNumber(value) {
            if (value === '' || value === null || isNaN(value)) return '';
            return parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function rawFromFormatted(str) {
            if (!str) return '';
            const cleaned = str.replace(/[^0-9.-]/g, '');
            return cleaned === '' ? '' : parseFloat(cleaned).toFixed(2);
        }

        // When modal is shown, format whatever raw value is present (this covers existing data)
        $('#editPawnModal').on('show.bs.modal', function () {
            const raw = hidden.value;
            if (raw !== undefined && raw !== null && raw !== '') {
                visible.value = formatNumber(raw);
            } else {
                visible.value = '';
            }
        });

        // Format while typing and keep hidden raw value updated
        visible.addEventListener('input', function (e) {
            // Keep caret roughly at end after formatting
            const pos = this.selectionStart;
            const oldLen = this.value.length;

            // Remove commas and any non-digit/dot chars
            let raw = this.value.replace(/,/g, '').replace(/[^0-9.]/g, '');
            // Handle multiple dots: keep first
            const parts = raw.split('.');
            if (parts.length > 2) {
                raw = parts.shift() + '.' + parts.join('');
            }
            const split = raw.split('.');
            let integer = split[0] || '0';
            let decimal = split[1] || '';

            // Limit decimals to 2
            if (decimal.length > 2) decimal = decimal.slice(0, 2);

            // Avoid leading zeros like "000" -> "0"
            integer = integer.replace(/^0+(?=\d)/, '');

            let formatted = integer ? Number(integer).toLocaleString() : '';
            if (decimal !== '') {
                // ensure decimal has no extra non-digits
                formatted = (formatted === '' ? '0' : formatted) + '.' + decimal;
            }

            // If both empty, show empty
            if (integer === '' && decimal === '') formatted = '';

            this.value = formatted;
            // Update hidden raw value (fixed to 2 decimals) or empty
            hidden.value = (raw === '' || isNaN(Number(raw))) ? '' : Number(raw).toFixed(2);

            // Restore caret position
            const newLen = this.value.length;
            const newPos = Math.max(0, pos + (newLen - oldLen));
            this.setSelectionRange(newPos, newPos);
        });

        // If code elsewhere programmatically sets hidden value while modal is open,
        // update visible immediately
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                if (m.attributeName === 'value') {
                    const raw = hidden.value;
                    visible.value = (raw !== undefined && raw !== null && raw !== '') ? formatNumber(raw) : '';
                }
            });
        });
        observer.observe(hidden, { attributes: true, attributeFilter: ['value'] });
    })();

</script>