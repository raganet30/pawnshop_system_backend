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
                                            <input type="text" class="form-control" name="customer_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Contact No.</label>
                                            <input type="text" class="form-control" name="contact_no"
                                                placeholder="09XXXXXXXXX" required>
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
                                        <input type="text" class="form-control" name="notes" placeholder="Notes/Remarks">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Date Pawned</label>
                                        <input type="date" class="form-control" name="date_pawned"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Password/Pin</label>
                                        <input type="text" class="form-control" name="pass_key"
                                            value="" placeholder="Password/PIN for gadgets/laptop" >
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