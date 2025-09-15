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
                                    <div class="col-md-6">
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

                                      <div class="col-md-6">
                                        <label>Password/PIN (for gadgets/laptop)</label>
                                        <input type="text" class="form-control" name="pass_key" id="editPassKey">
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