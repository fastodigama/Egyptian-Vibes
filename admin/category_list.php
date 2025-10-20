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

<!-- HTML for the category management page -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Categories</h2>
        <!-- Button to navigate to the category addition page -->
        <a href="category_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Category
        </a>
    </div>

    <?php
    // Query the database to retrieve all categories, ordered by category name
    $query = 'SELECT * FROM category ORDER BY category_name';
    $result = mysqli_query($connect, $query);
    ?>

    <!-- Responsive table for displaying categories -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Category Name</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($record = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <!-- Display the category name -->
                        <td><?php echo htmlspecialchars($record['category_name']); ?></td>
                        <td class="text-center">
                            <!-- Button to navigate to the category editing page -->
                            <a href="category_edit.php?id=<?php echo $record['category_id']; ?>"
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <!-- Button to navigate to the category deletion confirmation page -->
                            <a href="category_delete_confirm.php?delete=<?php echo $record['category_id']; ?>"
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