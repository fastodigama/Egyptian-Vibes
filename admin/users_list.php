<?php
// Include configuration file for database connection settings
include('includes/config.php');

// Include database connection file
include('includes/database.php');

// Include functions file for reusable functions
include('includes/functions.php');

// Call a security function to ensure the page is accessed securely
secure();

// Include header file for common HTML header content
include('includes/header.php');
?>

<!-- HTML for the admin users management page -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Admin Users</h2>
        <!-- Button to navigate to the user addition page -->
        <a href="users_add.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Add User
        </a>
    </div>

    <?php
    // Prepare the SQL query to retrieve all users ordered by last name and first name
    $query = 'SELECT * FROM users ORDER BY last, first';
    // Execute the query
    $result = mysqli_query($connect, $query);
    ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Email Address</th>
                    <th scope="col">Active?</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['first']); ?></td>
                        <td><?php echo htmlspecialchars($record['last']); ?></td>
                        <td><?php echo htmlspecialchars($record['email']); ?></td>
                        <td>
                            <?php if ($record['active'] === 'Yes'): ?>
                                <!-- Display a success badge if the user is active -->
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <!-- Display a warning badge if the user is not active -->
                                <span class="badge bg-warning">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Button to navigate to the user editing page -->
                            <a href="users_edit.php?id=<?php echo (int)$record['id']; ?>"
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <!-- Button to navigate to the user deletion confirmation page -->
                            <a href="user_delete_confirm.php?delete=<?php echo (int)$record['id']; ?>"
                               class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php'); ?>