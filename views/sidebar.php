<?php
if (!isset($_SESSION))
    session_start();
require_once "../config/db.php";

// Get branch name
$branchName = "Super Admin Modules";
if (!empty($_SESSION['user']['branch_id'])) {
    $stmt = $pdo->prepare("SELECT branch_name FROM branches WHERE branch_id = ?");
    $stmt->execute([$_SESSION['user']['branch_id']]);
    $branchName = $stmt->fetchColumn() ?: $branchName;
}

$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$role = $_SESSION['user']['role'] ?? 'guest';

function active($page, $currentPage)
{
    return $page === $currentPage ? 'active' : '';
}
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><strong><?= htmlspecialchars($branchName) ?></strong></h6>
        <button class="btn btn-sm btn-outline-light d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="list-group list-group-flush">

        <!-- Dashboard -->
        <?php if ($role === 'super_admin'): ?>
            <a href="dashboard_super" class="list-group-item <?= active('dashboard_super', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        <?php else: ?>
            <a href="dashboard" class="list-group-item <?= active('dashboard', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        <?php endif; ?>


        <!-- Pawns -->
        <div class="d-flex justify-content-between align-items-center">
            <a href="pawns" class="list-group-item flex-grow-1 <?= active('pawns', $currentPage) ?>">
                <i class="bi bi-box-seam"></i> Pawns
            </a>
            <a class="list-group-item border-0 bg-transparent py-2 px-2" data-bs-toggle="collapse" href="#submenuPawns">
                <i class="bi bi-caret-down-fill"></i>
            </a>
        </div>
        <div class="collapse ps-3 <?= in_array($currentPage, ['pawners','partial_payments']) ? 'show' : '' ?>"
            id="submenuPawns">
             <a href="partial_payments" class="list-group-item <?= active('partial_payments', $currentPage) ?>">
                <i class="bi bi-cash-stack"></i> Partial Payments
            </a>
            <a href="pawners" class="list-group-item <?= active('pawners', $currentPage) ?>">
                <i class="bi bi-people"></i> Pawners
            </a>
        </div>

        <!-- Claims -->
        <a href="claims" class="list-group-item <?= active('claims', $currentPage) ?>">
            <i class="bi bi-cash-coin"></i> Claims
        </a>

        <!-- Forfeits -->
        <a href="forfeits" class="list-group-item <?= active('forfeits', $currentPage) ?>">
            <i class="bi bi-exclamation-triangle"></i> Forfeits
        </a>

        <!-- Reports -->
        <div class="d-flex justify-content-between align-items-center">
            <a href="reports" class="list-group-item flex-grow-1 <?= active('reports', $currentPage) ?>">
                <i class="bi bi-file-earmark-text"></i> Reports
            </a>
            <a class="list-group-item border-0 bg-transparent py-2 px-2" data-bs-toggle="collapse"
                href="#submenuReports">
                <i class="bi bi-caret-down-fill"></i>
            </a>
        </div>
        <div class="collapse ps-3 <?= in_array($currentPage, ['ledger', 'audit_logs']) ? 'show' : '' ?>"
            id="submenuReports">
            <a href="ledger" class="list-group-item <?= active('ledger', $currentPage) ?>">
                <i class="bi bi-journal-text"></i> Cash Ledger
            </a>
           
            <?php if (in_array($role, ['super_admin', 'admin'])): ?>
                <a href="audit_logs" class="list-group-item <?= active('audit_logs', $currentPage) ?>">
                    <i class="bi bi-card-list"></i> Audit Logs
                </a>
            <?php endif; ?>
        </div>
                <!-- cash on hand -->
        <?php if (in_array($role, ['admin', 'cashier'])): ?>
            <a href="cash_on_hand" class="list-group-item <?= active('cash_on_hand', $currentPage) ?>">
                <i class="bi bi-wallet-fill"></i> Cash on Hand
            </a>
        <?php endif; ?>

         <?php if (in_array($role, ['admin'])): ?>
            <a href="trash" class="list-group-item <?= active('trash', $currentPage) ?>">
        <i class="bi bi-trash"></i> Trash Items
            </a>
        <?php endif; ?>



        <!-- Super Admin only -->
        <?php if ($role === 'super_admin'): ?>
            <a href="branches" class="list-group-item <?= active('branches', $currentPage) ?>">
                <i class="bi bi-diagram-3"></i> Branches
            </a>
            <a href="users" class="list-group-item <?= active('users', $currentPage) ?>">
                <i class="bi bi-people"></i> Users
            </a>

            <!-- Settings -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="settings" class="list-group-item flex-grow-1 <?= active('settings', $currentPage) ?>">
                    <i class="bi bi-gear"></i> Settings
                </a>
                
            </div>

        <?php endif; ?>

    </div>
</div>