<!-- Edit Pawn Modal -->
<div class="modal fade" id="editPawnModal" tabindex="-1">
    
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editPawnForm" method="POST" action="pawn_edit_process.php">
        <div class="modal-header">
          <h5 class="modal-title">Edit Pawn Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="pawn_id" id="editPawnId">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Owner Name</label>
                    <input type="text" class="form-control" name="owner_name" id="editOwnerName" required>
                </div>
                <div class="col-md-6">
                    <label>Contact No.</label>
                    <input type="text" class="form-control" name="contact_no" id="editContactNo">
                </div>
                <div class="col-md-6">
                    <label>Unit</label>
                    <input type="text" class="form-control" name="unit_description" id="editUnitDesc" required>
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
                    <input type="number" step="0.01" class="form-control" name="amount_pawned" id="editAmountPawned" required>
                </div>
                <div class="col-md-6">
                    <label>Note</label>
                    <input type="text" class="form-control" name="notes" id="editNotes" required>
                </div>
                <div class="col-md-6">
                    <label>Date Pawned</label>
                    <input type="date" class="form-control" name="date_pawned" id="editDatePawned" required>
                </div>


                <!-- <div class="col-md-6">
                    <label>Status</label>
                    <select class="form-control" name="status" id="editStatus">
                        <option value="pawned">Pawned</option>
                        <option value="claimed">Claimed</option>
                        <option value="forfeited">Forfeited</option>
                    </select>
                </div> -->
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
