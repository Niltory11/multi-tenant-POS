<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Edit Product
                <a href="products.php" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">


                <?php
                    $paramValue = checkParamId('id');
                    if(!is_numeric($paramValue)){
                        echo '<h5>Id is not an integer</h5>';
                        return false;
                    }

                    $product = getById('products',$paramValue);
                    if($product)
                    {
                        if($product['status'] == 200)
                        {
                        ?>

                        <input type="hidden" name="product_id" value="<?= $product['data']['id']; ?>" />

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Select Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    <?php
                                    $categories = getAll('categories');
                                    if($categories){
                                        if(mysqli_num_rows($categories) > 0){
                                            foreach($categories as $cateItem){
                                                ?>
                                                    <option 
                                                        value="<?= $cateItem['id']; ?>"
                                                        <?= $product['data']['category_id'] == $cateItem['id'] ? 'selected':''; ?>
                                                    >
                                                        <?= $cateItem['name']; ?>    
                                                    </option>
                                                <?php
                                            }
                                        }else{
                                            echo '<option value="">No Categories found</option>';
                                        }
                                    }else{
                                        echo '<option value="">Something Went Wrong!</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Product Name *</label>
                                <input type="text" name="name" required value="<?= $product['data']['name']; ?>" class="form-control" />
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= $product['data']['description']; ?></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Sale Rate *</label>
                                <input type="text" name="price" required value="<?= $product['data']['price']; ?>" class="form-control" />
                            </div>
                            
                            
                            
                             <div class="col-md-4 mb-3">
                                <label for="">Minimum Sale Rate *</label>
                                <input type="text" name="minimum_sale_rate" required value="<?= $product['data']['minimum_sale_rate']; ?>" class="form-control" />
                            </div>
                            
                            
                            
                            
                            <div class="col-md-4 mb-3">
                                <label for="">Quantity *</label>
                                <input type="text" name="quantity" required value="<?= $product['data']['quantity']; ?>" class="form-control" />
                            </div>
                            
                            
                            <div class="col-md-4 mb-3">
                                <label for="">Stock in *</label>
                                <input type="text" name="total" required value="<?= $product['data']['total']; ?>" class="form-control" />
                            </div>
                            


                   <div class="col-md-4 mb-3">
                        <label for="">Supplier Name *</label>
                        <input type="text" name="supplier_name" required value="<?= $product['data']['supplier_name']; ?>"  class="form-control" />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="">Memo ID *</label>
                        <input type="text" name="memo_id" required value="<?= $product['data']['memo_id']; ?>"        class="form-control" />
                    </div>


                   <div class="col-md-6 mb-3">
                        <label for="">Purchase Rate *</label>
                        <input type="text" name="purchaseRate" required value="<?= $product['data']['purchaseRate']; ?>"    class="form-control" />
                    </div>


                   <div class="col-md-3 mb-3">
                        <label for="unit">Product Unit *</label>
                        <select id="unit" name="unit"     required value="<?= $product['data']['unit']; ?>"   class="form-control" required>
                            <option value="Piece">Piece</option>
                            <option value="Set">Set</option>
                            <option value="Yard">Yard</option>
                            <option value="kg">kg</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>




                            
                            
                            
                            
                            
                            <div class="col-md-4 mb-3">
                                <label for="">Image *</label>
                                <input type="file" name="image" class="form-control" />
                                <img src="../<?= $product['data']['image']; ?>" style="width:40px;height:40px;" alt="Img" />
                            </div>

                            <div class="col-md-6">
                                <label>Status (UnChecked=Available, Checked=Stock Out)</label>
                                <br/>
                                <input type="checkbox" name="status" <?= $product['data']['status'] == true ? 'checked':''; ?> style="width:30px;height:30px";>
                            </div>
                            <div class="col-md-6 mb-3 text-end">
                                <br/>
                                <button type="submit" name="updateProduct" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                        <?php
                        }
                        else
                        {
                            echo '<h5>'.$product['message'].'</h5>';
                        }
                    }
                    else
                    {
                        echo '<h5>Something Went Wrong</h5>';
                        return false;
                    }
                ?>

            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>  
