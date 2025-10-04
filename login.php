<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('includes/header.php'); 

if (isset($_SESSION['loggedIn'])) {
    // Redirect if already logged in
    header("Location: index.php");
    exit();
}
?>

<div class="py-5 bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow rounded-4">

                    <!-- Display any session error message -->
                    <?php if (isset($_SESSION['status'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['status']; ?>
                            <?php unset($_SESSION['status']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-5">
                        <h4 class="text-dark mb-3">Sign into your POS System</h4>
                        <form action="login-code.php" method="POST">
                            <div class="mb-3">
                                <label>Enter Email Id</label>
                                <input type="email" name="email" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label>Enter Password</label>
                                <input type="password" name="password" class="form-control" required />
                            </div>
                            <div class="my-3">
                                <button type="submit" name="loginBtn" class="btn btn-primary w-100 mt-2">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
