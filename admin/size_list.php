<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

// Handle delete logic first
if (isset($_GET['delete'])) {
    $deleteSizeId = (int) $_GET['delete'];

    if ($deleteSizeId !== 20) {
        mysqli_query($connect, "DELETE FROM product_size WHERE size_id = $deleteSizeId");
        set_message("Size deleted");
        header('Location: size_list.php');
        exit;
    } else {
        set_message("You cannot delete One Size — it is the default.");
        header('Location: size_list.php');
        exit;
    }
}

// Fetch all sizes AFTER delete logic
$result = mysqli_query($connect, "SELECT * FROM product_size ORDER BY size_name ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Size List</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="size_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add size
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Size Name</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($size = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($size['size_name']) ?></td>
                        <td class="text-center">
                            <a href="size_edit.php?id=<?= (int)$size['size_id'] ?>"
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="size_list.php?delete=<?= (int)$size['size_id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this size?');">
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
