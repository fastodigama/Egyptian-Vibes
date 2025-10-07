<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Categories</h2>
        <a href="category_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Category
        </a>
    </div>

    <?php
    $query = 'SELECT * FROM category ORDER BY category_name';
    $result = mysqli_query($connect, $query);
    ?>

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
                        <td><?php echo htmlspecialchars($record['category_name']); ?></td>
                        <td class="text-center">
                            <a href="category_edit.php?id=<?php echo $record['category_id']; ?>" 
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
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
