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


            <!-- View Pawn Modal -->
            <div class="modal fade" id="viewPawnModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pawn Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Customer -->
                                <div class="col-md-3">
                                    <label>Pawner</label>
                                    <input type="text" class="form-control" id="viewCustomerName" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Contact No.</label>
                                    <input type="text" class="form-control" id="viewContactNo" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Address</label>
                                    <input type="text" class="form-control" id="viewAddress" readonly>
                                </div>

                                <!-- Pawn Item Details -->
                                <div class="col-md-3">
                                    <label>Unit</label>
                                    <input type="text" class="form-control" id="viewUnitDescription" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Category</label>
                                    <input type="text" class="form-control" id="viewCategory" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Amount Pawned</label>
                                    <input type="text" class="form-control" id="viewAmountPawned" readonly>
                                </div>

                                

                                <div class="col-md-3">
                                    <label>Date Pawned</label>
                                    <input type="text" class="form-control" id="viewDatePawned" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Current Due Date</label>
                                    <input type="text" class="form-control" id="viewDueDate" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label>Intererest Rate</label>
                                    <input type="text" class="form-control" id="viewInterest" readonly>
                                </div>


                                <div class="col-md-3">
                                    <label>Note</label>
                                    <input type="text" class="form-control" id="viewNotes" readonly>
                                </div>

                                


                                 <!--  Tubo Payments History -->
                                <h6 class="mt-3">Tubo Payments History</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="tuboPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Covered Period</th>
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
                                <h6 class="mt-3">Partial Payments History</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="partialPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Amount Paid</th>
                                                <th>Interest Paid</th>
                                                <th>Principal Paid</th>
                                                <th>Remaining Principal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- JS will populate this -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pawn Item Picture -->
                                <div class="col-md-6 ">
                                    <label class="form-label d-block">Item Picture</label>
                                    <div class="mt-2">
                                        <img id="view_pawn_preview" src="../assets/img/avatar.png" class="img-thumbnail"
                                            style="max-width:300px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Add Pawn Modal -->
            <div class="modal fade" id="addPawnModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="addPawnForm" method="POST" action="../processes/pawn_add_process.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Pawn Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    <!-- Owner Details -->
                                    <!-- Customer Selection/Add New -->
                                    <div class="col-md-12">
                                        <label>Pawner</label>
                                        <select id="customer_id" name="customer_id" class="form-control" required>
                                            <option value="">-- Select Pawner --</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-2">

                                        <input type="checkbox" class="btn-check" id="addNewCustomer" autocomplete="off">
                                        <label class="btn btn-primary" for="addNewCustomer">Add New Pawner</label>



                                    </div>

                                    <div id="newCustomerFields" class="row g-3" style="display:none;">
                                        <div class="col-md-6">
                                            <label>Full Name</label>
                                            <input type="text" class="form-control" name="customer_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Contact No.</label>
                                            <input type="text" class="form-control" name="contact_no"
                                                placeholder="09XXXXXXXXX">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address"
                                                placeholder="Pawner Address">
                                        </div>


                                    </div>


                                    <!-- Pawn Item Details -->
                                    <div class="col-md-6">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" name="unit_description" required>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Cellphone">Cellphone</option>
                                            <option value="Laptop">Laptop</option>
                                            <option value="Tablet/iPad">Tablet/iPad</option>
                                            <option value="Motorcycle">Motorcycle</option>
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

                                    <!-- Claimant Photo Capture -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Live Camera</label>
                                            <video id="cameraStream" width="100%" height="240" autoplay
                                                playsinline></video>
                                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                                id="pawnCapturePhotoBtn">
                                                <i class="bi bi-camera"></i> Capture Photo
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Captured Photo</label>
                                            <canvas id="pawnCapturedCanvas" width="320" height="240"
                                                class="border d-block mb-2"></canvas>
                                            <p class="text-muted small">Captured photo will appear here.</p>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Edit Pawn Modal -->
            <div class="modal fade" id="editPawnModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="editPawnForm" method="POST" action="../processes/pawn_edit_process.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Pawn Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="pawn_id" id="editPawnId">

                                <div class="row g-3">
                                    <!-- Customer (read-only) -->
                                    <div class="col-md-12">
                                        <label>Pawner</label>
                                        <input type="text" class="form-control" id="editCustomerName" readonly>
                                    </div>

                                    <!-- Contact (read-only for reference) -->
                                    <div class="col-md-6">
                                        <label>Contact No.</label>
                                        <input type="text" class="form-control" id="editContactNo" readonly>
                                    </div>

                                    <!-- Address (read-only for reference) -->
                                    <div class="col-md-6">
                                        <label>Address</label>
                                        <input type="text" class="form-control" id="editAddress" readonly>
                                    </div>

                                    <!-- Pawn Item Details -->
                                    <div class="col-md-6">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" name="unit_description"
                                            id="editUnitDescription" required>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Category</label>
                                        <select name="category" id="editCategory" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Cellphone">Cellphone</option>
                                            <option value="Laptop ">Laptop</option>
                                            <option value="Tablet/iPad">Tablet/iPad</option>
                                            <option value="Motorcycle">Motorcycle</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Amount Pawned</label>
                                        <!-- Visible input with formatting -->
                                        <input type="text" class="form-control" id="editAmountPawnedVisible"
                                            placeholder="0.00" required>
                                        <!-- Hidden input for raw numeric value -->
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



                                    <!-- Pawn Item Picture -->
                                    <div class="col-md-6 text-center">
                                        <label class="form-label d-block">Item Picture</label>
                                        <div class="mt-2">
                                            <img id="edit_pawn_preview" src="../assets/img/avatar.png"
                                                class="img-thumbnail" style="max-width:300px; ">

                                            <!-- Replace photo controls -->
                                            <div class="mt-3">
                                                <input type="file" id="editPawnPhoto" name="pawn_photo" accept="image/*"
                                                    class="form-control form-control-sm mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="editPawnCaptureBtn">
                                                    <i class="bi bi-camera"></i> Capture New Photo
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Hidden canvas for capture -->
                                        <canvas id="editPawnCanvas" width="320" height="240" class="d-none"></canvas>
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


            <!-- Claim Pawn Modal -->
            <div class="modal fade" id="claimPawnModal" tabindex="-1" aria-labelledby="claimPawnModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form id="claimPawnForm" method="POST" action="../processes/pawn_claim_process.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="claimPawnModalLabel">Claim Pawned Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="pawn_id" id="claimPawnId">
                                <input type="hidden" name="claimantPhoto" id="claimantPhoto">

                                <!-- Pawn Details -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label>Owner Name</label>
                                        <input type="text" class="form-control" id="claimOwnerName" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Unit Description</label>
                                        <input type="text" class="form-control" id="claimUnitDescription" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Date Pawned</label>
                                        <input type="text" class="form-control" id="claimDatePawned" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Months to be paid</label>
                                        <input type="text" class="form-control" id="claimMonths" readonly>
                                        <input type="hidden" id="claimMonthsValue" name="claimMonthsValue">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Amount Pawned</label>
                                        <input type="text" class="form-control" id="claimAmountPawned" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Interest Amount <small>(auto compute)</small> </label>
                                        <input type="text" class="form-control" id="claimInterest" readonly>
                                        
                                        <input type="hidden" class="form-control" id="claimInterestValue"
                                            name="claimInterestValue">

                                    </div>
                                    <div class="col-md-3">
                                        <label for="claimPenalty">Penalty (optional)</label>
                                        <input type="number" step="0.01" class="form-control" id="claimPenalty"
                                            name="claimPenalty" placeholder="Enter penalty amount">
                                        <input type="number" step="0.01" class="form-control" id="claimPenaltyHidden"
                                            name="claimPenaltyHidden" hidden>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Total Payment</label>
                                        <input type="text" class="form-control" id="claimTotal" readonly>

                                        <input type="number" step="0.01" class="form-control" id="claimTotalValue"
                                            name="claimTotalValue" hidden >
                                    </div>

                                     <div class="col-md-3">
                                        <label>Current Due Date</label>
                                        <input type="text" class="form-control" id="claimDueDate" readonly>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label >Date Claimed</label>
                                        <input type="date" class="form-control" id="claimDate" name="claimDate"
                                            required>
                                    </div>


                                    <div class="col-md-4">
                                        <label>Notes</label>
                                        <input type="text" class="form-control" id="claimNotes" name="claimNotes">
                                    </div>
                                    
                                </div>
                               <p id="waiveInfo" class="small d-none mt-2"></p>

                                <!--  Tubo Payments History -->
                                <h6 class="mt-3">Tubo Payments History</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-sm" id="tuboPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Covered Period</th>
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
                                <h6 class="mt-3">Partial Payments History</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="partialPaymentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date Paid</th>
                                                <th>Amount Paid</th>
                                                <th>Interest Paid</th>
                                                <th>Principal Paid</th>
                                                <th>Remaining Principal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- JS will populate this -->
                                        </tbody>
                                    </table>
                                </div>

                                

                                <hr>

                                <!-- Claimant Photo Capture -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Live Camera</label>
                                        <video id="claimCameraStream" width="100%" height="240" autoplay
                                            playsinline></video>
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



            <!-- Partial Payment Modal -->
            <div class="modal fade" id="partialPaymentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form id="partialPaymentForm">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Partial Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <!-- Pawn Details -->

                                    <div class="col-md-3">
                                        <label class="form-label">Pawner Name</label>
                                        <input type="text" class="form-control" id="ppPawnerName" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" class="form-control" id="ppCategory" readonly>
                                    </div>


                                    <div class="col-md-3">
                                        <label class="form-label">Unit</label>
                                        <input type="text" class="form-control" id="ppUnit" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date Pawned</label>
                                        <input type="text" class="form-control" id="ppDatePawned" readonly>
                                    </div>


                                    <div class="col-md-3">
                                        <label class="form-label">Amount Pawned</label>
                                        <input type="text" class="form-control" id="ppAmountPawned" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Months Covered</label>
                                        <input type="text" class="form-control" id="ppMonths" readonly>
                                    </div>



                                    <!-- Partial Payment -->

                                    <div class="col-md-3">
                                        <label class="form-label">Enter Partial Payment</label>
                                        <input type="number" class="form-control" id="ppAmount" name="partial_amount"
                                            min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Notes</label>
                                        <input type="text" class="form-control" id="ppNotes" name="ppNotes">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Current Due Date</label>
                                        <input type="text" class="form-control" id="ppDueDate" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" id="ppDatePaid" name="ppDatePaid"
                                            required>
                                    </div>
                                </div>

                                <!-- Live Computation -->
                                <div id="ppSummary" class="alert alert-info">
                                    <div>Original Principal: ₱0.00</div>
                                    <div>Partial Payment: ₱0.00</div>
                                    <div>Remaining Principal: ₱0.00</div>
                                    <div>1-Month Interest: ₱0.00</div>
                                    <hr>
                                    <strong>Total Payable Now: ₱0.00</strong>
                                </div>



                                <hr>
                                <h6>Tubo History</h6>
                                <table class="table table-sm table-bordered" id="ppTuboHistory">
                                    <thead>
                                        <tr>
                                            <th>Date Paid</th>
                                            <th>Covered Period</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <h6>Partial Payment History</h6>
                                <table class="table table-sm table-bordered" id="ppPartialHistory">
                                    <thead>
                                        <tr>
                                            <th>Date Paid</th>
                                            <th>Amount Paid</th>
                                            <th>Remaining Principal</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>


                            </div>
                            <div class="modal-footer">
                                <!-- Hidden Fields -->
                                <input type="hidden" id="ppPawnId" name="pawn_id">
                                <input type="hidden" id="ppInterestRate" name="interest_rate">
                                <input type="hidden" id="ppPrincipal" name="principal">

                                <input type="hidden" id="ppInterestDue" name="interest_due" value="0">
                                <input type="hidden" id="ppTotalPayable" name="total_payable" value="0">


                                <button type="submit" class="btn btn-primary">Save Partial Payment</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>






            <!-- Tubo Payment Modal -->
            <div class="modal fade" id="tuboPaymentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form id="tuboPaymentForm">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Tubo Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <!-- Pawn Details (readonly, for reference only) -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Pawner Name</label>
                                        <input type="text" class="form-control" id="tpPawnerName" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" class="form-control" id="tpCategory" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Unit</label>
                                        <input type="text" class="form-control" id="tpUnit" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date Pawned</label>
                                        <input type="text" class="form-control" id="tpDatePawned" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Current Due Date</label>
                                        <input type="text" class="form-control" id="tpDueDate" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Amount Pawned</label>
                                        <input type="text" class="form-control" id="tpAmountPawned" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Months period to pay</label>
                                        <select id="tpMonthsSelector" class="form-select">
                                            <option value="1">1 month</option>
                                            <option value="2">2 months</option>
                                            <option value="3">3 months</option>
                                            <option value="4">4 months</option>
                                        </select>

                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Notes</label>
                                        <input type="text" class="form-control" id="tpNotes" name="tpNotes">
                                    </div>
                                </div>

                                <hr>

                                <!-- New Tubo Payment Section -->
                                <h6>New Tubo Payment</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" id="tpDatePaid" name="tpDatePaid"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Months Covered</label>
                                        <input type="text" class="form-control" id="tpMonthsCovered"
                                            name="tpMonthsCovered" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Interest Amount</label>
                                        <input type="text" class="form-control" id="tpInterestAmount"
                                            name="tpInterestAmount" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">New Due Date</label>
                                        <input type="text" class="form-control" id="tpNewDueDate" name="tpNewDueDate"
                                            readonly>
                                    </div>

                                </div>

                                <hr>

                                <!-- Tubo History -->
                                <h6>Tubo History</h6>
                                <table class="table table-sm table-bordered" id="tpTuboHistory">
                                    <thead>
                                        <tr>
                                            <th>Date Paid</th>
                                            <th>Months Covered</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>


                                <h6>Partial Payment History</h6>
                                <table class="table table-sm table-bordered" id="tpPartialHistory">
                                    <thead>
                                        <tr>
                                            <th>Date Paid</th>
                                            <th>Amount Paid</th>
                                            <th>Remaining Principal</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>



                                <!-- Hidden Fields -->
                                <input type="hidden" id="tpPawnId" name="tpPawnId">
                                <input type="hidden" id="tpBranchId" name="tpBranchId">
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Tubo Payment</button>
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
                                    <div class="col-md-6">
                                        <label>Reason</label>
                                        <input type="text" class="form-control" id="forfeitReason" name="forfeitReason"
                                            required>
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
                                <th>Months</th>
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


<script>

    // pawns.php
    // DataTables AJAX init
    $(document).ready(function () {
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
                { title: "Months" },
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







    


</script>