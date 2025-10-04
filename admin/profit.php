<?php
include('includes/header.php');

// Initialize variables
$totalRevenue = 0.0;
$totalDue = 0.0;
$productPurchaseRate = 0.0;
$transportBill = 0.0;
$conditionBill = 0.0;
$clothingBillOwn = 0.0;
$clothingBillOthers = 0.0;
$buyingGoodsOthers = 0.0;

$snacksOwn = 0.0;
$snacksCustomer = 0.0;
$electricityBill = 0.0;
$foamBill = 0.0;
$otherBills = 0.0;

$transportationBill = 0.0;
$rentalPaymentAndDues = 0.0;
$mohajonJhoma = 0.0;
$marchantPaymentsAndDues = 0.0;
$employeesPaymentsAndDues = 0.0;
$otherBillsPayments = 0.0;
$rJhoma = 0.0;
$bankDeposit = 0.0;
$dailyEmployeeSnacks = 0.0;

$grandProfit = 0.0;
$netProfit = 0.0;

$startDate = '';
$endDate = '';

// Check for filter (Date Range)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Fetch Total Sales
    $salesQuery = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE order_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($salesQuery);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $salesResult = $stmt->get_result()->fetch_assoc();
    $totalRevenue = $salesResult['total_sales'] ?? 0;
    $stmt->close();

    // Fetch Total Due Amount
    $dueQuery = "SELECT SUM(due) AS total_due FROM orders WHERE order_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($dueQuery);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $duesResult = $stmt->get_result()->fetch_assoc();
    $totalDue = $duesResult['total_due'] ?? 0;
    $stmt->close();

    // Fetch Total Product Purchase Rate
    $purchaseQuery = "SELECT SUM(purchaseRate * quantity) AS total_purchase FROM products WHERE created_at BETWEEN ? AND ?";
    $stmt = $conn->prepare($purchaseQuery);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $purchaseResult = $stmt->get_result()->fetch_assoc();
    $productPurchaseRate = $purchaseResult['total_purchase'] ?? 0;
    $stmt->close();

    // Fetch Expenses
    $expenseQuery = "
        SELECT TRIM(LOWER(category)) AS category, SUM(amount) AS total_amount 
        FROM expenses 
        WHERE expense_date BETWEEN ? AND ? 
        GROUP BY TRIM(LOWER(category))
    ";
    $stmt = $conn->prepare($expenseQuery);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $expenseResult = $stmt->get_result();

    // Map expenses to categories
    while ($expense = $expenseResult->fetch_assoc()) {
        $category = strtolower(trim($expense['category']));
        switch ($category) {
            case 'transport bill':
                $transportBill = $expense['total_amount'];
                break;
            case 'condition bill':
                $conditionBill = $expense['total_amount'];
                break;
            case 'clothing bill (own)':
                $clothingBillOwn = $expense['total_amount'];
                break;
            case 'clothing bill (others)':
                $clothingBillOthers = $expense['total_amount'];
                break;
            case 'buying goods (others)':
                $buyingGoodsOthers = $expense['total_amount'];
                break;
            case 'snacks (own)':
                $snacksOwn = $expense['total_amount'];
                break;
            case 'snacks (customer)':
                $snacksCustomer = $expense['total_amount'];
                break;
            case 'electricity bill':
                $electricityBill = $expense['total_amount'];
                break;
            case 'foam bill':
                $foamBill = $expense['total_amount'];
                break;
            case 'other bills':
                $otherBills = $expense['total_amount'];
                break;
            case 'transportation bill':
                $transportationBill = $expense['total_amount'];
                break;
            case 'rental payments & dues':
                $rentalPaymentAndDues = $expense['total_amount'];
                break;
            case 'mohajon joma':
                $mohajonJhoma = $expense['total_amount'];
                break;
            case 'marchant payments & dues':
                $marchantPaymentsAndDues = $expense['total_amount'];
                break;
            case "employee's payments & dues":
                $employeesPaymentsAndDues = $expense['total_amount'];
                break;
            case "daily employee's snacks bill":
                $dailyEmployeeSnacks = $expense['total_amount'];
                break;
            case 'bank deposits':
                $bankDeposit = $expense['total_amount'];
                break;
            case 'r-joma':
                $rJhoma = $expense['total_amount'];
                break;
        }
    }
    $stmt->close();

    // Calculate Grand Profit
    $grandProfit = $totalRevenue - ($productPurchaseRate + $transportBill + $conditionBill + $clothingBillOwn + $clothingBillOthers + $buyingGoodsOthers + $totalDue);

    // Calculate Net Profit
    $netProfit = ($grandProfit + $rJhoma) - ($snacksOwn + $snacksCustomer + $electricityBill + $foamBill + $otherBills + $transportationBill + $rentalPaymentAndDues + $mohajonJhoma + $marchantPaymentsAndDues + $employeesPaymentsAndDues + $bankDeposit + $dailyEmployeeSnacks);
}
?>



<!-- HTML for Profit Report -->
<div class="container mt-4">
    <h2 class="text-center">Profit Report</h2>
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="profit.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date">From Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">To Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="profit.php" class="btn btn-danger">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($startDate) && !empty($endDate)): ?>
        <div class="text-center my-3">
            <h5>Showing Results From: <strong><?= date('F j, Y', strtotime($startDate)) ?></strong> To: <strong><?= date('F j, Y', strtotime($endDate)) ?></strong></h5>
        </div>
    <?php endif; ?>

    <!-- Grand Profit -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h4>Grand Profit Calculation</h4>
            <p><strong>Total Sales:</strong> TK <?= number_format($totalRevenue, 2) ?></p>
            <p><strong>Product Purchase Rate:</strong> TK <?= number_format($productPurchaseRate, 2) ?></p>
            <p><strong>Transport Bill:</strong> TK <?= number_format($transportBill, 2) ?></p>
            <p><strong>Condition Bill:</strong> TK <?= number_format($conditionBill, 2) ?></p>
            <p><strong>Clothing Bill (own):</strong> TK <?= number_format($clothingBillOwn, 2) ?></p>
            <p><strong>Clothing Bill (others):</strong> TK <?= number_format($clothingBillOthers, 2) ?></p>
            <p><strong>Buying Goods (others):</strong> TK <?= number_format($buyingGoodsOthers, 2) ?></p>
            <p><strong>Total Dues:</strong> TK <?= number_format($totalDue, 2) ?></p>
            <hr>
            <h4><strong>Grand Profit:</strong> TK <?= number_format($grandProfit, 2) ?></h4>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h4>Net Profit Calculation</h4>
            <p><strong>Grand Profit:</strong> TK <?= number_format($grandProfit, 2) ?></p>
            
            <p><strong>R-Joma:</strong> TK <?= number_format($rJhoma, 2) ?></p>
            
            
            
            <p><strong>Snacks (own):</strong> TK <?= number_format($snacksOwn, 2) ?></p>
            <p><strong>Snacks (customer):</strong> TK <?= number_format($snacksCustomer, 2) ?></p>
            <p><strong>Electricity Bill:</strong> TK <?= number_format($electricityBill, 2) ?></p>
                      <p><strong>Transportation Bill:</strong> TK <?= number_format($transportationBill, 2) ?></p>  
                      
                      
                      
            
            <p><strong>Foam Bill:</strong> TK <?= number_format($foamBill, 2) ?></p>
            
            <p><strong>Mohajon Joma:</strong> TK <?= number_format($mohajonJhoma, 2) ?></p>
            
            <p><strong>Other Bills:</strong> TK <?= number_format($otherBills, 2) ?></p>
            
            <p><strong>Marchant Payments & Dues:</strong> TK <?= number_format($marchantPaymentsAndDues, 2) ?></p>
            
            <p><strong>Employee's Payments & Dues:</strong> TK <?= number_format($employeesPaymentsAndDues, 2) ?></p>
            
            <p><strong>Rental Payments & Dues:</strong> TK <?= number_format($rentalPaymentAndDues, 2) ?></p>
            
            <p><strong>Daily Employee's Snacks Bill:</strong> TK <?= number_format($dailyEmpolyeeSnacks, 2) ?></p>
            
            <p><strong>Bank Deposits:</strong> TK <?= number_format($bankDeposit, 2) ?></p>
            
            
                        <p><strong>Other Bill And Payments:</strong> TK <?= number_format($otherBills_payments, 2) ?></p>

            <hr>
            <h4><strong>Net Profit:</strong> TK <?= number_format($netProfit, 2) ?></h4>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
