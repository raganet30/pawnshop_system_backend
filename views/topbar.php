<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-0">
    <!-- Sidebar toggle button always at far left -->
    <button class="btn btn-primary " id="sidebarToggleTop">
        <i class="bi bi-list"></i>
    </button>

    <div class="container-fluid">
        <!-- Right side: Profile dropdown -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" data-bs-toggle="dropdown">
                    <img src="../assets/img/avatar.png" class="rounded-circle me-2" width="30" height="30">
                    <?php echo $_SESSION['user']['name'] ?? 'User'; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
