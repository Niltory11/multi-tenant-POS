<?php
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expenseCategory = $_POST['expense_category'];
    $expenseAmount = $_POST['expense_amount'];
    $expenseDescription = $_POST['expense_description'];

    // Automatically set the current date
    $expenseDateForDB = date('Y-m-d');

    // Convert PDO statements to MySQLi
    $query = "INSERT INTO expenses (expense_date, category, amount, description, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssds', $expenseDateForDB, $expenseCategory, $expenseAmount, $expenseDescription);

    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Expense added successfully!</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Failed to add expense. Please try again.</div>';
    }

    $stmt->close(); // Close the statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link rel="stylesheet" href="path_to_your_css_file.css"> <!-- Replace with your actual CSS file path -->
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
    <form action="add-expenses.php" method="POST">
        <div class="form-group">
            <label for="expense_category">Category</label>
            <select id="expense_category" name="expense_category" required>
                <option value="">--Select Category--</option>
                <option value="Snacks (own)">Snacks (own)</option>
                <option value="Snacks (customer)">Snacks (customer)</option>
                <option value="Electricity Bill">Electricity Bill</option>
                <option value="Transport Bill">Transport Bill</option>
                <option value="Transportation Bill">Transportation Bill </option>
                <option value="Condition Bill ">Condition Bill</option>
                <option value="Clothing Bill (own)">Clothing Bill (own)</option>
                <option value="Clothing Bill (others)">Clothing Bill (others)</option>
                <option value="Buying Goods (others)">Buying Goods (others)</option>
                <option value="Foam Bill">Foam Bill</option>
                <option value="R-Joma">R-Joma</option>
                <option value="Mohajon Joma">Mohajon Joma</option>
                <option value="Other Bills">Other Bills</option>
            </select>
        </div>
        <div class="form-group">
            <label for="expense_amount">Amount</label>
            <input type="number" id="expense_amount" name="expense_amount" step="0.01" required>
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
