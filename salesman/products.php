<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Products
                <a href="products-create.php" class="btn btn-primary float-end">Add Product</a>
            </h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?>

            <!-- Search Bar -->
            <div class="mb-3">
                <input type="text" id="productSearch" class="form-control" placeholder="Search products by name or category..." onkeyup="searchProducts()">
            </div>

            <?php
            // Fetch products with category
            $productsQuery = "
                SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
            ";
            $products = mysqli_query($conn, $productsQuery);

            if (!$products) {
                echo '<h4>Something Went Wrong!</h4>';
                return false;
            }

            if (mysqli_num_rows($products) > 0) {
            ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Sale Rate</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $item): ?>
                        <tr 
                            data-expected-sale="<?= $item['price'] * $item['quantity']; ?>" 
                            data-search="<?= htmlspecialchars($item['name'] . ' ' . $item['category_name']) ?>">
                            <td><?= $item['id']; ?></td>
                            <td>
                                <img src="../<?= $item['image']; ?>" style="width:50px;height:50px;" alt="Img" />
                            </td>
                            <td><?= $item['name']; ?></td>
                            <td><?= $item['category_name']; ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td><?= $item['unit']; ?></td>
                            <td><?= number_format($item['price'], 2); ?></td>
                            <td>
                                <?php  
                                    if ($item['quantity'] <= 0) {
                                        echo '<span class="badge bg-danger">Stock Out</span>';
                                    } else {
                                        echo '<span class="badge bg-primary">Available</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="products-edit.php?id=<?= $item['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                <a 
                                    href="#=<?= $item['id']; ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Contact with manager to delete this product?')"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="mt-4">
                <h5>Total Expected Sale: TK <span id="totalExpectedSale">0.00</span></h5>
            </div>

            <?php
            } else {
                echo '<h4>No Record Found</h4>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('productSearch');
        const productTableRows = document.querySelectorAll('#productsTable tbody tr');
        const totalExpectedSaleElement = document.getElementById('totalExpectedSale');

        function calculateTotalExpectedSale() {
            let totalExpectedSale = 0;

            productTableRows.forEach(row => {
                if (row.style.display !== 'none') {
                    totalExpectedSale += parseFloat(row.getAttribute('data-expected-sale')) || 0;
                }
            });

            totalExpectedSaleElement.textContent = totalExpectedSale.toFixed(2);
        }

        function applyFilters() {
            const searchQuery = searchInput.value.toLowerCase();

            productTableRows.forEach(row => {
                const searchText = row.getAttribute('data-search').toLowerCase();
                row.style.display = searchText.includes(searchQuery) ? '' : 'none';
            });

            calculateTotalExpectedSale();
        }

        // Attach event listeners
        searchInput.addEventListener('keyup', applyFilters);

        // Initial calculation
        calculateTotalExpectedSale();
    });
</script>

<?php include('includes/footer.php'); ?>
