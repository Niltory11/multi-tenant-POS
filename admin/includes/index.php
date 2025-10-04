<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="row g-3">



    <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">Total Customers</h5>
                    <a class="small text-white stretched-link" href="customers-create.php">View Details</a>
                    <h4 class="fw-bold">
                        <?php
                        $customerQuery = "SELECT COUNT(*) AS total_customers FROM customers";
                        $customerResult = mysqli_query($conn, $customerQuery);
                        $customerData = mysqli_fetch_assoc($customerResult);
                        echo $customerData['total_customers'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    




        <!-- Statistics Cards -->
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Total Category</h5>
                    <a class="small text-white stretched-link" href="categories.php">View Details</a>
                    <h4 class="fw-bold">
                        <?php
                        $categoryQuery = "SELECT COUNT(*) AS total_category FROM categories";
                        $categoryResult = mysqli_query($conn, $categoryQuery);
                        $categoryData = mysqli_fetch_assoc($categoryResult);
                        echo $categoryData['total_category'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>

        


        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">Total Products</h5>
                    <a class="small text-white stretched-link" href="products.php">View Details</a>
                    <h4 class="fw-bold">
                        <?php
                        $productQuery = "SELECT COUNT(*) AS total_products FROM products";
                        $productResult = mysqli_query($conn, $productQuery);
                        $productData = mysqli_fetch_assoc($productResult);
                        echo $productData['total_products'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Total Admins</h5>
                    <h4 class="fw-bold">
                        <?php
                        $adminQuery = "SELECT COUNT(*) AS total_admins FROM admins";
                        $adminResult = mysqli_query($conn, $adminQuery);
                        $adminData = mysqli_fetch_assoc($adminResult);
                        echo $adminData['total_admins'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
   

    <div class="row g-3 mt-3">
        <!-- Action Cards -->
        <div class="col-xl-6 col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Create Order</h5>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="order-create.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Add Product</h5>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="products-create.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>



        <div class="col-xl-6 col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Add Expences</h5>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="add-expenses.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>









    </div>

    <div class="row g-3 mt-4">
        <!-- Orders and Sales -->
        <div class="col-md-6">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">Today Orders/Sales</h5>
                    <?php
                    $todayQuery = "SELECT COUNT(*) AS order_count, SUM(total_amount) AS total_sales 
                                   FROM orders 
                                   WHERE DATE(order_date) = CURDATE()";
                    $todayResult = mysqli_query($conn, $todayQuery);
                    $todayData = mysqli_fetch_assoc($todayResult);
                    ?>
                    <p class="fw-bold"><?= $todayData['order_count'] ?? 0; ?> Orders</p>
                    <p class="text-muted">Sale Amount: <strong>TK <?= number_format($todayData['total_sales'] ?? 0, 2); ?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-secondary">
                <div class="card-body text-center">
                    <h5 class="card-title text-secondary">Total Orders/Sales</h5>
                    <?php
                    $totalQuery = "SELECT COUNT(*) AS order_count, SUM(total_amount) AS total_sales 
                                   FROM orders";
                    $totalResult = mysqli_query($conn, $totalQuery);
                    $totalData = mysqli_fetch_assoc($totalResult);
                    ?>
                    <p class="fw-bold"><?= $totalData['order_count'] ?? 0; ?> Orders</p>
                    <p class="text-muted">Sale Amount: <strong>TK <?= number_format($totalData['total_sales'] ?? 0, 2); ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
