 <!-- Add Pawn Amount Modal -->
            <div class="modal fade" id="addPawnAmountModal" tabindex="-1" aria-labelledby="addPawnAmountModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="addPawnAmountModalLabel">
                                Add Pawn Amount <i class="bi bi-cash-stack me-2 text-success"></i>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Pawn Details -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Owner:</strong> <span id="pawnOwner"></span><br>
                                    <strong>Item:</strong> <span id="pawnItem"></span><br>
                                    <strong>Category:</strong> <span id="pawnCategory"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Original Amount:</strong> ₱<span id="pawnOriginalAmount"></span><br>
                                    <strong>Current Due Date:</strong> <span id="pawnDueDate"></span>
                                </div>
                            </div>

                            <hr>

                            <!-- Pawn Amount Control -->
                            <div class="text-center">
                                <label class="form-label fw-bold">Add Amount</label>
                                <div class="input-group justify-content-center mb-2"
                                    style="max-width: 300px; margin: 0 auto;">
                                    <button class="btn btn-outline-secondary" type="button" id="decreaseAmount"><i
                                            class="bi bi-dash-lg"></i></button>
                                    <input type="number" class="form-control text-center" id="pawnAmountInput"
                                        value="100" min="100" step="100" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="increaseAmount"><i
                                            class="bi bi-plus-lg"></i></button>
                                </div>
                                <div>
                                    <label for="quickAmountSelect" class="form-label small">Quick Select</label>
                                    <select id="quickAmountSelect"
                                        class="form-select form-select-sm w-auto d-inline-block">
                                        <option value="100">+100</option>
                                        <option value="500">+500</option>
                                        <option value="1000">+1000</option>
                                    </select>
                                </div>
                                <p class="mt-3">
                                   New Amount: <strong> ₱<span id="pawnNewAmount"></span></strong>
                                </p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="confirmAddPawnAmount">
                                <i class="bi bi-check2-circle me-1"></i> Confirm
                            </button>
                            <button type="button" id="resetPawnAmount" class="btn btn-secondary">
                                Reset
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>