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
                            <?php
                            $backupDir = __DIR__ . "/../backups"; // root-level backups folder // adjust path
                            if (is_dir($backupDir)) {
                                $files = glob($backupDir . "/*.sql");
                                usort($files, function ($a, $b) {
                                    return filemtime($b) - filemtime($a); // newest first
                                });
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
                                    <i class='bi bi-download'></i> Download
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




            <form id="settingsForm" class="row g-3">

                <!-- Low Cash Alert Threshold -->
                <div class="col-md-3">
                    <label class="form-label">Low Cash Alert Threshold (₱)</label>
                    <input type="number" class="form-control" name="cash_threshold" min="0" required>
                </div>

                <!-- Pawn Maturity Reminder -->
                <div class="col-md-3">
                    <label class="form-label">Pawn Maturity Reminder (days)</label>
                    <input type="number" class="form-control" name="pawn_maturity_reminder_days" min="1" required>
                </div>

                <!-- Default Export Format -->
                <!-- <div class="col-md-4">
                    <label class="form-label">Default Export Format</label>
                    <select class="form-select" name="export_format">
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div> -->



                <!-- Backup Frequency -->
                <!-- <div class="col-md-3">
                    <label class="form-label">Backup Frequency</label>
                    <select class="form-select" name="backup_frequency">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="manual">Manual only</option>
                    </select>
                </div> -->

                <!-- Session Timeout -->
                <div class="col-md-3">
                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" min="5"
                        max="120">
                </div>
                <!-- Report Header/Footer -->
                <!-- <div class="col-md-4">
                    <label class="form-label">Report Header/Footer Info</label>
                    <textarea class="form-control" name="report_info" rows="2"></textarea>
                </div> -->


                <!-- Save Button -->
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Settings
                    </button>


                    <!-- Reset Button -->
                    <button type="button" class="btn btn-danger" id="resetSettingsBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Database
                    </button>
                </div>
            </form>
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