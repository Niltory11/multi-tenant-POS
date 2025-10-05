<?php

ob_start(); // Start output buffering
include('includes/header.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once('../config/function.php');

// Retrieve and validate input values
$customerId = validate($_POST['customer_id']);
$orderStatus = validate($_POST['order_status']);
$deliveryDate = validate($_POST['delivery_date']); // Delivery Date
$description = validate($_POST['description']); // Overall Description
$trackingNo = 'INV-' . rand(100000, 999999);
$discount = isset($_POST['discount']) ? validate($_POST['discount']) : 0; // Default to 0
$advance = isset($_POST['advance'][0]) ? validate($_POST['advance'][0]) : 0; // Default to 0
$due = isset($_POST['due'][0]) ? validate($_POST['due'][0]) : 0; // Default to 0

$totalAmount = 0;

// Retrieve product and order details
$products = $_POST['products']; // Array of product IDs
$quantities = $_POST['quantities']; // Corresponding quantities
$units = $_POST['units']; // Unit
$productDescriptions = $_POST['descriptions']; // Individual product descriptions
$discounts = $_POST['discounts']; // Discounts for individual products
$advances = $_POST['advance']; // Advances
$dues = $_POST['due']; // Dues

foreach ($products as $index => $productId) {
$quantity = $quantities[$index];
$productQuery = "SELECT price FROM products WHERE id='$productId' AND tenant_id='$tenant_id' LIMIT 1";
$productResult = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($productResult);

$productDiscount = $discounts[$index] ?? 0; // Individual product discount
$totalAmount += ($product['price'] * $quantity) - $productDiscount; // Calculate total considering discount
}

// Insert the order with tenant_id
$tenant_id = $_SESSION['loggedInUser']['tenant_id'];
$orderQuery = "INSERT INTO orders (tenant_id, customer_id, tracking_no, total_amount, order_status, discount, advance, due, delivery_date, description, order_date)
VALUES ('$tenant_id', '$customerId', '$trackingNo', '$totalAmount', '$orderStatus', '$discount', '$advance', '$due', '$deliveryDate', '$description', NOW())";
$orderResult = mysqli_query($conn, $orderQuery);
if ($orderResult) {
$orderId = mysqli_insert_id($conn);

// Insert order items and update product quantities
foreach ($products as $index => $productId) {
$quantity = $quantities[$index];
$productQuery = "SELECT price, quantity FROM products WHERE id='$productId' AND tenant_id='$tenant_id' LIMIT 1"; // Use 'quantity' directly
$productResult = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($productResult);

if ($product['quantity'] < $quantity) { // Compare directly with 'quantity'
echo "<div class='alert alert-danger'>Not enough stock!</div>";
exit; // Stop the process if insufficient stock
}

$price = $product['price'];
$unit = $units[$index];
$productDiscount = $discounts[$index] ?? 0;
$productAdvance = $advances[$index] ?? 0;
$productDue = $dues[$index] ?? 0;
$productDescription = $productDescriptions[$index]; // Retrieve product description

$orderItemQuery = "INSERT INTO order_items (order_id, product_id, price, quantity, discount, advance, due, unit, descriptions, tenant_id)
VALUES ('$orderId', '$productId', '$price', '$quantity', '$productDiscount', '$productAdvance', '$productDue', '$unit', '$productDescription', '$tenant_id')";
mysqli_query($conn, $orderItemQuery);

// Update the product quantity in stock
$newQuantity = $product['quantity'] - $quantity; // Adjust the stock
$updateProductQuery = "UPDATE products SET quantity='$newQuantity' WHERE id='$productId' AND tenant_id='$tenant_id'";
mysqli_query($conn, $updateProductQuery);
}

// Redirect to invoice page
header("Location: invoice.php?order_id=$orderId");
exit;
} else {
echo '<div class="alert alert-danger">Failed to create order. Please try again.</div>';
}
}
?>

<!-- HTML Form -->
<div class="container-fluid px-4">
<div class="card mt-4 shadow-sm">
<div class="card-header">
<h4 class="mb-0">Create Order</h4>
</div>
<div class="card-body">
<form method="POST" action="">
<div class="mb-3">
<label for="customer_id" class="form-label">Customer</label>
<select id="customer_id" name="customer_id" class="form-select mySelect2" required>
<option value="" disabled selected>Select Customer</option>
<?php
$tenant_id = $_SESSION['loggedInUser']['tenant_id'];
$customers = mysqli_query($conn, "SELECT * FROM customers WHERE tenant_id='$tenant_id'");
while ($customer = mysqli_fetch_assoc($customers)) {
echo "<option value='{$customer['id']}'>{$customer['name']}</option>";
}
?>
</select>
</div>

<div class="mb-3">
<div class="col-md-2">
<label for="delivery_date" class="form-label">Delivery Date</label>
<input type="date" id="delivery_date" name="delivery_date" class="form-control" required>
</div>
</div>

<div class="mb-3">
<label for="description" class="form-label">Order Summery</label>
<textarea id="description" name="description" class="form-control" rows="3" required></textarea>
</div>

<div id="productList">
<div class="row mb-3">
<div class="col-md-6">
<label for="products[]" class="form-label">Product</label>
<select name="products[]" class="form-select mySelect2" required>
<option value="" disabled selected>Select Product</option>
<?php
$products = mysqli_query($conn, "SELECT * FROM products WHERE tenant_id='$tenant_id'");
while ($product = mysqli_fetch_assoc($products)) {
echo "<option value='{$product['id']}' data-price='{$product['price']}'>{$product['name']}</option>";
}
?>
</select>
</div>
<div class="col-md-3">
<label for="quantities[]" class="form-label">Quantity</label>
<input type="double" name="quantities[]" class="form-control quantity-input" min="1" required>
</div>
<div class="col-md-3">
<label for="units[]" class="form-label">Unit</label>
<select name="units[]" class="form-select" required>
<option value="Piece">Piece</option>
<option value="Set">Set</option>
<option value="Yard">Yard</option>
<option value="kg">kg</option>
<option value="Other">Other</option>
</select>
</div>
<div class="col-md-6">
<label for="descriptions[]" class="form-label">Description</label>
<textarea name="descriptions[]" class="form-control" rows="2" required></textarea>
</div>
<div class="col-md-3">
<label for="totals[]" class="form-label">Total</label>
<input type="text" name="totals[]" class="form-control total-input" readonly>
</div>
<div class="col-md-3">
<label for="discounts[]" class="form-label">Price Adjustment</label>
<input type="double" name="discounts[]" class="form-control discount-input" step="0.01" min="0" required>
</div>
<div class="col-md-3">
<label for="advance[]" class="form-label">Advance</label>
<input type="number" name="advance[]" class="form-control advance-input" step="0.01" min="0" required>
</div>
<div class="col-md-3">
<label for="due[]" class="form-label">Due</label>
<input type="number" name="due[]" class="form-control due-input" step="0.01" readonly>
</div>
</div>
</div>

<button type="button" id="addProduct" class="btn btn-success mb-3">Add Another Product</button>
<div class="mb-3">
<label for="order_status" class="form-label">Order Status</label>
<select id="order_status" name="order_status" class="form-select" required>
<option value="" disabled selected>Select Status</option>
<option value="sale">Sale</option>
<option value="order">Order</option>
</select>
</div>
<button type="submit" class="btn btn-primary">Create Order</button>
</form>
</div>
</div>
</div>

<script>
function calculateTotal(row) {
const productSelect = row.querySelector('select[name="products[]"]');
const quantityInput = row.querySelector('input[name="quantities[]"]');
const totalInput = row.querySelector('input[name="totals[]"]');
const discountInput = row.querySelector('input[name="discounts[]"]');
const advanceInput = row.querySelector('input[name="advance[]"]');
const dueInput = row.querySelector('input[name="due[]"]');

const price = parseFloat(productSelect.options[productSelect.selectedIndex]?.getAttribute('data-price')) || 0;
const quantity = parseFloat(quantityInput.value) || 0;
const discount = parseFloat(discountInput.value) || 0;
const advance = parseFloat(advanceInput.value) || 0;

const total = (price * quantity) - discount;
totalInput.value = total.toFixed(2);

const due = total - advance;
dueInput.value = due.toFixed(2);
}

document.addEventListener('input', function(event) {
if (event.target.matches('select[name="products[]"], input[name="quantities[]"], input[name="discounts[]"], input[name="advance[]"]')) {
const row = event.target.closest('.row');
calculateTotal(row);
}
});

document.getElementById('addProduct').addEventListener('click', function() {
const productList = document.getElementById('productList');
const newProductRow = `
<div class="row mb-3">
<div class="col-md-6">
<label for="products[]" class="form-label">Product</label>






<select name="products[]" class="form-select mySelect2" required>
                                <option value="" disabled selected>Select Product</option>
                                <?php
                                $products = mysqli_query($conn, "SELECT * FROM products WHERE tenant_id='$tenant_id'");
                                while ($product = mysqli_fetch_assoc($products)) {
                                    echo "<option value='{$product['id']}' data-price='{$product['price']}'>{$product['name']}</option>";
                                }
                                ?>
                            </select>








</div>
<div class="col-md-3">
<label for="quantities[]" class="form-label">Quantity</label>
<input type="double" name="quantities[]" class="form-control quantity-input" min="1" required>
</div>
<div class="col-md-3">
<label for="units[]" class="form-label">Unit</label>
<select name="units[]" class="form-select" required>
<option value="Piece">Piece</option>
<option value="Set">Set</option>
<option value="Yard">Yard</option>
<option value="kg">kg</option>
<option value="Other">Other</option>
</select>
</div>
<div class="col-md-6">
<label for="descriptions[]" class="form-label">Description</label>
<textarea name="descriptions[]" class="form-control" rows="2" required></textarea>
</div>
<div class="col-md-3">
<label for="totals[]" class="form-label">Total</label>
<input type="text" name="totals[]" class="form-control total-input" readonly>
</div>
<div class="col-md-3">
<label for="discounts[]" class="form-label">Price Adjustment</label>
<input type="double" name="discounts[]" class="form-control discount-input" step="0.01" min="0" required>
</div>
<div class="col-md-3">
<label for="advance[]" class="form-label">Advance</label>
<input type="number" name="advance[]" class="form-control advance-input" step="0.01" min="0" required>
</div>
<div class="col-md-3">
<label for="due[]" class="form-label">Due</label>
<input type="number" name="due[]" class="form-control due-input" step="0.01" readonly>
</div>
</div>`;
productList.insertAdjacentHTML('beforeend', newProductRow);$('.mySelect2').select2();
});
</script>

<?php include('includes/footer.php'); ?>
<?php ob_end_flush(); ?>