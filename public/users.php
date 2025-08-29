<?php
session_start();
// Only super_admin can manage users
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit();
}
include '../views/header.php';
?>

<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Users</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle"></i> Add User
                </button>
            </div>

            <table id="userTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Branch</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addUserForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" id="add_password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" id="add_confirm_password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <?php
                                $branches = $pdo->query("SELECT branch_id, branch_name FROM branches")->fetchAll();
                                foreach ($branches as $b) {
                                    echo "<option value='{$b['branch_id']}'>{$b['branch_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required>
                                <!-- <option value="super_admin">Super Admin</option> -->
                                <option value="admin">Admin</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editUserForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password (leave blank if unchanged)</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" id="edit_confirm_password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Branch</label>
                            <select name="branch_id" id="edit_branch_id" class="form-select" required>
                                <?php
                                foreach ($branches as $b) {
                                    echo "<option value='{$b['branch_id']}'>{$b['branch_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <!-- <option value="super_admin">Super Admin</option> -->
                                <option value="admin">Admin</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function () {
        // Initialize DataTable
        let userTable = $('#userTable').DataTable({
            ajax: '../api/user_list.php',
            columns: [
                { data: 'full_name', className: 'text-center' },
                { data: 'username', className: 'text-center' },
                { data: 'branch_name', className: 'text-center' },
                { data: 'role', className: 'text-center' },
                {
                    data: 'status', className: 'text-center',
                    render: function (d) {
                        return d === 'active'
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>';
                    }
                },
                { data: 'last_login', className: 'text-center' },
                { data: 'created_at', className: 'text-center' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (d, t, row) {
                        if (row.role === 'super_admin') {
                            return `<button class="btn btn-sm btn-secondary" disabled
                    title="Super Admin cannot be edited">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>`;
                        } else {
                            return `<button class="btn btn-sm btn-secondary editBtn" data-id="${row.user_id}">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>`;
                        }
                    }

                }
            ]
        });

        // 🔹 Add User Form
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();

            let password = $('#add_password').val();
            let confirmPassword = $('#add_confirm_password').val();

            // Confirm Password validation
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Confirm Password does not match.'
                });
                return;
            }

            // SweetAlert2 confirmation
            Swal.fire({
                title: 'Add new user?',
                text: 'Are you sure all details are correct?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/user_add.php', $(this).serialize(), function (resp) {
                        try {
                            let res = JSON.parse(resp);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Added!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#addUserModal').modal('hide');
                                $('#addUserForm')[0].reset();
                                userTable.ajax.reload();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Invalid server response', 'error');
                        }
                    });
                }
            });
        });

        // 🔹 Edit Button Click
        $('#userTable').on('click', '.editBtn', function () {
            let id = $(this).data('id');

            $.getJSON('../api/user_get.php', { id: id }, function (data) {
                $('#edit_user_id').val(data.user_id);
                $('#edit_full_name').val(data.full_name);
                $('#edit_username').val(data.username);
                $('#edit_branch_id').val(data.branch_id);
                $('#edit_role').val(data.role);
                $('#edit_status').val(data.status);

                // Clear password fields
                $('#edit_password').val('');
                $('#edit_confirm_password').val('');

                $('#editUserModal').modal('show');
            });
        });

        // 🔹 Edit User Form
        $('#editUserForm').on('submit', function (e) {
            e.preventDefault();

            let password = $('#edit_password').val();
            let confirmPassword = $('#edit_confirm_password').val();

            // If either password or confirm is filled, both must match
            if ((password || confirmPassword) && password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Both password fields must match.'
                });
                return;
            }

            // Proceed with AJAX update
            Swal.fire({
                title: 'Save changes?',
                text: 'Do you want to update this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/user_update.php', $(this).serialize(), function (resp) {
                        try {
                            let res = JSON.parse(resp);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#editUserModal').modal('hide');
                                userTable.ajax.reload();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Invalid server response', 'error');
                        }
                    });
                }
            });
        });




    });

</script>