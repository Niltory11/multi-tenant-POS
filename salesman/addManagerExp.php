<?php
include('includes/header.php'); // Include your header file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect input values from the form
    $expenseCategory = trim($_POST['expense_category']);
    $expenseAmount = (float)$_POST['expense_amount']; // Total amount
    $expenseDue = (float)$_POST['expense_due']; // Due amount
    $expensePaid = (float)$_POST['expense_paid']; // Paid amount
    $expenseDescription = trim($_POST['expense_description']);

    // Automatically set the current date
    $expenseDateForDB = date('Y-m-d');

    // Insert the expense into the database (MySQLi)
    $query = "INSERT INTO expenses (expense_date, category, amount, due, paid, description, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssddds', $expenseDateForDB, $expenseCategory, $expenseAmount, $expenseDue, $expensePaid, $expenseDescription);

    // Execute the query and display appropriate messages
    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Expense added successfully!</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Failed to add expense. Please try again.</div>';
    }

    $stmt->close(); // Close the prepared statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <style>
        .container {
            max-width: 600px;
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
        .form-group input, .form-group select, .form-group textarea {
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
    </style>
</head>
<body>

<div class="container">
    <h2>Add Expense</h2>
    <form action="addManagerExp.php" method="POST">
        <div class="form-group">
            <label for="expense_category">Category</label>
            <select id="expense_category" name="expense_category" required>
                <option value="">--Select Category--</option>
                <option value="Marchant Payments & Dues">Marchant Payments & Dues</option>
                <option value="Employee's Payments & Dues">Employee's Payments & Dues</option>
                <option value="Daily Employee's Snacks Bill">Daily Employee's Snacks Bill</option>
                <option value="Rental Payments & Dues">Rental Payments & Dues</option>
                <option value="Bank Deposits">Bank Deposits</option>
                <option value="Other Bill">Other Bill/Payments</option>
            </select>
        </div>
        <div class="form-group">
            <label for="expense_amount">Total Amount</label>
            <input type="number" id="expense_amount" name="expense_amount" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="expense_due">Due Amount</label>
            <input type="number" id="expense_due" name="expense_due" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="expense_paid">Paid Amount</label>
            <input type="number" id="expense_paid" name="expense_paid" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="expense_description">Description</label>
            <textarea id="expense_description" name="expense_description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit">Add Expense</button>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
