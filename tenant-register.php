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
            <div class="col-md-8">
                <div class="card shadow rounded-4">
                    <!-- Display any session error message -->
                    <?php if (isset($_SESSION['status'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['status']; ?>
                            <?php unset($_SESSION['status']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-primary mb-3">Start Your Business with MultiPOS</h2>
                            <p class="text-muted">Create your company account and get started with our powerful POS system</p>
                        </div>
                        
                        <form action="tenant-register-code.php" method="POST">
                            <!-- Company Information -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">Company Information</h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company_name">Company Name *</label>
                                    <input type="text" name="company_name" required class="form-control" 
                                           placeholder="Enter your company name" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company_email">Company Email *</label>
                                    <input type="email" name="company_email" required class="form-control" 
                                           placeholder="Enter company email" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company_phone">Company Phone</label>
                                    <input type="text" name="company_phone" class="form-control" 
                                           placeholder="Enter company phone" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="subscription_plan">Subscription Plan *</label>
                                    <select name="subscription_plan" class="form-select" required>
                                        <option value="">Select Plan</option>
                                        <option value="basic">Basic - $29/month</option>
                                        <option value="premium">Premium - $59/month</option>
                                        <option value="enterprise">Enterprise - $99/month</option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="company_address">Company Address</label>
                                    <textarea name="company_address" class="form-control" rows="3" 
                                              placeholder="Enter company address"></textarea>
                                </div>
                            </div>

                            <!-- Admin Account Information -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">Admin Account</h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="admin_name">Admin Name *</label>
                                    <input type="text" name="admin_name" required class="form-control" 
                                           placeholder="Enter admin full name" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="admin_email">Admin Email *</label>
                                    <input type="email" name="admin_email" required class="form-control" 
                                           placeholder="Enter admin email" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="admin_password">Admin Password *</label>
                                    <input type="password" name="admin_password" required class="form-control" 
                                           placeholder="Enter admin password" minlength="6" />
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="admin_phone">Admin Phone</label>
                                    <input type="text" name="admin_phone" class="form-control" 
                                           placeholder="Enter admin phone" />
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" name="registerTenant" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-building me-2"></i>Create Company Account
                                    </button>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <p class="text-muted">
                                        Already have an account? 
                                        <a href="login.php" class="text-primary">Login here</a>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
