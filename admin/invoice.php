<?php
include('includes/header.php');
require_once('../config/function.php');

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    echo '<div class="alert alert-danger">No order ID provided.</div>';
    exit;
}

$orderId = validate($_GET['order_id']);

// Fetch order details
$orderQuery = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email 
               FROM orders o 
               JOIN customers c ON o.customer_id = c.id 
               WHERE o.id = '$orderId'";
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
                    WHERE oi.order_id = '$orderId'";
$orderItemsResult = mysqli_query($conn, $orderItemsQuery);

if (!$orderItemsResult) {
    echo '<div class="alert alert-danger">Failed to fetch order items.</div>';
    exit;
}

$orderItems = [];
while ($item = mysqli_fetch_assoc($orderItemsResult)) {
    $orderItems[] = $item;
}

// Calculate totals
$totalAdvance = array_sum(array_column($orderItems, 'advance'));
$totalDue = array_sum(array_column($orderItems, 'due'));
?>

<div class="container-fluid px-12">
    <div class="card mt-12 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Invoice</h4>
        </div>
        <div class="row mb-12">
            <div class="col-md-4">
                <h5>Customer Info</h5>
                <p>Name: <?= htmlspecialchars($order['customer_name']); ?></p>
                <p>Email: <?= htmlspecialchars($order['customer_email']); ?></p>
                <p>Phone: <?= htmlspecialchars($order['customer_phone']); ?></p>
            </div>
            <div class="col-md-4 text-center">
                <h3 style="font-weight: bold; margin: 0;">RJ Group Ltd</h3>
                <p style="margin: 0;">Rahim Foam Market</p>
                <p style="margin: 0;">Sylhet, Bangladesh</p>
            </div>
            <div class="col-md-4 text-end">
                <h5>Additional Info</h5>
                <p>Order Date: <?= htmlspecialchars($order['order_date']); ?></p>
                <p>Tracking No: <?= htmlspecialchars($order['tracking_no']); ?></p>
                <p>Delivery Date: <?= htmlspecialchars($order['delivery_date']); ?></p>
                <p>Prepared By: <?= $_SESSION['loggedInUser']['name']; ?></p>
            </div>
        </div>

        <div class="mb-4">
            <h5>Order Summery</h5>
            <p><?= htmlspecialchars($order['description']); ?></p>
        </div>

        <div class="mb-4">
            <h5>Order Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Item Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item) : ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= htmlspecialchars($item['descriptions']); ?></td>
                            <td><?= number_format($item['price'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td><?= $item['unit']; ?></td>
                            <td><?= number_format($item['discount'], 2); ?></td>
                            <td><?= number_format(($item['price'] * $item['quantity']) - $item['discount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end">Grand Total:</td>
                        <td><?= number_format($order['total_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end">Total Advance Paid:</td>
                        <td><?= number_format($totalAdvance, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end">Total Due Amount:</td>
                        <td><?= number_format($totalDue, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end">
            <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
