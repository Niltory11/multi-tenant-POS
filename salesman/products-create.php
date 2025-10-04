<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Add Product
                <a href="products.php" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Select Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select Category</option>
                            <?php
                            $categories = getAll('categories');
                            if ($categories) {
                                if (mysqli_num_rows($categories) > 0) {
                                    foreach ($categories as $cateItem) {
                                        echo '<option value="' . $cateItem['id'] . '">' . $cateItem['name'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No Categories found</option>';
                                }
                            } else {
                                echo '<option value="">Something Went Wrong!</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Name *</label>
                        <input type="text" name="name" required class="form-control" />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="">Supplier Name *</label>
                        <input type="text" name="supplier_name" required class="form-control" />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="">Memo ID *</label>
                        <input type="text" name="memo_id" required class="form-control" />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="">Purchase Rate *</label>
                        <input type="text" name="purchaseRate" required class="form-control" />
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="product_unit">Product Unit *</label>
                        <select id="product_unit" name="product_unit" class="form-control" required>
                            <option value="Piece">Piece</option>
                            <option value="Set">Set</option>
                            <option value="Yard">Yard</option>
                            <option value="kg">kg</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="">Sale Rate *</label>
                        <input type="text" name="price" required class="form-control" />
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="">Quantity *</label>
                        <input type="text" name="quantity" required class="form-control" />
                    </div>



                    <div class="col-md-2 mb-3">
                        <label for="">Stock In *</label>
                        <input type="int" name="total" required class="form-control" />
                    </div>















                   

                    <div class="col-md-2 mb-3">
                        <label for="">Minimum Sale Rate *</label>
                        <input type="text" name="minimum_sale_rate" required class="form-control" />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="">Image *</label>
                        <input type="file" name="image" class="form-control" />
                    </div>

                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" name="saveProduct" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
