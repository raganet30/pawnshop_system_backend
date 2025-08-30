<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
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
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Users</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle"></i> Add User
                </button>
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="addUserForm" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Full Name</label>
                                        <input type="text" class="form-control" name="full_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Username</label>
                                        <input type="text" class="form-control" name="username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Password</label>
                                        <input type="password" class="form-control" id="add_password" name="password"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control" id="add_confirm_password"
                                            name="confirm_password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Branch</label>
                                        <select class="form-control" name="branch_id" required>
                                            <?php
                                            $branches = $pdo->query("SELECT branch_id, branch_name FROM branches")->fetchAll();
                                            foreach ($branches as $b) {
                                                echo "<option value='{$b['branch_id']}'>{$b['branch_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Role</label>
                                        <select class="form-control" name="role" required>
                                            <option value="admin">Admin</option>
                                            <option value="cashier">Cashier</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <label class="form-label d-block">Profile Picture</label>
                                        <img id="add_preview" src="../assets/img/avatar.png" class="rounded-circle mb-2"
                                            width="80" height="80">

                                        <input type="file" class="form-control" id="add_photo" name="photo">
                                        <small class="text-muted">Optional – you can add a profile picture now or
                                            later.</small>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="editUserForm" enctype="multipart/form-data" method="POST">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Full Name</label>
                                        <input type="text" class="form-control" name="full_name" id="edit_fullname"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Username</label>
                                        <input type="text" class="form-control" name="username" id="edit_username"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password" id="edit_password"
                                            placeholder="Leave blank to keep current password">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control" name="confirm_password"
                                            id="edit_confirm_password"
                                            placeholder="Leave blank to keep current password">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Branch</label>
                                        <select class="form-control" name="branch_id" id="edit_branch" required>
                                            <?php
                                            foreach ($branches as $b) {
                                                echo "<option value='{$b['branch_id']}'>{$b['branch_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Role</label>
                                        <select class="form-control" name="role" id="edit_role" required>
                                            <option value="admin">Admin</option>
                                            <option value="cashier">Cashier</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Status</label>
                                        <select class="form-control" name="status" id="edit_status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <label class="form-label d-block">Profile Picture</label>
                                        <img id="edit_preview" src="../assets/img/avatar.png"
                                            class="rounded-circle mb-2" width="80" height="80">

                                        <!-- Hidden input to keep current photo if no new file uploaded -->
                                        <input type="hidden" id="current_photo_path" name="current_photo_path">

                                        <input type="file" class="form-control" id="edit_photo" name="photo">
                                        <small class="text-muted">Leave blank to keep current photo.</small>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Users Table -->
            <div class="card">
                <div class="card-header">User List</div>
                <div class="card-body">
                    <table id="userTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Branch</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script>
    let userTable = $('#userTable').DataTable({
        ajax: {
            url: '../api/user_list.php',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'photo_path',
                className: 'text-center',
                render: function (data, type, row) {
                    if (!data) {
                        // fallback to default avatar
                        return '<img src="../assets/img/avatar.png" class="rounded-circle" width="32" height="32">';
                    }
                    // prepend ../ if needed based on your folder structure
                    return '<img src="../' + data + '" class="rounded-circle" width="32" height="32">';
                }
            },
            { data: 'full_name', className: 'text-center' },
            { data: 'username', className: 'text-center' },
            { data: 'branch_name', className: 'text-center' },
            { data: 'role', className: 'text-center' },
            {
                data: 'status', className: 'text-center',
                render: function (d) { return `<span class="badge bg-${d === 'active' ? 'success' : 'secondary'}">${d}</span>`; }
            },
            { data: 'last_login', className: 'text-center' },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (d, t, row) {
                    if (row.role === 'super_admin') {
                        return `<button class="btn btn-sm btn-secondary" disabled title="Super Admin cannot be edited">
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


    $('#userTable').on('click', '.editBtn', function () {
        let id = $(this).data('id');
        $.getJSON('../api/user_get.php', { id: id }, function (data) {
            $('#edit_user_id').val(data.user_id);
            $('#edit_fullname').val(data.full_name);
            $('#edit_username').val(data.username);
            $('#edit_branch').val(data.branch_id);
            $('#edit_role').val(data.role);
            $('#edit_status').val(data.status);
            $('#edit_password').val('');
            $('#edit_confirm_password').val('');

            // Set preview image
            let avatar = data.photo_path ? '../' + data.photo_path : '../assets/img/avatar.png';
            $('#edit_preview').attr('src', avatar);

            // Save current path in hidden input
            $('#current_photo_path').val(data.photo_path);

            $('#editUserModal').modal('show');
        });
    });


    // Strong password check function
    function isStrongPassword(pwd) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(pwd);
    }



    // Submit Edit Form
    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();

        let password = $('#edit_password').val();
        let confirmPassword = $('#edit_confirm_password').val();

        if (password || confirmPassword) {
            if (password !== confirmPassword) {
                Swal.fire('Error', 'Passwords do not match', 'error');
                return;
            }
            if (!isStrongPassword(password)) {
                Swal.fire('Error', 'Password must be 8+ chars, include uppercase, lowercase, number, special char', 'error');
                return;
            }
        }

        Swal.fire({
            title: 'Save changes?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, save'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData($('#editUserForm')[0]); // 🔑 use FormData

                $.ajax({
                    url: '../processes/user_update.php',
                    type: 'POST',
                    data: formData,
                    processData: false, // prevent jQuery from messing with FormData
                    contentType: false, // tell server we’re sending files
                    success: function (resp) {
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
                    },
                    error: function () {
                        Swal.fire('Error', 'An error occurred while saving.', 'error');
                    }
                });
            }
        });
    });



    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();

        let password = $('#add_password').val();
        let confirmPassword = $('#add_confirm_password').val();

        if (password !== confirmPassword) {
            Swal.fire('Error', 'Passwords do not match', 'error');
            return;
        }
        if (!isStrongPassword(password)) {
            Swal.fire('Error', 'Password must be 8+ chars, include uppercase, lowercase, number, special char', 'error');
            return;
        }

        Swal.fire({
            title: 'Add new user?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, add'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData(this); // includes file + inputs

                $.ajax({
                    url: '../processes/user_add.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        let res = JSON.parse(resp);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#addUserModal').modal('hide');
                            $('#addUserForm')[0].reset();
                            $('#add_preview').attr('src', '../assets/img/avatar.png'); // reset preview
                            userTable.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            }
        });

        // Preview for Add User
        $('#add_photo').on('change', function () {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#add_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                // reset to default avatar if cleared
                $('#add_preview').attr('src', '../assets/img/avatar.png');
            }
        });

    });


</script>