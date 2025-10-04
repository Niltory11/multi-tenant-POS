<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Add Admin/Staff
                <a href="admins.php" class="btn btn-danger float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>
            <form action="code.php" method="POST">

                <div class="row">
                    <!-- Name -->
                    <div class="col-md-12 mb-3">
                        <label for="name">Name *</label>
                        <input type="text" name="name" required class="form-control" placeholder="Enter full name" />
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label for="email">Email *</label>
                        <input type="email" name="email" required class="form-control" placeholder="Enter email" />
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label for="password">Password *</label>
                        <input type="password" name="password" required class="form-control" placeholder="Enter password" />
                    </div>

                    <!-- Phone Number -->
                    <div class="col-md-6 mb-3">
                        <label for="phone">Phone Number *</label>
                        <input type="text" name="phone" required class="form-control" placeholder="Enter phone number" />
                    </div>

                    <!-- Role Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="role">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="salesman">Salesman</option>
                        </select>
                    </div>

                    <!-- Is Ban Checkbox -->
                    <div class="col-md-3 mb-3">
                        <label for="is_ban">Is Ban</label>
                        <br />
                        <input type="checkbox" name="is_ban" style="width:30px;height:30px;" />
                    </div>

                    <!-- Save Button -->
                    <div class="col-md-12 mb-3 text-end">
                        <button type="submit" name="saveAdmin" class="btn btn-primary">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
