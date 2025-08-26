<?php
// sidebar.php (refactored role-based)
if (!isset($_SESSION)) session_start();
require_once "../config/db.php";

$branchName = "Super Admin Modules"; // Default for super admin
if (!empty($_SESSION['user']['branch_id'])) {
    $stmt = $pdo->prepare("SELECT branch_name FROM branches WHERE branch_id = ?");
    $stmt->execute([$_SESSION['user']['branch_id']]);
    $branchName = $stmt->fetchColumn() ?: $branchName;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['user']['role'] ?? 'guest';

$menuItems = [
    // Super admin specific dashboard
    ['href' => 'dashboard_super', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'roles' => ['super_admin']],
    // Admin / Cashier dashboard
    ['href' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'roles' => ['admin', 'cashier']],

    // Common items
    ['href' => 'pawns', 'icon' => 'bi-box-seam', 'label' => 'Pawns', 'roles' => '*'],
    ['href' => 'claims', 'icon' => 'bi-cash-coin', 'label' => 'Claims', 'roles' => '*'],
    ['href' => 'partial_payments', 'icon' => 'bi bi-cash-stack', 'label' => 'Partial Payments', 'roles' => '*'],
    ['href' => 'pawners', 'icon' => 'bi bi-people', 'label' => 'Pawners', 'roles' => '*'],
    


    // Admin & Super Admin
    ['href' => 'forfeits', 'icon' => 'bi-exclamation-triangle', 'label' => 'Forfeits', 'roles' => ['super_admin', 'admin']],
    ['href' => 'reports', 'icon' => 'bi-file-earmark-text', 'label' => 'Reports', 'roles' => ['super_admin', 'admin']],
        // cash ledger
    ['href' => 'ledger', 'icon' => 'bi bi-journal-text', 'label' => 'Cash Ledger', 'roles' => ['super_admin', 'admin']],

    // Admin only (trash available to admin )
    ['href' => 'trash', 'icon' => 'bi-trash', 'label' => 'Trash Bin', 'roles' => ['admin']],

    // Super admin only
    ['href' => 'branches', 'icon' => 'bi-diagram-3', 'label' => 'Branches', 'roles' => ['super_admin']],
    ['href' => 'users', 'icon' => 'bi-people', 'label' => 'Users', 'roles' => ['super_admin']],
    ['href' => 'audit_logs', 'icon' => 'bi-journal-text', 'label' => 'Audit Logs', 'roles' => ['super_admin']],
    ['href' => 'settings', 'icon' => 'bi-gear', 'label' => 'Settings', 'roles' => ['super_admin']],
];

function isAllowed($itemRoles, $currentRole) {
    if ($itemRoles === '*') return true;
    return in_array($currentRole, (array)$itemRoles, true);
}
?>
<div id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <strong><?= htmlspecialchars($branchName) ?></strong>
        </h6>
        <!-- Sidebar toggle button -->
        <button class="btn btn-sm btn-outline-light d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i> <!-- Bootstrap icon: Hamburger -->
        </button>
    </div>

    <div class="list-group list-group-flush">
        <?php foreach ($menuItems as $item): 
            if (!isAllowed($item['roles'], $role)) continue;
            $active = $currentPage === $item['href'] ? 'active' : '';
        ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="list-group-item <?= $active ?>">
                <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i> <?= htmlspecialchars($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

