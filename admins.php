<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config/function.php');

// Check if user is logged in
if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['status'] = "Please login to access this page!";
    header("Location: login.php");
    exit();
}

include('includes/header.php'); 
?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Admins/Staff
                <a href="admins-create.php" class="btn btn-primary float-end">Add Admin</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>

            <?php
            $admins = getAll('admins');
            if(!$admins){
                echo '<h4>Something Went Wrong!</h4>';
                return false;
            }

            if(mysqli_num_rows($admins) > 0)
            {
            ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($admin = mysqli_fetch_assoc($admins)): ?>
                        <tr>
                            <td><?= $admin['id']; ?></td>
                            <td><?= $admin['name']; ?></td>
                            <td><?= $admin['email']; ?></td>
                            <td>
                                <span class="badge bg-<?= $admin['role'] == 'admin' ? 'danger' : ($admin['role'] == 'manager' ? 'warning' : 'info'); ?>">
                                    <?= ucfirst($admin['role']); ?>
                                </span>
                            </td>
                            <td><?= $admin['phone']; ?></td>
                            <td>
                                <span class="badge bg-<?= $admin['is_ban'] == 1 ? 'danger' : 'success'; ?>">
                                    <?= $admin['is_ban'] == 1 ? 'Banned' : 'Active'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="admins-edit.php?id=<?= $admin['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="admins-delete.php?id=<?= $admin['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php
            }
            else
            {
                echo '<h4>No Records Found</h4>';
            }
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
