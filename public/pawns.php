<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include '../views/header.php';
require_once "../config/db.php";

$userRole = $_SESSION['user']['role'];

// Fetch only pawned items
$stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE status = 'pawned' ORDER BY date_pawned DESC");
$stmt->execute();
$pawned_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex" id="wrapper">
    <?php include '../views/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../views/topbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Pawned Items</h4>
                <button class="btn btn-primary" id="addPawnBtn">
                    <i class="bi bi-plus-circle"></i> Add Pawn
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="pawnTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Owner</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Amount Pawned</th>
                                <th>Contact No.</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pawned_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['date_pawned']) ?></td>
                                <td><?= htmlspecialchars($item['owner_name']) ?></td>
                                <td><?= htmlspecialchars($item['unit_description']) ?></td>
                                <td><?= htmlspecialchars($item['category']) ?></td>
                                <td><?= htmlspecialchars($item['amount_pawned']) ?></td>
                                <td><?= htmlspecialchars($item['contact_no']) ?></td>
                                <td><?= htmlspecialchars($item['notes']) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if (in_array($userRole, ['admin','super_admin','cashier'])): ?>
                                                <li><a class="dropdown-item editPawnBtn" data-id="<?= $item['pawn_id'] ?>"><i class="bi bi-pencil-square text-primary"></i> Edit</a></li>
                                            <?php endif; ?>
                                            <?php if (in_array($userRole, ['admin','super_admin','cashier'])): ?>
                                                <li><a class="dropdown-item claimPawnBtn" data-id="<?= $item['pawn_id'] ?>"><i class="bi bi-cash-coin text-success"></i> Claim</a></li>
                                            <?php endif; ?>
                                            <?php if (in_array($userRole, ['admin','super_admin'])): ?>
                                                <li><a class="dropdown-item forfeitPawnBtn" data-id="<?= $item['pawn_id'] ?>"><i class="bi bi-x-circle text-warning"></i> Forfeit</a></li>
                                                <li><a class="dropdown-item deletePawnBtn" data-id="<?= $item['pawn_id'] ?>"><i class="bi bi-trash text-danger"></i> Delete</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<!-- Claim Modal -->
 <!-- Claim Modal -->
<div class="modal fade" id="claimPawnModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="claimPawnForm" method="POST" action="pawn_claim_process.php">
        <div class="modal-header">
          <h5 class="modal-title">Claim Pawned Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="pawn_id" id="claimPawnId">
          
          <p><strong>Owner:</strong> <span id="claimOwnerName"></span></p>
          <p><strong>Unit:</strong> <span id="claimUnit"></span></p>
          <p><strong>Date Pawned:</strong> <span id="claimDatePawned"></span></p>
          <p><strong>Amount Pawned:</strong> ₱<span id="claimAmountPawned"></span></p>
          <p><strong>Months:</strong> <span id="claimMonths"></span></p>
          <p><strong>Interest (6%/mo):</strong> ₱<span id="claimInterest"></span></p>
          <p><strong>Total to Pay:</strong> ₱<span id="claimTotal"></span></p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Confirm Claim</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php include '../views/modals_pawn.php'; ?>

<script>
$(document).ready(function() {
    $('#pawnTable').DataTable();

    // Open Edit modal
    $(".editPawnBtn").click(function() {
    let pawnId = $(this).data("id");
    $.get("pawn_get.php", { pawn_id: pawnId }, function(data) {
        if (data.error) {
            Swal.fire("Error", data.error, "error");
            return;
        }
        
        $("#editPawnId").val(data.pawn_id);
        $("#editOwnerName").val(data.owner_name);
        $("#editContactNo").val(data.contact_no);
        $("#editUnitDesc").val(data.unit_description);
        $("#editCategory").val(data.category);
        $("#editAmountPawned").val(data.amount_pawned);
        $("#editNotes").val(data.notes);
        $("#editDatePawned").val(data.date_pawned);
        $("#editPawnModal").modal("show");
    }, "json");
});


    // Claim item
    // $(".claimPawnBtn").click(function() {
    //     let pawnId = $(this).data("id");
    //     Swal.fire({
    //         title: "Claim Item?",
    //         text: "This will mark the item as claimed.",
    //         icon: "question",
    //         showCancelButton: true,
    //         confirmButtonText: "Yes, Claim"
    //     }).then((result) => {   
    //         if (result.isConfirmed) {
    //             $.post("pawn_claim_process.php", { pawn_id: pawnId, interest_amount: 0 }, function(res) {
    //                 Swal.fire("Success", "Item claimed successfully!", "success")
    //                     .then(() => location.reload());
    //             });
    //         }
    //     });
    // });

    // Forfeit item
    $(".forfeitPawnBtn").click(function() {
        let pawnId = $(this).data("id");
        Swal.fire({
            title: "Forfeit Item?",
            text: "This will mark the item as forfeited.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Forfeit"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("pawn_forfeit_process.php", { pawn_id: pawnId }, function(res) {
                    Swal.fire("Success", "Item forfeited successfully!", "success")
                        .then(() => location.reload());
                });
            }
        });
    });

    // Delete item
    $(".deletePawnBtn").click(function() {
        let pawnId = $(this).data("id");
        Swal.fire({
            title: "Delete Item?",
            text: "This action cannot be undone.",
            icon: "error",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("pawn_delete_process.php", { pawn_id: pawnId }, function(res) {
                    Swal.fire("Deleted", "Item deleted successfully!", "success")
                        .then(() => location.reload());
                });
            }
        });
    });
});
</script>


<!-- Sidebar Toggle Script -->
<script>
    const wrapper = document.getElementById("wrapper");
    document.querySelectorAll("#sidebarToggle, #sidebarToggleTop").forEach(btn => {
        btn.addEventListener("click", () => {
            wrapper.classList.toggle("toggled");
        });
    });

    // DataTables init
    $(document).ready(function () {
        $('#pawnTable').DataTable();
    });
</script>

<script>
    $("#editPawnForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "edit_pawn_process.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Updated!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });

            } else {
                Swal.fire("Error", response.message, "error");
            }
        }
    });
});

</script>

<script>
    $(document).on("click", ".claimPawnBtn", function (e) {
    e.preventDefault();
    let pawnId = $(this).data("id");

    $.getJSON("pawn_get.php", { pawn_id: pawnId }, function (data) {
        if (data.error) {
            Swal.fire("Error", data.error, "error");
            return;
        }

        // Calculate months difference (min 1 month)
        let datePawned = new Date(data.date_pawned);
        let today = new Date();
        let diffDays = Math.ceil((today - datePawned) / (1000 * 60 * 60 * 24));
        let months = Math.max(1, Math.ceil(diffDays / 30));

        let interest = data.amount_pawned * 0.06 * months;
        let total = parseFloat(data.amount_pawned) + interest;

        // Populate modal
        $("#claimPawnId").val(data.pawn_id);
        $("#claimOwnerName").text(data.owner_name);
        $("#claimUnit").text(data.unit_description);
        $("#claimDatePawned").text(data.date_pawned);
        $("#claimAmountPawned").text(parseFloat(data.amount_pawned).toFixed(2));
        $("#claimMonths").text(months);
        $("#claimInterest").text(interest.toFixed(2));
        $("#claimTotal").text(total.toFixed(2));

        $("#claimPawnModal").modal("show");
    });
});

// Handle Claim form submit
$("#claimPawnForm").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Confirm Claim?",
        text: "This will mark the item as claimed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, proceed claim!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("pawn_claim_process.php", $(this).serialize(), function (response) {
                if (response.status === "success") {
                    Swal.fire("Success", response.message, "success").then(() => {
                        $("#claimPawnModal").modal("hide");
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            }, "json");
        }
    });
});

</script>
