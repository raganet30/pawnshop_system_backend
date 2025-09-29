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


                                    <!-- Original Amount Pawned -->
                                     <!-- <div class="col-md-3">
                                        <label id="tpOriginalAmountLabel" class="form-label">Original Amount Pawned</label>
                                        <input type="text" class="form-control" id="tpOriginalAmountPawned" readonly>
                                    </div> -->
                                    <!-- Original Amount Pawned -->

                                    <!-- Remaining Amount Pawned -->
                                    <div class="col-md-3">
                                        <label id="tpRemainingAmountLabel" class="form-label">Amount Pawned</label>
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
                                            <th>Months Period</th>
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
                                <!-- hiddedn field for original amount pawned -->
                                <input type="hidden" name="original_amount_pawned" id="tpOriginalAmountPawned">
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Tubo Payment</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>