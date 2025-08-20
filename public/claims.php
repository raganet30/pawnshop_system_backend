<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";
include '../views/header.php';

$user_role  = $_SESSION['user']['role'];
$branch_id  = $_SESSION['user']['branch_id'] ?? null;

// restrict: cashier/admin only see their branch; super_admin sees all
?>
<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Claims</h4>

                <?php if ($user_role === 'super_admin'): ?>
                    <!-- Branch filter dropdown for super admin -->
                    <select id="branchFilter" class="form-select" style="width: 200px;">
                        <option value="">All Branches</option>
                        <?php
                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name ASC");
                        while ($b = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="'.$b['branch_id'].'">'.htmlspecialchars($b['branch_name']).'</option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-header">Claimed Items</div>
                <div class="card-body">
                    <table id="claimsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Date Claimed</th>
                                <th>Owner Name</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Interest Amount</th>
                                <th>Total Paid</th>
                                <th>Contact No.</th>
                                <th>Notes</th>
                                <?php if ($user_role !== 'super_admin'): ?>
                                    <th>Action</th>
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
$(document).ready(function(){
    let userRole = "<?= $user_role ?>";
    let table = $("#claimsTable").DataTable({
        "ajax": {
            "url": "claim_list.php",
            "data": function(d){
                if(userRole === "super_admin"){
                    d.branch_id = $("#branchFilter").val(); // add branch filter param
                }
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7 },
            { "data": 8 },
            { "data": 9 },
            <?php if ($user_role !== 'super_admin'): ?>
            { "data": 10, "orderable": false }
            <?php endif; ?>
        ]
    });

    <?php if ($user_role === 'super_admin'): ?>
        $("#branchFilter").on("change", function(){
            table.ajax.reload();
        });
    <?php endif; ?>
});



$(document).on("click", ".revertClaimBtn", function(e){
    e.preventDefault();
    let pawn_id = $(this).data("id");

    Swal.fire({
        title: "Revert Claim?",
        text: "This will move the item back to pawned items and deduct cash on hand.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Revert"
    }).then((result) => {
        if(result.isConfirmed){
            $.post("claim_revert_process.php", { pawn_id: pawn_id }, function(resp){
                if(resp.status === "success"){
                    Swal.fire("Reverted!", resp.message, "success");
                    $("#claimsTable").DataTable().ajax.reload();`
                } else {
                    Swal.fire("Error", resp.message, "error");
                }
            }, "json");
        }
    });
});

</script>
