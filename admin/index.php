<?php include('includes/header.php'); ?>

<style>
    /* Custom Styles for Professional Look */
    .card {
        border: none;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .fw-bold {
        font-size: 1.8rem;
    }

    /* Custom Border Colors */
    .border-danger { border-top: 5px solid #dc3545 !important; }
    .border-primary { border-top: 5px solid #0d6efd !important; }
    .border-warning { border-top: 5px solid #ffc107 !important; }
    .border-success { border-top: 5px solid #198754 !important; }
    .border-info { border-top: 5px solid #0dcaf0 !important; }
    .border-secondary { border-top: 5px solid #6c757d !important; }

    /* Spacing Improvements */
    .mt-4, .mt-3 { margin-top: 1.5rem !important; }
    .row.g-3 { row-gap: 1.5rem; }
</style>

<div class="container-fluid px-4">
    <h3 class="mt-4 mb-4 text-center text-dark">Admin Dashboard</h3>

    <!-- Summary Cards -->
    <div class="row g-3">
        <!-- Total Customers -->
        <div class="col-md-3">
            <div class="card shadow-sm border-danger bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">Total Customers</h5>
                    <h4 class="fw-bold text-danger">
                        <?php
                        // Get tenant_id from logged-in user session for filtering
                        $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
                        $customerQuery = "SELECT COUNT(*) AS total_customers FROM customers WHERE tenant_id = '$tenant_id'";
                        $customerResult = mysqli_query($conn, $customerQuery);
                        $customerData = mysqli_fetch_assoc($customerResult);
                        echo $customerData['total_customers'];
                        ?>
                    </h4>
                    <a href="customers.php" class="small text-danger stretched-link">View Details</a>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="col-md-3">
            <div class="card shadow-sm border-primary bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Total Categories</h5>
                    <h4 class="fw-bold text-primary">
                        <?php
                        $categoryQuery = "SELECT COUNT(*) AS total_category FROM categories WHERE tenant_id = '$tenant_id'";
                        $categoryResult = mysqli_query($conn, $categoryQuery);
                        $categoryData = mysqli_fetch_assoc($categoryResult);
                        echo $categoryData['total_category'];
                        ?>
                    </h4>
                    <a href="categories.php" class="small text-primary stretched-link">View Details</a>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-md-3">
            <div class="card shadow-sm border-warning bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">Total Products</h5>
                    <h4 class="fw-bold text-warning">
                        <?php
                        $productQuery = "SELECT COUNT(*) AS total_products FROM products WHERE tenant_id = '$tenant_id'";
                        $productResult = mysqli_query($conn, $productQuery);
                        $productData = mysqli_fetch_assoc($productResult);
                        echo $productData['total_products'];
                        ?>
                    </h4>
                    <a href="products.php" class="small text-warning stretched-link">View Details</a>
                </div>
            </div>
        </div>

        <!-- Total Admins -->
        <div class="col-md-3">
            <div class="card shadow-sm border-success bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Total Admins</h5>
                    <h4 class="fw-bold text-success">
                        <?php
                        $adminQuery = "SELECT COUNT(*) AS total_admins FROM admins WHERE tenant_id = '$tenant_id'";
                        $adminResult = mysqli_query($conn, $adminQuery);
                        $adminData = mysqli_fetch_assoc($adminResult);
                        echo $adminData['total_admins'];
                        ?>
                    </h4>
                    <a href="#" class="small text-success stretched-link">View Details</a>
                </div>
            </div>
        </div>
    </div>

  


<!-- Sales and Orders -->
<div class="row g-3 mt-4">

    <!-- Today's Sales -->
    <div class="col-md-6">
        <div class="card shadow-sm border-info bg-light">
            <a class="small text-white stretched-link" href="orders.php">View Details</a>
            <div class="card-body text-center">
                <h5 class="card-title text-info">Today's Sales</h5>
                <?php
                $todaySalesQuery = "SELECT COUNT(*) AS order_count, 
                                           COALESCE(SUM(total_amount), 0) AS total_sales, 
                                           COALESCE(SUM(due), 0) AS total_due 
                                    FROM orders 
                                    WHERE DATE(order_date) = CURDATE() AND tenant_id = '$tenant_id'";
                $todaySalesResult = mysqli_query($conn, $todaySalesQuery);
                $todaySalesData = mysqli_fetch_assoc($todaySalesResult);
                $netTodaySales = ($todaySalesData['total_sales'] ?? 0) - ($todaySalesData['total_due'] ?? 0);
                ?>
                <p class="fw-bold text-info"><?= $todaySalesData['order_count'] ?? 0; ?> Orders</p>
                <p class="text-muted">Sale Amount: <strong>TK <?= number_format($todaySalesData['total_sales'] ?? 0, 2); ?></strong></p>
                <p class="text-muted">Net Sale (After Due): <strong>TK <?= number_format($netTodaySales, 2); ?></strong></p>
            </div>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="col-md-6">
        <div class="card shadow-sm border-secondary bg-light">
            <a class="small text-white stretched-link" href="orders.php">View Details</a>
            <div class="card-body text-center">
                <h5 class="card-title text-secondary">Total Sales</h5>
                <?php
                $totalSalesQuery = "SELECT COUNT(*) AS order_count, 
                                           COALESCE(SUM(total_amount), 0) AS total_sales, 
                                           COALESCE(SUM(due), 0) AS total_due 
                                    FROM orders WHERE tenant_id = '$tenant_id'";
                $totalSalesResult = mysqli_query($conn, $totalSalesQuery);
                $totalSalesData = mysqli_fetch_assoc($totalSalesResult);
                $netTotalSales = ($totalSalesData['total_sales'] ?? 0) - ($totalSalesData['total_due'] ?? 0);
                ?>
                <p class="fw-bold text-secondary"><?= $totalSalesData['order_count'] ?? 0; ?> Orders</p>
                <p class="text-muted">Sale Amount: <strong>TK <?= number_format($totalSalesData['total_sales'] ?? 0, 2); ?></strong></p>
                <p class="text-muted">Net Sale (After Due): <strong>TK <?= number_format($netTotalSales, 2); ?></strong></p>
            </div>
        </div>
    </div>

</div>




















    <!-- Expenses -->
    <div class="row g-3 mt-4">
        <!-- Today's Expenses -->
        <div class="col-md-6">
            <div class="card shadow-sm border-danger bg-light">
                <a class="small text-white stretched-link" href="#">View Details</a>

                <div class="card-body text-center">
                    
                    <h5 class="card-title text-danger">Today's Expenses</h5>
                    
                    <?php
                    // Get tenant_id from logged-in user session for filtering
                    $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
                    $todayExpensesQuery = "SELECT SUM(amount) AS today_expenses FROM expenses WHERE DATE(expense_date) = CURDATE() AND tenant_id = '$tenant_id'";
                    $todayExpensesResult = mysqli_query($conn, $todayExpensesQuery);
                    $todayExpensesData = mysqli_fetch_assoc($todayExpensesResult);
                    ?>
                    <p class="text-muted">Expense Amount: <strong>TK <?= number_format($todayExpensesData['today_expenses'] ?? 0, 2); ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-md-6">
            <div class="card shadow-sm border-warning bg-light">
                <a class="small text-white stretched-link" href="#">View Details</a>

                <div class="card-body text-center">
                    <h5 class="card-title text-warning">Total Expenses</h5>
                    

                    <?php
                    $totalExpensesQuery = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE tenant_id = '$tenant_id'";
                    $totalExpensesResult = mysqli_query($conn, $totalExpensesQuery);
                    $totalExpensesData = mysqli_fetch_assoc($totalExpensesResult);
                    ?>
                    <p class="text-muted">Expense Amount: <strong>TK <?= number_format($totalExpensesData['total_expenses'] ?? 0, 2); ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
