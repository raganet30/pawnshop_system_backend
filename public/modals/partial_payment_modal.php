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

                        <!-- it should be dynamic, will be fixed later -->
                        <!-- <div class="col-md-3">
                                        <label class="form-label">Months Covered</label>
                                        <input type="text" class="form-control" id="ppMonths" readonly>
                                    </div> -->




                        <!-- Partial Payment -->

                        <div class="col-md-3">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" id="ppNotes" name="ppNotes">
                        </div>

                        <div class="col-md-3">
                            <label>Current Due Date</label>
                            <input type="text" class="form-control" id="ppDueDate" readonly>
                        </div>


                        <div class="col-md-3">
                            <label class="form-label">Enter Partial Payment</label>
                            <input type="number" class="form-control" id="ppAmount" name="partial_amount" min="1"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="ppDatePaid" name="ppDatePaid" required>
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


                    <input type="hidden" name="period_start" id="ppPeriodStart">
                    <input type="hidden" name="period_end" id="ppPeriodEnd">
                    <input type="hidden" name="months_covered" id="ppMonthsCovered">
                    <input type="hidden" name="new_due_date" id="ppNewDueDate">
                    <input type="hidden" name="interest_amount" id="ppInterestAmount">



                    <button type="submit" class="btn btn-primary">Save Partial Payment</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>