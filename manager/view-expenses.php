<?php
include('includes/header.php');


// Initialize variables
$expenses = [];
$selectedCategory = '';
$startDate = '';
$endDate = '';
$descriptionSearch = '';
$totalAmount = 0.0;
$totalDueAmount = 0.0;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedCategory = $_POST['expense_category'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $descriptionSearch = '%' . $_POST['description_search'] . '%';

    // Get tenant_id from logged-in user session for filtering
    $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
    
    // Fetch expenses based on category, date range, and description
    $query = "SELECT * FROM expenses 
              WHERE category = ? 
              AND expense_date BETWEEN ? AND ? 
              AND description LIKE ?
              AND tenant_id = ?
              ORDER BY expense_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $selectedCategory, $startDate, $endDate, $descriptionSearch, $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Get tenant_id from logged-in user session for filtering
    $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
    
    // Fetch all expenses if no filters are applied
    $query = "SELECT * FROM expenses WHERE tenant_id = ? ORDER BY expense_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Calculate total amounts
foreach ($expenses as $expense) {
    $totalAmount += $expense['amount'];
    $totalDueAmount += $expense['due'];
}
?>

<div class="container">
    <h1 class="mt-4">View Expenses</h1>

    <!-- Filter Form -->
    <form action="view-expenses.php" method="POST" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="expense_category">Category</label>
                <select id="expense_category" name="expense_category" class="form-control" required>
                    <option value="">--Select Category--</option>
                    <option value="Merchant Payments & Dues" <?= $selectedCategory == 'Merchant Payments & Dues' ? 'selected' : '' ?>>Merchant Payments & Dues</option>
                    <!-- Add more categories as needed -->




<option value="Employee's Payments & Dues" <?= $selectedCategory == "Employee's Payments & Dues" ? 'selected' : '' ?>>Employee's Payments & Dues</option>
                    <option value="Daily Employee's Snacks Bill" <?= $selectedCategory == 'Daily Employee\'s Snacks Bill' ? 'selected' : '' ?>>Daily Employee's Snacks Bill</option>
                    <option value="Rental Payments & Dues" <?= $selectedCategory == 'Rental Payments & Dues' ? 'selected' : '' ?>>Rental Payments & Dues</option>
                    <option value="Bank Deposits" <?= $selectedCategory == 'Bank Deposits' ? 'selected' : '' ?>>Bank Deposits</option>
                    <option value="Other Bill" <?= $selectedCategory == 'Other Bill' ? 'selected' : '' ?>>Other Bill/Payments</option>
                    <option value="Snacks (own)" <?= $selectedCategory == 'Snacks (own)' ? 'selected' : '' ?>>Snacks (own)</option>
                    <option value="Snacks (customer)" <?= $selectedCategory == 'Snacks (customer)' ? 'selected' : '' ?>>Snacks (customer)</option>
                    <option value="Electricity Bill" <?= $selectedCategory == 'Electricity Bill' ? 'selected' : '' ?>>Electricity Bill</option>
                    <option value="Transport Bill" <?= $selectedCategory == 'Transport Bill' ? 'selected' : '' ?>>Transport Bill</option>
                    <option value="Transportation Bill" <?= $selectedCategory == 'Transportation Bill' ? 'selected' : '' ?>>Transportation Bill</option>
                    <option value="Condition Bill" <?= $selectedCategory == 'Condition Bill' ? 'selected' : '' ?>>Condition Bill</option>
                    <option value="Clothing Bill (own)" <?= $selectedCategory == 'Clothing Bill (own)' ? 'selected' : '' ?>>Clothing Bill (own)</option>
                    <option value="Clothing Bill (others)" <?= $selectedCategory == 'Clothing Bill (others)' ? 'selected' : '' ?>>Clothing Bill (others)</option>
                    <option value="Buying Goods (others)" <?= $selectedCategory == 'Buying Goods (others)' ? 'selected' : '' ?>>Buying Goods (others)</option>
                    <option value="Foam Bill" <?= $selectedCategory == 'Foam Bill' ? 'selected' : '' ?>>Foam Bill</option>
                    <option value="R-Joma" <?= $selectedCategory == 'R-Joma' ? 'selected' : '' ?>>R-Joma</option>
                    <option value="Mohajon Joma" <?= $selectedCategory == 'Mohajon Joma' ? 'selected' : '' ?>>Mohajon Joma</option>
                    <option value="Other Bills" <?= $selectedCategory == 'Other Bills' ? 'selected' : '' ?>>Other Bills</option>

















                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="start_date">From</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
            </div>
            <div class="form-group col-md-3">
                <label for="end_date">To</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label for="description_search">Search by Description</label>
                <input type="text" id="description_search" name="description_search" class="form-control" value="<?= htmlspecialchars($_POST['description_search'] ?? '') ?>" placeholder="Enter description...">
            </div>
            <div class="form-group col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
        </div>
    </form>

    <!-- Expenses Table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= htmlspecialchars($expense['category']) ?></td>
                            <td><?= number_format($expense['amount'], 2) ?></td>
                            <td><?= number_format($expense['paid'], 2) ?></td>
                            <td><?= number_format($expense['due'], 2) ?></td>
                            <td><?= htmlspecialchars($expense['description']) ?></td>
                            <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2"><strong>Total Amount</strong></td>
                        <td colspan="4"><strong><?= number_format($totalAmount, 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Total Due Amount</strong></td>
                        <td colspan="4"><strong><?= number_format($totalDueAmount, 2) ?></strong></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No expenses found for the selected filters.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php'); ?>
