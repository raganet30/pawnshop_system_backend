<?php
session_start();
// Redirect if not logged in
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
        <!-- Top Navigation -->
        <?php include '../views/topbar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Pawners</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPawnerModal">
                    <i class="bi bi-plus-circle"></i> Add Pawner
                </button>
            </div>

            <!-- Add Pawner Modal -->
            <div class="modal fade" id="addPawnerModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="addPawnerForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Pawner</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                
                                <div class="mb-3">
                                    <label for="add_fullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="add_fullname" name="full_name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="add_contact" class="form-label">Contact No.</label>
                                    <input type="number" class="form-control" id="add_contact" name="contact_no"
                                        minlength="11" maxlength="11" required
                                        oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11)">
                                </div>

                                <div class="mb-3">
                                    <label for="add_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="add_address" name="address" required></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Edit Pawner Modal -->
            <div class="modal fade" id="editPawnerModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="editPawnerForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Pawner</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id">

                                <div class="mb-3">
                                    <label for="edit_fullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="edit_fullname" name="fullname" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_contact" class="form-label">Contact No.</label>
                                    <input type="number" class="form-control" id="edit_contact" name="contact"
                                        minlength="11" maxlength="11" required
                                        oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11)">
                                </div>

                                <div class="mb-3">
                                    <label for="edit_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="edit_address" name="address" required></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Pawners</div>
                <div class="card-body">
                    <table id="pawnerTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Contact No.</th>
                                <th>Address</th>
                                <th>Date Created</th>
                                <?php if (in_array($_SESSION['user']['role'], ['admin'])): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
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
    // DataTables AJAX init
    $(document).ready(function () {
        let table = $('#pawnerTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '../api/customer_list.php',
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'full_name' },
                { data: 'contact_no' },
                { data: 'address' },
                { data: 'created_at' },
                <?php if (in_array($_SESSION['user']['role'], ['admin'])): ?>
                                                {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                        <button class="btn btn-sm btn-secondary editBtn" data-id="${row.customer_id}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    `;
                        }
                    }
            <?php endif; ?>
            ]
        });

        // 🔹 Open Edit Modal
        $('#pawnerTable').on('click', '.editBtn', function () {
            let id = $(this).data('id');

            $.getJSON('../api/customer_get.php', { id: id }, function (data) {
                $('#edit_id').val(data.customer_id);
                $('#edit_fullname').val(data.full_name);
                $('#edit_contact').val(data.contact_no);
                $('#edit_address').val(data.address);

                $('#editPawnerModal').modal('show');
            });
        });

        // 🔹 Submit Edit Form with SweetAlert2
        $('#editPawnerForm').on('submit', function (e) {
            e.preventDefault();

            let contact = $('#edit_contact').val().trim();

            // Validate: must be exactly 11 digits
            if (!/^\d{11}$/.test(contact)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Contact Number',
                    text: 'Contact number must be exactly 11 digits (e.g. 09123456789)',
                });
                return;
            }

            Swal.fire({
                title: 'Save changes?',
                text: "Do you want to update this pawner's info?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/customer_update.php', $(this).serialize(), function (response) {
                        try {
                            let res = JSON.parse(response);

                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: res.message || 'Something went wrong.'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid response from server.'
                            });
                        }

                        $('#editPawnerModal').modal('hide');
                        table.ajax.reload();
                    });
                }
            });
        });


        // 🔹 Add Pawner Form with SweetAlert2
        // 🔹 Submit Add Forms

        $('#addPawnerForm').on('submit', function (e) {
            e.preventDefault();

            let contact = $('#add_contact').val().trim();

            // Validate: must be exactly 11 digits
            if (!/^\d{11}$/.test(contact)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Contact Number',
                    text: 'Contact number must be exactly 11 digits (e.g. 09123456789)',
                });
                return;
            }

            Swal.fire({
                title: 'Add New Pawner?',
                text: "Do you want to save this pawner?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('../processes/customer_add.php', $(this).serialize(), function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'New pawner has been added successfully.'
                        });
                        $('#addPawnerModal').modal('hide');
                        $('#addPawnerForm')[0].reset();
                        table.ajax.reload();
                    });
                }
            });
        });

    });





</script>