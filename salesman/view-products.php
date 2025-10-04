<?php include('includes/header.php'); ?>

<?php
// Fetch products from the database using MySQLi
$query = "SELECT * FROM products";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result(); // Get the result set
$products = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
$stmt->close();
?>

<div class="container mt-4">
    <h2 class="text-center">Product List</h2>
    <?php alertMessage(); ?>

    <!-- Search and Date Range Input -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for products...">
        </div>
        <div class="col-md-3">
            <input type="date" id="dateFrom" class="form-control" placeholder="From Date">
        </div>
        <div class="col-md-3">
            <input type="date" id="dateTo" class="form-control" placeholder="To Date">
        </div>
    </div>

    <!-- Product Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Supplier Name</th>
                    <th>Description</th>
                    <th>Memo/Challan NO</th>
                    <th>Stock In</th>
                    <th>Unit</th>
                    <th>Purchase Rate</th>
                    <th>Sale Rate</th>
                    <th>Minimum Sale Rate</th>
                    <th>Created AT</th>
                    
                    
                    
                    
                </tr>
            </thead>
            <tbody id="productTable">
                <?php foreach ($products as $product): ?>
                    <tr 
                        data-search="<?= htmlspecialchars($product['name'] . ' ' . $product['supplier_name']) ?>" 
                        data-expense="<?= htmlspecialchars($product['purchaseRate'] * $product['total']) ?>" 
                        data-expected-sale="<?= htmlspecialchars($product['minimum_sale_rate'] * $product['total']) ?>" 
                        data-date="<?= htmlspecialchars($product['created_at']) ?>">
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['supplier_name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['memo_id']) ?></td>
                        <td><?= htmlspecialchars($product['total']) ?></td>
                        <td><?= htmlspecialchars($product['unit']) ?></td>
                        <td><?= htmlspecialchars($product['purchaseRate']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td><?= htmlspecialchars($product['minimum_sale_rate']) ?></td>
                        <td><?= htmlspecialchars($product['created_at']) ?></td>
                        
                        
                        
                        
                        
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="mt-4">
        <h5>Total Expense: TK <span id="totalExpense">0.00</span></h5>
        <h5>Total Expected Sale: TK <span id="totalExpectedSale">0.00</span></h5>
    </div>
</div>

<script>
    // JavaScript for search and dynamic calculation functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        const productTableRows = document.querySelectorAll('#productTable tr');
        const totalExpenseElement = document.getElementById('totalExpense');
        const totalExpectedSaleElement = document.getElementById('totalExpectedSale');

        function calculateTotals() {
            let totalExpense = 0;
            let totalExpectedSale = 0;

            productTableRows.forEach(row => {
                if (row.style.display !== 'none') {
                    totalExpense += parseFloat(row.getAttribute('data-expense')) || 0;
                    totalExpectedSale += parseFloat(row.getAttribute('data-expected-sale')) || 0;
                }
            });

            totalExpenseElement.textContent = totalExpense.toFixed(2);
            totalExpectedSaleElement.textContent = totalExpectedSale.toFixed(2);
        }

        function applyFilters() {
            const searchQuery = searchInput.value.toLowerCase();
            const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
            const toDate = dateTo.value ? new Date(dateTo.value) : null;

            productTableRows.forEach(row => {
                const searchText = row.getAttribute('data-search').toLowerCase();
                const rowDate = new Date(row.getAttribute('data-date'));

                // Check if the row matches the search query and date range
                const matchesSearch = searchText.includes(searchQuery);
                const matchesDate = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);

                if (matchesSearch && matchesDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            calculateTotals();
        }

        // Attach event listeners
        searchInput.addEventListener('keyup', applyFilters);
        dateFrom.addEventListener('change', applyFilters);
        dateTo.addEventListener('change', applyFilters);

        // Initial calculation
        calculateTotals();
    });
</script>

<?php include('includes/footer.php'); ?>
