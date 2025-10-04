<?php
include('includes/header.php');

if (isset($_GET['track'])) {
    $trackingNo = validate($_GET['track']);

    // Query to fetch order details along with total due amount
    $query = "SELECT 
                o.*, 
                (SELECT SUM(oi.due) 
                 FROM order_items oi 
                 WHERE oi.order_id = o.id) AS total_due
              FROM orders o
              WHERE o.tracking_no = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $trackingNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo "<h5>Order Not Found</h5>";
        exit;
    }

    // Fetch order items
    $orderItemsQuery = "SELECT oi.*, p.name AS product_name 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?";
    $stmtItems = $conn->prepare($orderItemsQuery);
    $stmtItems->bind_param('i', $order['id']);
    $stmtItems->execute();
    $itemsResult = $stmtItems->get_result();
    $orderItems = $itemsResult->fetch_all(MYSQLI_ASSOC);
    $stmtItems->close();
} else {
    echo "<h5>Invalid Tracking Number</h5>";
    exit;
}
?>

<div class="container mt-4">
    <h2>Edit Order</h2>
    <form action="code.php" method="POST">
        <input type="hidden" name="updateOrder" value="1">
        <input type="hidden" name="tracking_no" value="<?= htmlspecialchars($order['tracking_no']); ?>">

        <div class="mb-3">
            <label for="order_date" class="form-label">Order Date</label>
            <input type="date" name="order_date" id="order_date" class="form-control" 
                   value="<?= htmlspecialchars($order['order_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="order_status" class="form-label">Order Status</label>
            <input type="text" name="order_status" id="order_status" class="form-control" 
                   value="<?= htmlspecialchars($order['order_status']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="delivery_date" class="form-label">Delivery Date</label>
            <input type="date" name="delivery_date" id="delivery_date" class="form-control" 
                   value="<?= htmlspecialchars($order['delivery_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="due" class="form-label">Due Amount</label>
            <input type="number" name="due" id="due" class="form-control" 
                   value="<?= htmlspecialchars($order['total_due']); ?>" required placeholder="e.g., 0.00">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Order-Wide Description</label>
            <input type="text" name="description" id="description" class="form-control" 
                   value="<?= htmlspecialchars($order['description']); ?>" required>
        </div>

        <h4>Order Items</h4>
       <?php foreach ($orderItems as $item): ?>
    <div class="mb-3">
        <label for="due_<?= $item['id']; ?>" class="form-label">Due for <?= htmlspecialchars($item['product_name']); ?></label>
        <input type="number" 
               name="order_items[<?= $item['id']; ?>][due]" 
               id="due_<?= $item['id']; ?>" 
               class="form-control" 
               value="<?= htmlspecialchars($item['due']); ?>" 
               required>
        <input type="hidden" name="order_items[<?= $item['id']; ?>][id]" value="<?= $item['id']; ?>">
    </div>
<?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="orders.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
