<?php
// Include configuration and DB connection
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

secure(); // your security function

include('includes/header.php');

// --- Build query depending on search ---
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $searchInput = mysqli_real_escape_string($connect, $_GET['search']);

    $query = "
        SELECT p.product_id, p.product_title,
               (
                   SELECT pp.photo
                   FROM product_photos pp
                   WHERE pp.product_id = p.product_id
                   ORDER BY pp.photo_id DESC
                   LIMIT 1
               ) AS thumbnail
        FROM product p
        WHERE p.product_title LIKE '%$searchInput%'
           
        ORDER BY p.dateAdded DESC
    ";
} else {
    $query = "
        SELECT p.product_id, p.product_title,
               (
                   SELECT pp.photo
                   FROM product_photos pp
                   WHERE pp.product_id = p.product_id
                   ORDER BY pp.photo_id DESC
                   LIMIT 1
               ) AS thumbnail
        FROM product p
        ORDER BY p.dateAdded DESC
    ";
}

$result = mysqli_query($connect, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Products</h2>
        <a href="product_add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    <!-- Search Form -->
    <form method="get" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-4">
                <input
                    type="search"
                    name="search"
                    class="form-control"
                    placeholder="Search products..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars((string)$_GET['search']) : ''; ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
            <div class="col-auto">
                <a href="product_list.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Photos</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($record = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($record['thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars((string)$record['thumbnail']); ?>"
                                         alt="Thumbnail"
                                         class="img-thumbnail"
                                         style="width: 100px; height: auto;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars((string)$record['product_title']); ?></td>
                            <td>
                                <a href="product_photo.php?product_id=<?php echo (int)$record['product_id']; ?>"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-images"></i> Add/Edit Photos
                                </a>
                            </td>
                            <td>
                                <a href="product_edit.php?product_id=<?php echo (int)$record['product_id']; ?>"
                                   class="btn btn-sm btn-warning me-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="product_delete_confirm.php?delete=<?php echo (int)$record['product_id']; ?>"
                                   class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-muted">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php'); ?>
