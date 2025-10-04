<?php include('includes/header.php'); ?>

<?php
// Display session status message if set
if (isset($_SESSION['status'])) {
    echo "<div class='alert alert-danger text-center'>{$_SESSION['status']}</div>";
    unset($_SESSION['status']); // Clear the message after showing
}
?>

<div class="py-5 mainPosBg">
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12 py-5 text-center">

                <?php alertMessage(); ?>

                <h1 class="mt-3">MultiPOS System</h1>
                <p class="lead">Your Business, Simplified</p>

                <?php if (!isset($_SESSION['loggedIn'])) : ?>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="tenant-register.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket me-2"></i>Start Free Trial
                        </a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="admin/index.php" class="btn btn-success btn-lg">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
