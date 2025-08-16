<?php
// sidebar.php
if (!isset($_SESSION)) session_start();
require_once "../config/db.php";

$branchName = "Unknown Branch";
if (isset($_SESSION['user']['branch_id'])) {
    $stmt = $pdo->prepare("SELECT branch_name FROM branches WHERE branch_id = ?");
    $stmt->execute([$_SESSION['user']['branch_id']]);
    $branchName = $stmt->fetchColumn() ?? "Unknown Branch";
}

// Detect current page for active link
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['user']['role'] ?? 'guest';
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-3">
        <h6 class="mb-0"><strong><?= htmlspecialchars($branchName) ?></strong></h6>
    </div>

    <div class="list-group list-group-flush">

        <!-- Dashboard (all roles) -->
            <a href="dashboard.php" class="list-group-item <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        <!-- Dashboard (all roles) -->

        <!-- Pawns (all roles) -->
        <a href="pawns.php" class="list-group-item <?= $currentPage === 'pawns.php' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Pawns
        </a>

        <!-- Claims (all roles) -->
        <a href="claims.php" class="list-group-item <?= $currentPage === 'claims.php' ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Claims
        </a>

        <?php if ($role === 'admin' || $role === 'super_admin'): ?>
            <a href="forfeits.php" class="list-group-item <?= $currentPage === 'forfeits.php' ? 'active' : '' ?>">
                <i class="bi bi-exclamation-triangle"></i> Forfeits
            </a>

            <a href="reports.php" class="list-group-item <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Reports
            </a>

            <a href="branches.php" class="list-group-item <?= $currentPage === 'branches.php' ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i> Branches
            </a>
        <?php endif; ?>
    </div>
</div>
