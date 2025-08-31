<?php
// --- Session User ---
$userId = $_SESSION['user']['id'];
$sessionBranchId = $_SESSION['user']['branch_id'];
$userRole = $_SESSION['user']['role'];



// --- Fetch User Details (with avatar) ---
$userStmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :id LIMIT 1");
$userStmt->execute([':id' => $userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// DB already has something like: "uploads/avatars/user_1_123.jpg"
$avatarPath = !empty($user['photo_path']) ? "../" . $user['photo_path'] : "../assets/img/avatar.png";

// Extra check if file really exists
if (!empty($user['photo_path']) && !file_exists("../" . $user['photo_path'])) {
    $avatarPath = "../assets/img/avatar.png";
}





// --- Settings ---
$settingsQuery = $pdo->query("SELECT pawn_maturity_reminder_days FROM settings LIMIT 1");
$settings = $settingsQuery->fetch(PDO::FETCH_ASSOC);
$reminderDays = $settings ? (int) $settings['pawn_maturity_reminder_days'] : 7;

// --- Build WHERE clause depending on role ---
$params = [];
if ($userRole === 'super_admin') {
    $where = "WHERE p.status = 'pawned' AND p.is_deleted = 0";
    if (!empty($selected_branch_id)) {
        $where .= " AND p.branch_id = ?";
        $params[] = $selected_branch_id;
    }
} else {
    $where = "WHERE p.status = 'pawned' AND p.is_deleted = 0 AND p.branch_id = ?";
    $params[] = $sessionBranchId;
}

// --- Nearing Maturity ---
$sqlNearing = "
    SELECT 
        p.pawn_id,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.date_pawned,
        DATE_ADD(p.date_pawned, INTERVAL 2 MONTH) AS maturity_date,
        c.full_name AS customer_name,
        b.branch_name
    FROM pawned_items p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN branches b ON p.branch_id = b.branch_id
    $where
      AND DATE_ADD(p.date_pawned, INTERVAL 2 MONTH) 
            BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
    ORDER BY maturity_date ASC
";
$stmtNearing = $pdo->prepare($sqlNearing);
$stmtNearing->execute([...$params, $reminderDays]);
$nearing = $stmtNearing->fetchAll(PDO::FETCH_ASSOC);

// --- Overdue Items ---
$sqlOverdue = "
    SELECT 
        p.pawn_id,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.date_pawned,
        DATE_ADD(p.date_pawned, INTERVAL 2 MONTH) AS maturity_date,
        c.full_name AS customer_name,
        b.branch_name
    FROM pawned_items p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN branches b ON p.branch_id = b.branch_id
    $where
      AND DATE_ADD(p.date_pawned, INTERVAL 2 MONTH) < CURDATE()
    ORDER BY maturity_date ASC
";
$stmtOverdue = $pdo->prepare($sqlOverdue);
$stmtOverdue->execute($params);
$overdue = $stmtOverdue->fetchAll(PDO::FETCH_ASSOC);

$notifCount = count($nearing) + count($overdue);
?>



<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-0">
    <!-- Sidebar toggle -->
    <button class="btn btn-link p-0 ms-2" id="sidebarToggleTop">
        <i class="bi bi-list fs-2"></i>
    </button>

    <div class="container-fluid">
        <ul class="navbar-nav ms-auto align-items-center">

            <!-- Notifications -->
            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4"></i>
                    <?php if ($notifCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $notifCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2" aria-labelledby="notifDropdown"
                    style="min-width: 300px; max-width: 500px; max-height: 400px; overflow-y: auto; white-space: normal;">
                    <li class="dropdown-header fw-bold">Notifications</li>

                    <!-- Overdue -->
                    <?php foreach ($overdue as $item): ?>
                        <li>
                            <a class="dropdown-item text-wrap text-danger d-flex align-items-start"
                                href="../public/pawns?id=<?= $item['pawn_id'] ?>" style="white-space: normal;">
                                <i class="bi bi-exclamation-triangle me-2 fs-5 mt-1"></i>
                                <div>
                                    <strong><?= htmlspecialchars($item['customer_name']) ?></strong>
                                    <?php if ($userRole === 'super_admin'): ?>
                                        (<?= htmlspecialchars($item['branch_name']) ?>)
                                    <?php endif; ?><br>
                                    <?= htmlspecialchars($item['unit_description']) ?>
                                    (<?= htmlspecialchars($item['category']) ?>)<br>
                                    Amount: ₱<?= number_format($item['amount_pawned'], 2) ?><br>
                                    Due: <?= date("M d, Y", strtotime($item['maturity_date'])) ?>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <!-- Nearing -->
                    <?php foreach ($nearing as $item): ?>
                        <li>
                            <a class="dropdown-item text-wrap d-flex align-items-start"
                                href="../public/pawns?id=<?= $item['pawn_id'] ?>" style="white-space: normal;">
                                <i class="bi bi-clock-history text-warning me-2 fs-5 mt-1"></i>
                                <div>
                                    <strong><?= htmlspecialchars($item['customer_name']) ?></strong>
                                    <?php if ($userRole === 'super_admin'): ?>
                                        (<?= htmlspecialchars($item['branch_name']) ?>)
                                    <?php endif; ?><br>
                                    <?= htmlspecialchars($item['unit_description']) ?>
                                    (<?= htmlspecialchars($item['category']) ?>)<br>
                                    Amount: ₱<?= number_format($item['amount_pawned'], 2) ?><br>
                                    Due: <?= date("M d, Y", strtotime($item['maturity_date'])) ?>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <?php if ($notifCount == 0): ?>
                        <li><span class="dropdown-item-text text-muted small">No notifications</span></li>
                    <?php endif; ?>
                </ul>
            </li>


            <!-- User Profile -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                    data-bs-toggle="dropdown">
                    <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-circle me-2" width="32" height="32">
                    <?= htmlspecialchars($user['full_name'] ?? 'User') ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">Edit
                            Profile</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#profilePassword">Change password
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="logout">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>


<!-- Edit Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="profileForm" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name"
                            value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username"
                            value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="photo" id="edit_profile_photo">
                        <small class="text-muted">Leave blank to keep current photo.</small>

                        <!-- Preview -->
                        <div class="mt-2 text-center">
                            <img id="edit_profile_preview" src="<?php echo !empty($user['photo_path'])
                                ? '../' . htmlspecialchars($user['photo_path'])
                                : '../assets/img/avatar.png'; ?>" alt="Profile Preview" class="img-thumbnail"
                                style="max-width: 120px; border-radius: 50%;">
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Change Password Modal -->
<div class="modal fade" id="profilePassword" tabindex="-1" aria-labelledby="profilePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="passwordForm" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilePasswordLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Old Password</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>




<script>
    // Submit profile update
    document.querySelector("form").addEventListener("submit", function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Confirm before submitting
        Swal.fire({
            title: 'Save changes?',
            text: "Do you want to update your profile?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("../processes/update_profile.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Updated!",
                                text: data.message
                            }).then(() => {
                                location.reload(); // refresh to show new session values
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: data.message
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: "error",
                            title: "Request Failed",
                            text: err
                        });
                    });
            }
        });
    });

    // ✅ Preview for Edit User (vanilla JS only)
    document.getElementById("edit_profile_photo").addEventListener("change", function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById("edit_profile_preview");

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // fallback if cleared
            preview.src = "../assets/img/avatar.png";
        }
    });


    //change user password
    document.getElementById("passwordForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const newPass = form.querySelector("input[name='new_password']").value;
        const confirmPass = form.querySelector("input[name='confirm_password']").value;

        // Strong password validation
        const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        if (!strongPassword.test(newPass)) {
            Swal.fire("Weak Password", "Password must be at least 8 chars with uppercase, lowercase, number, and special char.", "error");
            return;
        }

        if (newPass !== confirmPass) {
            Swal.fire("Mismatch", "New password and Confirm password do not match.", "error");
            return;
        }

        Swal.fire({
            title: "Change password?",
            text: "Do you want to update your password?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("../processes/password_change.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire("Updated!", data.message, "success")
                                .then(() => {
                                    form.reset();
                                    bootstrap.Modal.getInstance(document.getElementById('profilePassword')).hide();
                                });
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    })
                    .catch(err => {
                        Swal.fire("Error", err, "error");
                    });
            }
        });
    });
</script>