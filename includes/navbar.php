<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <!-- Brand Logo -->
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fas fa-cash-register me-2"></i>
      MultiPOS System
    </a>

    <!-- Mobile Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <?php if(!isset($_SESSION['loggedIn'])) : ?>
        <li class="nav-item">
          <a class="nav-link" href="tenant-register.php">
            <i class="fas fa-building me-1"></i>Start Free Trial
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- User Section -->
      <ul class="navbar-nav">
        <?php if(isset($_SESSION['loggedIn'])) : ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
              <i class="fas fa-user"></i>
            </div>
            <span class="fw-medium"><?= $_SESSION['loggedInUser']['name']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
              <h6 class="dropdown-header">
                <i class="fas fa-user-circle me-1"></i>
                <?= $_SESSION['loggedInUser']['name']; ?>
              </h6>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <span class="dropdown-item-text">
                <i class="fas fa-envelope me-1"></i>
                <?= $_SESSION['loggedInUser']['email']; ?>
              </span>
            </li>
            <li>
              <span class="dropdown-item-text">
                <i class="fas fa-user-tag me-1"></i>
                <?= ucfirst($_SESSION['loggedInUser']['role']); ?>
              </span>
            </li>
            <?php if(isset($_SESSION['loggedInUser']['tenant_id'])): ?>
            <?php 
            $companyInfo = getCompanyDisplayInfo($_SESSION['loggedInUser']['tenant_id']);
            ?>
            <li>
              <span class="dropdown-item-text">
                <i class="fas fa-building me-1"></i>
                <strong><?= $companyInfo['name']; ?></strong>
              </span>
            </li>
            <li>
              <span class="dropdown-item-text text-muted small">
                <i class="fas fa-crown me-1"></i>
                <?= $companyInfo['subscription']; ?> Plan
              </span>
            </li>
            <?php if($_SESSION['loggedInUser']['role'] == 'admin'): ?>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="tenant-settings.php">
                <i class="fas fa-cog me-1"></i>Company Settings
              </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
              </a>
            </li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="btn btn-outline-light btn-sm" href="login.php">
            <i class="fas fa-sign-in-alt me-1"></i>Login
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>