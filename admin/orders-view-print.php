<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">
                <p>Served by: <?= $_SESSION['loggedInUser']['name']; ?></p>
                <a href="orders.php" class="btn btn-danger btn-sm float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php
                if (!isset($_GET['track'])) {
                    echo '<div class="alert alert-danger">No tracking number provided.</div>';
                    exit;
                }

                $trackingNo = validate($_GET['track']);
                if (empty($trackingNo)) {
                    echo '<div class="alert alert-danger">Please provide a valid tracking number.</div>';
                    exit;
                }

                // Get tenant_id from logged-in user session for filtering
                $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
                
                // Fetch order details
                $orderQuery = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email 
                               FROM orders o 
                               JOIN customers c ON o.customer_id = c.id 
                               WHERE o.tracking_no = '$trackingNo' AND o.tenant_id = '$tenant_id' AND c.tenant_id = '$tenant_id'";
                $orderResult = mysqli_query($conn, $orderQuery);

                if (!$orderResult || mysqli_num_rows($orderResult) == 0) {
                    echo '<div class="alert alert-danger">Order not found.</div>';
                    exit;
                }

                $order = mysqli_fetch_assoc($orderResult);

                // Fetch order items
                $orderItemsQuery = "SELECT oi.*, p.name AS product_name 
                                    FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = '{$order['id']}' AND oi.tenant_id = '$tenant_id' AND p.tenant_id = '$tenant_id'";
                $orderItemsResult = mysqli_query($conn, $orderItemsQuery);

                if (!$orderItemsResult) {
                    echo '<div class="alert alert-danger">Failed to fetch order items.</div>';
                    exit;
                }

                // Calculate total advance, due, and grand total
                $totalAdvance = 0;
                $totalDue = 0;
                $grandTotal = 0;
                while ($item = mysqli_fetch_assoc($orderItemsResult)) {
                    $totalAdvance += $item['advance'];
                    $totalDue += $item['due'];
                    $grandTotal += ($item['price'] * $item['quantity']) - $item['discount'];
                }

                // Re-fetch the result set for display
                mysqli_data_seek($orderItemsResult, 0);
            ?>

            <!-- Invoice Header -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <h5>Customer Info</h5>
                    <p>Name: <?= htmlspecialchars($order['customer_name']); ?></p>
                    <p>Address: <?= htmlspecialchars($order['customer_email']); ?></p>
                    <p>Phone: <?= htmlspecialchars($order['customer_phone']); ?></p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 style="font-weight: bold; margin: 0;">RJ Group Ltd</h3>
                    <p style="margin: 0;">Shubhanighat-Bandar Bazar Road</p>
                    <p style="margin: 0;">Sylhet, Bangladesh</p>
                </div>
                <div class="col-md-4 text-end">
                    <h5>Additional Info</h5>
                    <p>Order Date: <?= htmlspecialchars($order['order_date']); ?></p>
                    <p>Track No: <?= htmlspecialchars($order['tracking_no']); ?></p>
                    <p>Delivery Date: <?= htmlspecialchars($order['delivery_date']); ?></p>
                    <p>Served By: <?= $_SESSION['loggedInUser']['name']; ?></p>
                </div>
            </div>

            <!-- Order Items Table -->
            <h5>Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Discount</th>
                            <th>Advance</th>
                        
                            <th>Due</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($item = mysqli_fetch_assoc($orderItemsResult)) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($item['product_name']); ?></td>
                                <td><?= htmlspecialchars($item['descriptions']); ?></td>

                                <td><?= number_format($item['price'], 2); ?></td>
                                <td><?= $item['quantity']; ?></td>
                                <td><?= number_format($item['discount'], 2); ?></td>
                                <td><?= number_format($item['advance'], 2); ?></td>
                                <td><?= number_format($item['due'], 2); ?></td>
                                <td><?= number_format(($item['price'] * $item['quantity']) - $item['discount'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end">Grand Total:</td>
                            <td><?= number_format($grandTotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Total Advance Paid:</td>
                            <td><?= number_format($totalAdvance, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Total Due Amount:</td>
                            <td><?= number_format($totalDue, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="text-end mt-4">
                <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
                <button class="btn btn-success">Download PDF</button>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
