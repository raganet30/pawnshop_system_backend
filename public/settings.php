<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Restrict only super_admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    header("Location: dashboard");
    exit();
}

include '../config/db.php';
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);


// Fetch settings (get the first row)
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <h2 class="mb-4">System Settings</h2>


            <!-- Datatabel for downloaded sql backeup files, add download actions in the last columns, columns should be #, Date, SQL File, Action, etc. -->
            <!-- Datatable for SQL Backup Files -->

            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="preferences-tab" data-bs-toggle="tab"
                        data-bs-target="#preferences" type="button" role="tab" aria-controls="preferences"
                        aria-selected="false">
                        <i class="bi bi-gear"></i> System Preferences
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="backups-tab" data-bs-toggle="tab" data-bs-target="#backups"
                        type="button" role="tab" aria-controls="backups" aria-selected="true">
                        <i class="bi bi-database"></i> Database Backups
                    </button>
                </li>
            </ul>


            <div class="tab-content mt-3" id="settingsTabsContent">

                <!-- Backups Tab -->
                <div class="tab-pane fade" id="backups" role="tabpanel" aria-labelledby="backups-tab">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-database"></i> Database Backups</span>
                            <button id="generateBackupBtn" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle"></i> Generate Backup
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="backupTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>SQL File</th>
                                        <th>Size</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- PHP loop as in your current code -->
                                    <?php
                                    $backupDir = __DIR__ . "/../backups";
                                    if (is_dir($backupDir)) {
                                        $files = glob($backupDir . "/*.sql");
                                        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                                        $i = 1;
                                        foreach ($files as $file) {
                                            $filename = basename($file);
                                            $filesize = round(filesize($file) / 1024, 2) . " KB";
                                            $date = date("Y-m-d H:i:s", filemtime($file));
                                            echo "<tr>
                        <td>{$i}</td>
                        <td>{$date}</td>
                        <td>{$filename}</td>
                        <td>{$filesize}</td>
                        <td>
                          <a href='../processes/download_backup.php?file={$filename}' class='btn btn-sm btn-success'>
                            <i class='bi bi-download'></i>
                          </a>
                        </td>
                      </tr>";
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No backups found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade show active" id="preferences" role="tabpanel"
                    aria-labelledby="preferences-tab">
                    <form id="settingsForm" class="row g-4">

                        <!-- Receipt Header / Shop Name -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-shop"></i> Receipt Header</h6>
                                    <p class="text-muted small mb-2">
                                        This name will appear at the top of all receipts.
                                    </p>
                                    <input type="text" class="form-control" name="shop_name"
                                        placeholder="Enter Shop Name" required>
                                </div>
                            </div>
                        </div>

                        <!-- FB Page Name -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-facebook"></i> Facebook Page Name</h6>
                                    <p class="text-muted small mb-2">
                                        This will appear on the receipt for customer reference.
                                    </p>
                                    <input type="text" class="form-control" name="fb_page_name"
                                        placeholder="Enter Facebook Page Name">
                                </div>
                            </div>
                        </div>

                        <!-- Low Cash Alert Threshold -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-cash-coin"></i> Low Cash Alert Threshold</h6>
                                    <p class="text-muted small mb-2">
                                        System will notify you if available cash drops below this amount.
                                    </p>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="cash_threshold" min="0"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pawn Maturity Reminder -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-calendar-event"></i> Pawn Maturity Reminder
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        Send reminders this many days before maturity.
                                    </p>
                                    <input type="number" class="form-control" name="pawn_maturity_reminder_days" min="1"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Session Timeout -->
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-clock-history"></i> Session Timeout</h6>
                                    <p class="text-muted small mb-2">
                                        User will be logged out after inactivity.
                                    </p>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="session_timeout" min="20"
                                            max="480">
                                        <span class="input-group-text">minutes</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-hdd"></i> Backup Frequency</h6>
                                    <p class="text-muted small mb-2">
                                        Choose how often the system should automatically back up the database.
                                    </p>
                                    <select class="form-select" name="backup_frequency">
                                        <option value="manual">Manual Only</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-chat-dots"></i> SMS Reminders</h6>
                                    <p class="text-muted small mb-2">
                                        Enable or disable SMS reminders for pawn maturity and due dates.
                                    </p>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enable_sms"
                                            id="enable_sms">
                                        <label class="form-check-label" for="enable_sms">Enable SMS Reminders</label>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Save / Reset Buttons -->
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                            <button type="button" class="btn btn-danger" id="resetSettingsBtn">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset Database
                            </button>
                        </div>
                    </form>
                </div>


            </div>

        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Load settings from DB
        $.get("../api/get_settings.php", function (res) {
            if (res.success) {
                let s = res.data;
                $("input[name='cash_threshold']").val(s.cash_threshold);
                $("input[name='pawn_maturity_reminder_days']").val(s.pawn_maturity_reminder_days);
                $("select[name='export_format']").val(s.export_format);
                $("textarea[name='report_info']").val(s.report_info);
                $("select[name='backup_frequency']").val(s.backup_frequency);
                $("input[name='session_timeout']").val(s.session_timeout);
                $("input[name='shop_name']").val(s.shop_name);
                $("input[name='fb_page_name']").val(s.fb_page_name);
                $("select[name='backup_frequency']").val(s.backup_frequency);
                $("#enable_sms").prop("checked", s.enable_sms == 1);


            }
        }, "json");

        // Save settings
        $("#settingsForm").submit(function (e) {
            e.preventDefault();
            $.post("../processes/save_settings.php", $(this).serialize(), function (res) {
                if (res.success) {
                    Swal.fire("Saved!", "Settings updated successfully.", "success");
                } else {
                    Swal.fire("Error", res.message || "Failed to update settings", "error");
                }
            }, "json");
        });
    });




    document.getElementById('resetSettingsBtn').addEventListener('click', function () {
        if (confirm('Are you sure you want to reset the database?')) {
            fetch('../processes/reset_db.php', { // replace with your backend URL
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Settings have been reset successfully.');
                        location.reload(); // reload page to reflect changes
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An unexpected error occurred.');
                });
        }
    });




    $(document).ready(function () {
        $('#backupTable').DataTable({
            "pageLength": 10,          // show 10 rows per page
            "lengthMenu": [5, 10, 20, 50],
            "order": [[1, "desc"]]     // sort by Date descending
        });
    });



    // Generate Backup button
    document.getElementById("generateBackupBtn").addEventListener("click", function () {
        if (confirm("Generate a new database backup now?")) {
            fetch("../processes/generate_backup.php")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Success!", "Backup generated: " + data.file, "success")
                            .then(() => location.reload()); // refresh table
                    } else {
                        Swal.fire("Error", data.message || "Failed to generate backup", "error");
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "An unexpected error occurred.", "error");
                });
        }
    });




</script>