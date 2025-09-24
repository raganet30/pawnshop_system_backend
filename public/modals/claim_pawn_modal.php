<!-- Claim Pawn Modal -->
<div class="modal fade" id="claimPawnModal" tabindex="-1" aria-labelledby="claimPawnModalLabel" aria-hidden="true">
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

                            <input type="hidden" class="form-control" id="claimInterestValue" name="claimInterestValue">

                        </div>
                        <div class="col-md-3">
                            <label for="claimPenalty">Penalty (optional)</label>
                            <input type="number" step="0.01" class="form-control" id="claimPenalty" name="claimPenalty"
                                placeholder="Enter penalty amount">
                            <input type="number" step="0.01" class="form-control" id="claimPenaltyHidden"
                                name="claimPenaltyHidden" hidden>
                        </div>
                        <div class="col-md-3">
                            <label>Total Payment</label>
                            <input type="text" class="form-control" id="claimTotal" readonly>

                            <input type="number" step="0.01" class="form-control" id="claimTotalValue"
                                name="claimTotalValue" hidden>
                        </div>

                        <div class="col-md-3">
                            <label>Current Due Date</label>
                            <input type="text" class="form-control" id="claimDueDate" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Date Claimed</label>
                            <input type="date" class="form-control" id="claimDate" name="claimDate" required>
                        </div>


                
                       <!-- DISPLAY IF ROLE IS ADMIN ONLY - prepared only  -->
                        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <!-- Interest Options -->
                        <div class="col-md-3">
                            <label for="interestOption">Interest Computation</label>
                            <select id="interestOption" class="form-control" name="interestOption">
                                <option value="auto" selected>Compute Automatically</option>
                                <option value="waive">Waive Interest</option>
                                <option value="custom">Custom Interest Amount</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-3">
                            <label for="claimNotes">Notes</label>
                            <textarea class="form-control" id="claimNotes" name="claimNotes" rows="2"></textarea>
                        </div>


                        <!-- Custom Interest Input (hidden by default) -->
                        <div class="col-md-3" id="customInterestWrapper" style="display:none;">
                            <label for="customInterest">Custom Interest Amount</label>
                            <input type="number" id="customInterest" name="customInterest" class="form-control" min="0"
                                step="0.01" placeholder="Enter interest">
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
                    <h6 class="mt-3">Partial Payments History</h6>
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



                    <hr>

                    <!-- Claimant Photo Capture -->
                    <div class="row">
                        <div class="col-md-6">
                            <label>Live Camera</label>
                            <video id="claimCameraStream" width="100%" height="240" autoplay playsinline></video>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="capturePhotoBtn">
                                <i class="bi bi-camera"></i> Capture Photo
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label>Captured Photo</label>
                            <canvas id="capturedCanvas" width="320" height="240" class="border d-block mb-2"></canvas>
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