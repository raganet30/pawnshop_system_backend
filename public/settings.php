<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include '../config/db.php';
include '../views/header.php';

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

            <form id="settingsForm" class="row g-3">

                <!-- Low Cash Alert Threshold -->
                <div class="col-md-4">
                    <label class="form-label">Low Cash Alert Threshold (₱)</label>
                    <input type="number" class="form-control" name="cash_threshold" min="0" required>
                </div>

                <!-- Pawn Maturity Reminder -->
                <div class="col-md-4">
                    <label class="form-label">Pawn Maturity Reminder (days before)</label>
                    <input type="number" class="form-control" name="maturity_days" min="1" required>
                </div>

                <!-- Default Export Format -->
                <div class="col-md-4">
                    <label class="form-label">Default Export Format</label>
                    <select class="form-select" name="export_format">
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>

               

                <!-- Backup Frequency -->
                <div class="col-md-4">
                    <label class="form-label">Backup Frequency</label>
                    <select class="form-select" name="backup_frequency">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="manual">Manual only</option>
                    </select>
                </div>

                <!-- Session Timeout -->
                <div class="col-md-4">
                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" min="5">
                </div>
                 <!-- Report Header/Footer -->
                <div class="col-md-4">
                    <label class="form-label">Report Header/Footer Info</label>
                    <textarea class="form-control" name="report_info" rows="2"></textarea>
                </div>


                <!-- Save Button -->
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Settings
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
                $("input[name='maturity_days']").val(s.maturity_days);
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

</script>