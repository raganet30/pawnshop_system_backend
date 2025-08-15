<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include '../views/header.php';
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">Pawned Items</h1>
                <a href="add_pawn.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Pawn
                </a>
            </div>

            <!-- Pawned Items Table -->
            <div class="card">
                <div class="card-body">
                    <table id="pawnedItemsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date Pawned</th>
                                <th>Unit</th>
                                <th>Category</th>
                                <th>Owner</th>
                                <th>Contact No.</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once "../config/db.php";
                            $stmt = $pdo->query("SELECT pawn_id, date_pawned, unit_description, category, owner_name, contact_no, notes, status 
                                                 FROM pawned_items WHERE status = 'pawned'
                                                 ORDER BY date_pawned DESC");

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['date_pawned']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['unit_description']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['owner_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['contact_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['notes']) . "</td>";

                                // Status badge
                                $badgeClass = ($row['status'] === 'pawned') ? 'bg-info' :
                                              (($row['status'] === 'claimed') ? 'bg-success' : 'bg-secondary');
                                echo "<td><span class='badge $badgeClass'>" . ucfirst($row['status']) . "</span></td>";

                                // Actions dropdown
                                echo "<td class='text-center'>
                                        <div class='dropdown'>
                                            <button class='btn btn-sm btn-primary dropdown-toggle' type='button' 
                                                    id='actionMenu{$row['pawn_id']}' data-bs-toggle='dropdown' aria-expanded='false'>
                                                <i class='bi bi-three-dots'></i>
                                            </button>
                                            <ul class='dropdown-menu' aria-labelledby='actionMenu{$row['pawn_id']}'>
                                                <li>
                                                    <a class='dropdown-item' href='edit_pawn.php?id={$row['pawn_id']}'>
                                                        <i class='bi bi-pencil-square me-2 text-warning'></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class='dropdown-item' href='claim_pawn.php?id={$row['pawn_id']}'>
                                                        <i class='bi bi-cash-coin me-2 text-success'></i>Claim
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class='dropdown-item text-danger' href='delete_pawn.php?id={$row['pawn_id']}' 
                                                       onclick=\"return confirm('Are you sure you want to delete this pawn?');\">
                                                        <i class='bi bi-trash me-2'></i>Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                      </td>";

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>

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
        $('#pawnedItemsTable').DataTable({
            responsive: true
        });
    });
</script>


