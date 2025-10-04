<?php

require_once('../config/function.php');

// Add Admin
if (isset($_POST['saveAdmin'])) {
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = password_hash(validate($_POST['password']), PASSWORD_BCRYPT);
    $phone = validate($_POST['phone']);
    $role = validate($_POST['role']); // Role field (admin, manager, salesman)
    $is_ban = isset($_POST['is_ban']) ? 1 : 0;

    if (!empty($name) && !empty($email) && !empty($password)) {
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role' => $role,
            'is_ban' => $is_ban
        ];

        $result = insert('admins', $data);
        if ($result) {
            redirect('admins.php', 'Admin/Staff Created Successfully!');
        } else {
            redirect('admins-create.php', 'Something Went Wrong!');
        }
    } else {
        redirect('admins-create.php', 'Please fill all required fields.');
    }
}

// Add Customer
if (isset($_POST['saveCustomer'])) {
    $name = validate($_POST['name']);
    $email = validate($_POST['email']); // Treat as Address
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) ? 1 : 0; // Status for visibility

    if (!empty($name)) {
        $data = [
            'name' => $name,
            'email' => $email, // Store email field as address
            'phone' => $phone,
            'status' => $status
        ];

        $result = insert('customers', $data);
        if ($result) {
            redirect('order-create.php', 'Customer Added Successfully!');
        } else {
            redirect('customers-create.php', 'Something Went Wrong!');
        }
    } else {
        redirect('customers-create.php', 'Customer Name is Required.');
    }
}

// Update Admin
if (isset($_POST['updateAdmin'])) {
    $admin_id = validate($_POST['adminId']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $password = password_hash(validate($_POST['password']), PASSWORD_BCRYPT);
    $is_ban = isset($_POST['is_ban']) ? 1 : 0;

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $password,
        'is_ban' => $is_ban
    ];

    $result = update('admins', $admin_id, $data);
    if ($result) {
        redirect('admins.php', 'Admin/Staff Updated Successfully!');
    } else {
        redirect('admins-edit.php?id=' . $admin_id, 'Something Went Wrong!');
    }
}

// Update Customer
if (isset($_POST['updateCustomer'])) {
    $customer_id = validate($_POST['id']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) ? 1 : 0;

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'status' => $status
    ];

    $result = update('customers', $customer_id, $data);
    if ($result) {
        redirect('customers.php', 'Customer Updated Successfully!');
    } else {
        redirect('customers-edit.php?id=' . $customer_id, 'Something Went Wrong!');
    }
}











if(isset($_POST['saveProduct']))
{
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $purchaseRate = validate($_POST['purchaseRate']);
    $supplier_name = validate($_POST['supplier_name']);
    $description = validate($_POST['description']);

    $price = validate($_POST['price']);
    $memo_id = validate($_POST['memo_id']);
    $quantity = validate($_POST['quantity']);
    $minimum_sale_rate = validate($_POST['minimum_sale_rate']);
    $total =validate($_POST['total']);

    $status = isset($_POST['status']) == true ? 1:0;

    if($_FILES['image']['size'] > 0)
    {
        $path = "../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;

        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);

        $finalImage = "assets/uploads/products/".$filename;
    }
    else
    {
        $finalImage = '';
    }

    $data = [
        'category_id' => $category_id,
        'name' => $name,
        'supplier_name' => $supplier_name,
        'memo_id' => $memo_id,

        'description' => $description,
        'purchaseRate' => $purchaseRate,
        'price' => $price,
        'quantity' => $quantity,

        'minimum_sale_rate' => $minimum_sale_rate,
        'total' =>$total,

       




        'image' => $finalImage,
        'status' => $status
    ];

    $result = insert('products',$data);
    
    if($result){
        redirect('products.php','Product Created Successfully!');
    }else{
        redirect('products-create.php','Something Went Wrong!');
    }
}


// Add Category
if (isset($_POST['saveCategory'])) {
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0; // Status for visibility

    if (!empty($name)) {
        $data = [
            'name' => $name,
            'description' => $description,
            'status' => $status
        ];

        $result = insert('categories', $data);
        if ($result) {
            redirect('categories.php', 'Category Added Successfully!');
        } else {
            redirect('categories-create.php', 'Something Went Wrong!');
        }
    } else {
        redirect('categories-create.php', 'Category Name is Required.');
    }
}












// Update Product
if (isset($_POST['updateProduct'])) {
    $product_id = validate($_POST['product_id']);
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $minimum_sale_rate = validate($_POST['minimum_sale_rate']);
    $status = isset($_POST['status']) ? 1 : 0;
     $total =validate($_POST['total']);
     $memo_id = validate($_POST['memo_id']);
     $purchaseRate = validate($_POST['purchaseRate']);
     $supplier_name = validate($_POST['supplier_name']);

    $data = [
        'category_id' => $category_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'minimum_sale_rate' => $minimum_sale_rate,
        'status' => $status,
        
        'total' =>$total,
        'memo_id' => $memo_id,
        'purchaseRate' => $purchaseRate,
        'supplier_name' => $supplier_name,
    ];

    $result = update('products', $product_id, $data);
    if ($result) {
        redirect('products.php', 'Product Updated Successfully!');
    } else {
        redirect('products-edit.php?id=' . $product_id, 'Something Went Wrong!');
    }
}











// Update Order
if (isset($_POST['updateOrder'])) {
    $trackingNo = validate($_POST['tracking_no']);
    $orderDate = validate($_POST['order_date']);
    $orderStatus = validate($_POST['order_status']);
    $deliveryDate = validate($_POST['delivery_date']);
    $description = validate($_POST['description']);
    $orderItems = $_POST['order_items']; // Array of order items with due amounts

    $totalDue = 0;

    if (!empty($trackingNo) && !empty($description)) {
        // Recalculate total due from order items
        foreach ($orderItems as $item) {
            $itemDue = validate($item['due']);
            $totalDue += $itemDue;

            // Update due amounts in `order_items` table for each item
            $updateOrderItemQuery = "UPDATE order_items 
                                     SET due = ? 
                                     WHERE id = ?";
            $stmt1 = $conn->prepare($updateOrderItemQuery);
            $stmt1->bind_param('di', $itemDue, $item['id']);
            $stmt1->execute();
            $stmt1->close();
        }

        // Update the `orders` table with recalculated total due
        $updateOrderQuery = "UPDATE orders 
                             SET order_date = ?, order_status = ?, delivery_date = ?, due = ?, description = ? 
                             WHERE tracking_no = ?";
        $stmt2 = $conn->prepare($updateOrderQuery);
        $stmt2->bind_param('sssiss', $orderDate, $orderStatus, $deliveryDate, $totalDue, $description, $trackingNo);
        $orderUpdateResult = $stmt2->execute();
        $stmt2->close();

        // Redirect based on update results
        if ($orderUpdateResult) {
            redirect('orders.php', 'Order Updated Successfully!');
        } else {
            redirect('edit-order.php?track=' . $trackingNo, 'Failed to update order. Please try again.');
        }
    } else {
        redirect('edit-order.php?track=' . $trackingNo, 'All fields are required.');
    }
}

// Helper Function for Updating Orders by Tracking Number
function updateByTrackingNo($table, $trackingNo, $data) {
    global $conn;

    $setClause = '';
    foreach ($data as $column => $value) {
        $setClause .= "$column = '" . mysqli_real_escape_string($conn, $value) . "', ";
    }
    $setClause = rtrim($setClause, ', ');

    $query = "UPDATE $table SET $setClause WHERE tracking_no = '" . mysqli_real_escape_string($conn, $trackingNo) . "'";
    return mysqli_query($conn, $query);
}

?>
