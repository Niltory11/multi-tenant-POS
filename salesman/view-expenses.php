<?php
include('includes/header.php');
// Ensure this file contains the MySQLi connection setup

// Initialize variables
$expenses = [];
$selectedCategory = '';
$startDate = '';
$endDate = '';
$totalAmount = 0.0;
$totalDueAmount = 0.0;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedCategory = $_POST['expense_category'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Fetch expenses based on the selected category and date range
    // Get tenant_id from logged-in user session for filtering
    $tenant_id = isset($_SESSION['loggedInUser']['tenant_id']) ? $_SESSION['loggedInUser']['tenant_id'] : 'default';
    
    $query = "SELECT * FROM expenses WHERE category = ? AND expense_date BETWEEN ? AND ? AND tenant_id = ? ORDER BY expense_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $selectedCategory, $startDate, $endDate, $tenant_id);
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

// Calculate the total amount of expenses
foreach ($expenses as $expense) {
    $totalAmount += $expense['amount'];
    $totalDueAmount += $expense['due'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses</title>
    <link rel="stylesheet" href="path_to_your_css_file.css"> <!-- Replace with your actual CSS file path -->
    <style>
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: orange;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #e69500;
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .search-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }
        .date-inputs {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>View Expenses</h2>
    
    <div class="search-container">
        <form action="view-expenses.php" method="POST" class="search-form">
            <div class="form-group">
                <label for="expense_category">Category</label>
                <select id="expense_category" name="expense_category" class="mySelect2" required>
                    <option value="">--Select Category--</option>
                    <!-- Add your options here -->
                    
                    
                    
                    <option value="Marchant Payments & Dues" <?= $selectedCategory == 'Merchant Payments & Dues' ? 'selected' : '' ?>>Merchant Payments & Dues</option>
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



                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
          























            <div class="form-group date-inputs">
                <div>
                    <label for="start_date">From</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
                </div>
                <div>
                    <label for="end_date">To</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <button type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Search by description -->
    <div class="mb-3">
        <input type="text" id="descriptionSearch" class="form-control" placeholder="Search by Description...">
    </div>

    <div class="table-responsive">
        <table id="expensesTable">
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
                            <td><?= htmlspecialchars($expense['amount']) ?></td>
                            <td><?= htmlspecialchars($expense['paid']) ?></td>
                            <td><?= htmlspecialchars($expense['due']) ?></td>
                            <td><?= htmlspecialchars($expense['description']) ?></td>
                            <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2"><strong>Total Amount</strong></td>
                        <td colspan="3"><strong><?= number_format($totalAmount, 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Total Due Amount</strong></td>
                        <td colspan="3"><strong><?= number_format($totalDueAmount, 2) ?></strong></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No expenses found for the selected category and date range.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const descriptionSearch = document.getElementById('descriptionSearch');
        const tableRows = document.querySelectorAll('#expensesTable tbody tr');

        descriptionSearch.addEventListener('keyup', function () {
            const query = descriptionSearch.value.toLowerCase();

            tableRows.forEach(row => {
                const description = row.children[4]?.textContent.toLowerCase();
                row.style.display = description && description.includes(query) ? '' : 'none';
            });
        });
    });
</script>

<?php include('includes/footer.php'); ?>
</body>
</html>
