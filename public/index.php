<?php
session_start();

// If user already logged in, go directly to dashboard
if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
    header("Location: ../public/dashboard.php");
    exit();
}

include '../views/header.php';

?>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg" style="width: 350px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="../assets/img/pawnshop icon.png" alt="Pawnshop Logo" style="max-width:80px;">
                <h5 class="mt-2">Pawnshop Management System</h5>
            </div>

            <?php if (!empty($_SESSION['error'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: '<?php echo $_SESSION['error']; ?>',
                        });
                    });
                </script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['expired'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Session Expired',
                            text: '<?php echo $_SESSION['expired']; ?>',
                        });
                    });
                </script>
                <?php unset($_SESSION['expired']); ?>
            <?php endif; ?>


            <form id="loginForm" method="POST" action="../processes/login_process.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none">Forgot password? Contact Admin.</a>
            </form>
        </div>
    </div>
</div>
<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/sweetalert2.all.min.js"></script>
<script src="../assets/js/chart.min.js"></script>
<script src="../assets/js/app.js"></script>