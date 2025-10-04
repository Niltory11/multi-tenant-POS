<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config/function.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedInUser']['role'] !== 'admin') {
    $_SESSION['status'] = "Access denied! Admin privileges required.";
    header("Location: login.php");
    exit();
}

include('includes/header.php'); 

$tenantInfo = getTenantInfo($_SESSION['loggedInUser']['tenant_id']);
?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                <i class="fas fa-building me-2"></i>Company Settings
                <a href="admin/index.php" class="btn btn-secondary float-end">Back to Dashboard</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>
            
            <?php if ($tenantInfo): ?>
            <div class="row">
                <div class="col-md-8">
                    <form action="tenant-settings-code.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name">Company Name *</label>
                                <input type="text" name="company_name" required class="form-control" 
                                       value="<?= htmlspecialchars($tenantInfo['company_name']); ?>" />
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_email">Company Email *</label>
                                <input type="email" name="company_email" required class="form-control" 
                                       value="<?= htmlspecialchars($tenantInfo['company_email']); ?>" />
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_phone">Company Phone</label>
                                <input type="text" name="company_phone" class="form-control" 
                                       value="<?= htmlspecialchars($tenantInfo['company_phone']); ?>" />
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="subscription_plan">Subscription Plan</label>
                                <select name="subscription_plan" class="form-select">
                                    <option value="basic" <?= $tenantInfo['subscription_plan'] == 'basic' ? 'selected' : ''; ?>>Basic</option>
                                    <option value="premium" <?= $tenantInfo['subscription_plan'] == 'premium' ? 'selected' : ''; ?>>Premium</option>
                                    <option value="enterprise" <?= $tenantInfo['subscription_plan'] == 'enterprise' ? 'selected' : ''; ?>>Enterprise</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="company_address">Company Address</label>
                                <textarea name="company_address" class="form-control" rows="3"><?= htmlspecialchars($tenantInfo['company_address']); ?></textarea>
                            </div>
                            
                            <div class="col-12 text-end">
                                <button type="submit" name="updateTenant" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Company Info
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">Company Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Tenant ID:</strong><br>
                            <code><?= $tenantInfo['tenant_id']; ?></code></p>
                            
                            <p><strong>Subscription Status:</strong><br>
                            <span class="badge bg-<?= $tenantInfo['subscription_status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?= ucfirst($tenantInfo['subscription_status']); ?>
                            </span></p>
                            
                            <p><strong>Created:</strong><br>
                            <?= date('M d, Y', strtotime($tenantInfo['created_at'])); ?></p>
                            
                            <p><strong>Last Updated:</strong><br>
                            <?= date('M d, Y H:i', strtotime($tenantInfo['updated_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <a href="admins.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-users me-1"></i>Manage Users
                            </a>
                            <a href="tenant-settings.php" class="btn btn-outline-info btn-sm w-100 mb-2">
                                <i class="fas fa-cog me-1"></i>Advanced Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Company information not found. Please contact support.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
