<?php include('includes/header.php'); ?>

<?php
// Initialize variables
$totalRevenue = 0.0;
$totalExpenses = 0.0;
$totalProfit = 0.0;
$startDate = '';
$endDate = '';
$salesData = [];
$expensesData = [];

// Check if a date filter is applied
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Fetch Sales Data with customer names within the date range
    $salesQuery = "
        SELECT o.*, c.name AS customer_name 
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        WHERE o.order_date BETWEEN ? AND ?";
    $salesStmt = $conn->prepare($salesQuery);
    $salesStmt->bind_param('ss', $startDate, $endDate);
    $salesStmt->execute();
    $salesResult = $salesStmt->get_result();
    $salesData = $salesResult->fetch_all(MYSQLI_ASSOC);
    $salesStmt->close();

    // Fetch Total Sales Revenue within the date range
    $salesTotalQuery = "SELECT SUM(total_amount) AS total_sales 
                        FROM orders 
                        WHERE order_date BETWEEN ? AND ?";
    $salesTotalStmt = $conn->prepare($salesTotalQuery);
    $salesTotalStmt->bind_param('ss', $startDate, $endDate);
    $salesTotalStmt->execute();
    $salesTotalResult = $salesTotalStmt->get_result();
    $salesTotalData = $salesTotalResult->fetch_assoc();
    $totalRevenue = $salesTotalData['total_sales'] ?? 0;
    $salesTotalStmt->close();

    // Fetch Expenses Data within the date range
    $expensesQuery = "SELECT * FROM expenses WHERE expense_date BETWEEN ? AND ?";
    $expensesStmt = $conn->prepare($expensesQuery);
    $expensesStmt->bind_param('ss', $startDate, $endDate);
    $expensesStmt->execute();
    $expensesResult = $expensesStmt->get_result();
    $expensesData = $expensesResult->fetch_all(MYSQLI_ASSOC);
    $expensesStmt->close();

    // Fetch Total Expenses within the date range
    $expensesTotalQuery = "SELECT SUM(amount) AS total_expenses 
                           FROM expenses 
                           WHERE expense_date BETWEEN ? AND ?";
    $expensesTotalStmt = $conn->prepare($expensesTotalQuery);
    $expensesTotalStmt->bind_param('ss', $startDate, $endDate);
    $expensesTotalStmt->execute();
    $expensesTotalResult = $expensesTotalStmt->get_result();
    $expensesTotalData = $expensesTotalResult->fetch_assoc();
    $totalExpenses = $expensesTotalData['total_expenses'] ?? 0;
    $expensesTotalStmt->close();
} else {
    // Fetch All Sales Data with customer names
    $salesQuery = "
        SELECT o.*, c.name AS customer_name 
        FROM orders o
        JOIN customers c ON o.customer_id = c.id";
    $salesResult = $conn->query($salesQuery);
    $salesData = $salesResult->fetch_all(MYSQLI_ASSOC);

    // Fetch Total Sales Revenue
    $salesTotalQuery = "SELECT SUM(total_amount) AS total_sales FROM orders";
    $salesTotalResult = $conn->query($salesTotalQuery);
    $salesTotalData = $salesTotalResult->fetch_assoc();
    $totalRevenue = $salesTotalData['total_sales'] ?? 0;

    // Fetch All Expenses Data
    $expensesQuery = "SELECT * FROM expenses";
    $expensesResult = $conn->query($expensesQuery);
    $expensesData = $expensesResult->fetch_all(MYSQLI_ASSOC);

    // Fetch Total Expenses
    $expensesTotalQuery = "SELECT SUM(amount) AS total_expenses FROM expenses";
    $expensesTotalResult = $conn->query($expensesTotalQuery);
    $expensesTotalData = $expensesTotalResult->fetch_assoc();
    $totalExpenses = $expensesTotalData['total_expenses'] ?? 0;
}

// Calculate Profit
$totalProfit = $totalRevenue - $totalExpenses;
?>

<div class="container mt-4">
    <h2 class="text-center">Profit Report</h2>

    <!-- Filter Section -->
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="profit.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date">From Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">To Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="profit.php" class="btn btn-danger">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Profit Summary -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h4>Profit Summary</h4>
            <p><strong>Total Sales Revenue:</strong> TK <?= number_format($totalRevenue, 2) ?></p>
            <p><strong>Total Expenses:</strong> TK <?= number_format($totalExpenses, 2) ?></p>
            <hr>
            <h4><strong>Total Profit:</strong> TK <?= number_format($totalProfit, 2) ?></h4>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="mt-4">
        <h3>Details</h3>

        <!-- Sales Table -->
        <h4>Sales</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesData as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                            <td><?= htmlspecialchars($sale['order_date']) ?></td>
                            <td>TK <?= number_format($sale['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Expenses Table -->
        <h4>Expenses</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Expense Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expensesData as $expense): ?>
                        <tr>
                            <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                            <td><?= htmlspecialchars($expense['category']) ?></td>
                            <td>TK <?= number_format($expense['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>






<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['authenticated']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>






















<?php include('includes/footer.php'); ?>
