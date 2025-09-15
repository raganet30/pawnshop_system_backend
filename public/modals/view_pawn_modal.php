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

                                <div class="col-md-3">
                                        <label>Password/Pin(for gadgets/laptop)</label>
                                        <input type="text" class="form-control" id="viewPassKey" >
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
