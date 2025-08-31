<?php
session_start();
require_once "../config/db.php";

// role restriction
if (!in_array($_SESSION['user']['role'], ['admin','super_admin'])) {
    header("Location: ../public/dashboard.php");
    exit();
}
?>
<?php include '../views/header.php';
// session checker
require_once "../processes/session_check.php"; 
checkSessionTimeout($pdo);
 ?>
<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <h4>Trash Bin</h4>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="btn-group" role="group" aria-label="Trash actions">
                                <button id="restoreSelected" class="btn btn-success btn-sm" disabled title="Restore selected items">
                                    <i class="fa fa-undo mr-1" aria-hidden="true"></i> Restore Selected
                                </button>
                                
                                <button id="deleteSelected" class="btn btn-danger btn-sm" disabled title="Permanently delete selected items">
                                    <i class="fa fa-trash mr-1" aria-hidden="true"></i> Delete Selected
                                </button>
                            </div>
                            <span class="ml-3">Selected: <span id="selectedCount" class="badge badge-info" style="color: black;">0</span></span>
                        </div>
                        <small class="text-muted">Tip: use the header checkbox to select all</small>
                    </div>

                   
                    <hr>
                    <table id="trashTable" class="table table-bordered table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Date Pawned</th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Panwed</th>
                                <th>Status Before Trash</th>
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
    let table = $("#trashTable").DataTable({
        "ajax": "../api/pawn_trash_list.php",
        "columns": [
            { 
                "data": "pawn_id",
                "render": function(data){
                    return `<input type="checkbox" class="row-check" value="${data}">`;
                },
                "orderable": false
            },
            { "data": "date_pawned" },
            { "data": "owner_name" },
            { "data": "unit_description" },
            { "data": "category" },
            { 
                "data": "amount_pawned",
                "render": function(data){ return "₱"+parseFloat(data).toLocaleString(); }
            },
            { "data": "status" }
        ]
    });

    // select all toggle
    $("#selectAll").on("click", function(){
        $(".row-check").prop("checked", this.checked);
    });

    // restore selected
    $("#restoreSelected").on("click", function(){
        let ids = $(".row-check:checked").map(function(){ return this.value; }).get();
        if(ids.length === 0){
            Swal.fire("No Selection", "Please select at least one record.", "warning");
            return;
        }
        Swal.fire({
            title: "Restore Items?",
            text: "These items will be restored.",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Yes, Restore"
        }).then((result)=>{
            if(result.isConfirmed){
                $.post("../processes/pawn_trash_action.php", { action: "restore", ids: ids }, function(resp){
                    if(resp.status === "success"){
                        Swal.fire("Restored!", resp.message, "success");
                        table.ajax.reload();
                    } else {
                        Swal.fire("Error", resp.message, "error");
                    }
                }, "json");
            }
        });
    });

    // delete selected
    $("#deleteSelected").on("click", function(){
        let ids = $(".row-check:checked").map(function(){ return this.value; }).get();
        if(ids.length === 0){
            Swal.fire("No Selection", "Please select at least one record.", "warning");
            return;
        }
        Swal.fire({
            title: "Permanently Delete?",
            text: "This cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete"
        }).then((result)=>{
            if(result.isConfirmed){
                $.post("../processes/pawn_trash_action.php", { action: "delete", ids: ids }, function(resp){
                    if(resp.status === "success"){
                        Swal.fire("Deleted!", resp.message, "success");
                        table.ajax.reload();
                    } else {
                        Swal.fire("Error", resp.message, "error");
                    }
                }, "json");
            }
        });
    });
});



(function($){
                        function updateSelection(){
                            var checked = $(".row-check:checked").length;
                            var total = $(".row-check").length;
                            $("#selectedCount").text(checked);
                            $("#restoreSelected, #deleteSelected").prop("disabled", checked === 0);
                            $("#selectAll").prop("checked", total > 0 && checked === total);
                        }

                        $(document).on("change", ".row-check", updateSelection);
                        $(document).on("change", "#selectAll", updateSelection);

                        // Update when DataTable redraws (works for any table draw)
                        $(document).on("draw.dt", function(){ updateSelection(); });

                        // initial
                        $(function(){ updateSelection(); });
                    })(jQuery);
</script>
