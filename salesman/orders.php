<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="mb-0">Orders</h4>
                </div>
                <div class="col-md-8">
                    <form action="" method="GET">
                        <div class="row g-1">
                            <div class="col-md-3">
                                <label for="start_date">From</label>
                                <input type="date" 
                                    name="from_date" 
                                    class="form-control"
                                    value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" 
                                />
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">To</label>
                                <input type="date" 
                                    name="to_date" 
                                    class="form-control"
                                    value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" 
                                />
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="orders.php" class="btn btn-danger">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search orders..." />
            </div>
            <?php
            if(isset($_GET['from_date']) || isset($_GET['to_date']) || isset($_GET['payment_status'])){
                $fromDate = validate($_GET['from_date']);
                $toDate = validate($_GET['to_date']);
                $paymentStatus = validate($_GET['payment_status']);

                if($fromDate != '' && $toDate != '' && $paymentStatus == ''){
                    $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
                    $query = "SELECT o.*, c.*, 
                                     oi.quantity, 
                                     p.minimum_sale_rate,
                                     SUM(oi.due) AS total_due -- Add total_due calculation
                              FROM orders o
                              LEFT JOIN customers c ON c.id = o.customer_id AND c.tenant_id = '$tenant_id'
                              LEFT JOIN order_items oi ON oi.order_id = o.id AND oi.tenant_id = '$tenant_id'
                              LEFT JOIN products p ON p.id = oi.product_id AND p.tenant_id = '$tenant_id'
                              WHERE o.order_date BETWEEN '$fromDate' AND '$toDate' AND o.tenant_id = '$tenant_id'
                              GROUP BY o.id
                              ORDER BY o.id DESC";

                } elseif($fromDate != '' && $toDate != '' && $paymentStatus != '') {
                    $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
                    $query = "SELECT o.*, c.*, 
                                     oi.quantity, 
                                     p.minimum_sale_rate,
                                     SUM(oi.due) AS total_due -- Add total_due calculation
                              FROM orders o
                              LEFT JOIN customers c ON c.id = o.customer_id AND c.tenant_id = '$tenant_id'
                              LEFT JOIN order_items oi ON oi.order_id = o.id AND oi.tenant_id = '$tenant_id'
                              LEFT JOIN products p ON p.id = oi.product_id AND p.tenant_id = '$tenant_id'
                              WHERE o.order_date BETWEEN '$fromDate' AND '$toDate' 
                              AND o.payment_mode='$paymentStatus' AND o.tenant_id = '$tenant_id'
                              GROUP BY o.id
                              ORDER BY o.id DESC";

                } else {
                    $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
                    $query = "SELECT o.*, c.*, 
                                     oi.quantity, 
                                     p.minimum_sale_rate,
                                     SUM(oi.due) AS total_due -- Add total_due calculation
                              FROM orders o
                              LEFT JOIN customers c ON c.id = o.customer_id AND c.tenant_id = '$tenant_id'
                              LEFT JOIN order_items oi ON oi.order_id = o.id AND oi.tenant_id = '$tenant_id'
                              LEFT JOIN products p ON p.id = oi.product_id AND p.tenant_id = '$tenant_id'
                              WHERE o.tenant_id = '$tenant_id'
                              GROUP BY o.id
                              ORDER BY o.id DESC";
                }
            } else {
                $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
                $query = "SELECT o.*, c.*, 
                                 oi.quantity, 
                                 p.minimum_sale_rate,
                                 SUM(oi.due) AS total_due -- Add total_due calculation
                          FROM orders o
                          LEFT JOIN customers c ON c.id = o.customer_id AND c.tenant_id = '$tenant_id'
                          LEFT JOIN order_items oi ON oi.order_id = o.id AND oi.tenant_id = '$tenant_id'
                          LEFT JOIN products p ON p.id = oi.product_id AND p.tenant_id = '$tenant_id'
                          WHERE o.tenant_id = '$tenant_id'
                          GROUP BY o.id
                          ORDER BY o.id DESC";
            }
            $orders = mysqli_query($conn, $query);
            if($orders){
                if(mysqli_num_rows($orders) > 0)
                {
                    ?>
                    <table class="table table-striped table-bordered align-items-center justify-content-center" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Tracking No.</th>
                                <th>C Name</th>
                                <th>C Phone</th>
                                <th>Description</th>
                                <th>Order Date</th>
                                <th>Order Status</th>
                                <th>Delivery Date</th>
                                <th>Total Sale</th>
                                <th>Due</th> <!-- New Due Column -->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $orderItem) : ?>
                                <tr 
                                    class="<?= ($orderItem['total_amount'] < ($orderItem['minimum_sale_rate'] * $orderItem['quantity'])) ? 'table-danger' : ''; ?>" 
                                    data-total-sale="<?= $orderItem['total_amount']; ?>"
                                >
                                    <td class="fw-bold"><?= $orderItem['tracking_no']; ?></td>
                                    <td><?= $orderItem['name']; ?></td>
                                    <td><?= $orderItem['phone']; ?></td>
                                    <td><?= $orderItem['description']; ?></td>
                                    <td><?= date('d M, Y', strtotime($orderItem['order_date'])); ?></td>
                                    <td><?= $orderItem['order_status']; ?></td>
                                    <td><?= $orderItem['delivery_date']; ?></td>
                                    <td><?= number_format($orderItem['total_amount'], 2); ?></td>
                                    <td><?= number_format($orderItem['total_due'], 2); ?></td> <!-- Display Total Due -->
                                    <td>
                                        <a href="orders-view.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-info mb-0 px-2 btn-sm">View</a>
                                        <a href="orders-view-print.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-primary mb-0 px-2 btn-sm">Print</a>
                                        <a href="edit-orders.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-warning mb-0 px-2 btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end">Total:</th>
                                <th id="totalSale">0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php
                }
                else
                {
                    echo "<h5>No Record Available</h5>";
                }
            }
            else
            {
                echo "<h5>Something Went Wrong</h5>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#ordersTable tbody tr');
        const totalSaleElement = document.getElementById('totalSale');

        function calculateTotals() {
            let totalSale = 0;
            const uniqueInvoices = new Set();

            rows.forEach(row => {
                if (row.style.display !== 'none') { // Only include visible rows
                    const trackingNo = row.querySelector('td:first-child').textContent.trim();

                    if (!uniqueInvoices.has(trackingNo)) {
                        uniqueInvoices.add(trackingNo);
                        totalSale += parseFloat(row.getAttribute('data-total-sale')) || 0;
                    }
                }
            });

            totalSaleElement.textContent = totalSale.toFixed(2);
        }

        searchInput.addEventListener('keyup', function () {
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(searchValue) ? '' : 'none';
            });

            calculateTotals(); // Recalculate totals after filtering
        });

        calculateTotals(); // Initial calculation
    });
</script>

<?php include('includes/footer.php'); ?>
