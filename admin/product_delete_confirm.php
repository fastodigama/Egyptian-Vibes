<?php
// Include configuration and DB connection
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

secure(); // security function

include('includes/header.php');

// --- Step 1: Validate the product to delete ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // sanitize input

    // Fetch the product title for confirmation display
    $query = "SELECT product_title FROM product WHERE product_id = {$id} LIMIT 1";
    $result = mysqli_query($connect, $query);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        // If no product found, redirect back with message
        set_message("Product not found");
        header('Location: product_list.php');
        die();
    }

    $product_name = $product['product_title'];
} else {
    // If no ID provided, redirect back
    set_message("Product not found");
    header('Location: product_list.php');
    die();
}
?>

<!-- HTML confirmation page -->
<div class="container mt-5">
    <div class="card shadow-sm border-danger">
        <div class="card-body text-center py-5">
            <h2 class="mb-4 text-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Confirm Deletion
            </h2>
            <p class="fs-5">
                Are you sure you want to delete the product
                <strong class="text-dark">"<?php echo htmlspecialchars($product_name); ?>"</strong>?
            </p>

            <!-- Confirmation form -->
            <form method="post" action="" class="mt-4">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                        <i class="bi bi-trash-fill me-1"></i> Yes, Delete
                    </button>
                    <a href="product_list.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// --- Step 2: Handle deletion ---
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);

    // Because of ON DELETE CASCADE, deleting the product will also
    // delete all related rows in product_variants, product_photos, product_category, etc.
    $query = "DELETE FROM product WHERE product_id = {$id} LIMIT 1";
    mysqli_query($connect, $query);

    set_message("Product and all related data have been deleted");
    header('Location: product_list.php');
    die();
}

include('includes/footer.php');
?>
