<?php
session_start();
// Restrict only super_admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit();
}
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);

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
                <h2>Branches</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    <i class="bi bi-plus-circle"></i> Add Branch
                </button>
            </div>

            <!-- Branches Table -->
            <table id="branchTable" class="table table-bordered table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Interest Rate (%)</th>
                        <th>Cash on Hand</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addBranchForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="branch_phone" pattern="\d{11}" placeholder="09123456789" minlength="11" maxlength="11" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="branch_address" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate (%)</label>
                            <input type="number" step="0.01" class="form-control" name="interest_rate" required>
                        </div>
                        <!-- You can add more fields here if needed -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editBranchForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="branch_id" id="edit_branch_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" id="edit_branch_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="branch_phone" id="edit_branch_phone" pattern="\d{11}" placeholder="09123456789" minlength="11" maxlength="11" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="branch_address" id="edit_branch_address" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate (%)</label>
                            <input type="number" step="0.01" class="form-control" name="interest_rate" id="edit_interest_rate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cash on Hand</label>
                            <input type="number" step="0.01" class="form-control" name="cash_on_hand" id="edit_cash_on_hand" required readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function () {
        // Load Branches
        let branchTable = $('#branchTable').DataTable({
            ajax: '../api/branch_list.php',
            columns: [
                {
                    title: "#",
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // auto-increment numbering
                    },
                    className: "text-center"
                },
                { data: 'branch_name', className: 'text-center' },
                { data: 'branch_address', className: 'text-center' },
                { data: 'branch_phone', className: 'text-center' },
                {
                    data: 'status', className: 'text-center',
                    render: function (data) {
                        if (data === 'active') {
                            return `<span class="badge bg-success">Active</span>`;
                        } else {
                            return `<span class="badge bg-danger">Inactive</span>`;
                        }
                    }
                },
                { data: 'interest_rate', className: 'text-center' },
                {
                    data: 'cash_on_hand',
                    className: 'text-center',
                    render: function (data) {
                        // Format number with comma separator
                        let formatted = Number(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                        // Color badge based on value
                        let badgeClass = Number(data) > 100000 ? 'bg-success' : (Number(data) > 5000 ? 'bg-info' : 'bg-danger');

                        return `<span class="badge ${badgeClass}">${formatted}</span>`;
                    }
                },
                { data: 'created_at', className: 'text-center' },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<i class="bi bi-pencil-square editBtn" data-id="${row.branch_id}" title="Edit Branch" style="cursor: pointer;"></i>`;
                    }

                }
            ]
        });

        // Add Branch
        $('#addBranchForm').on('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Add new branch?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/branch_add.php', $(this).serialize(), function (response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire('Added!', res.message, 'success');
                            $('#addBranchModal').modal('hide');
                            $('#addBranchForm')[0].reset();
                            branchTable.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    });
                }
            });
        });

        // Edit button click
        $('#branchTable').on('click', '.editBtn', function () {
            let id = $(this).data('id');
            $.getJSON('../processes/branch_get.php', { id: id }, function (data) {
                $('#edit_branch_id').val(data.branch_id);
                $('#edit_branch_name').val(data.branch_name);
                $('#edit_branch_address').val(data.branch_address);
                $('#edit_branch_phone').val(data.branch_phone);
                $('#edit_status').val(data.status);
                $('#edit_interest_rate').val(data.interest_rate);
                $('#edit_cash_on_hand').val(data.cash_on_hand);
                $('#editBranchModal').modal('show');
            });
        });

        // Update Branch
        $('#editBranchForm').on('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Update branch?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/branch_update.php', $(this).serialize(), function (response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire('Updated!', res.message, 'success');
                            $('#editBranchModal').modal('hide');
                            branchTable.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    });
                }
            });
        });

    });
</script>