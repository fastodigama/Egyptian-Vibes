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

// Check if the 'delete' parameter is set in the GET request
if (isset($_GET['delete'])) {
    // Convert the 'delete' parameter to an integer to prevent SQL injection
    $id = intval($_GET['delete']);

    // Fetch the product name using the provided ID
    $query = "SELECT product_title FROM product WHERE product_id = {$id} LIMIT 1";
    $result = mysqli_query($connect, $query);
    $product = mysqli_fetch_assoc($result);

    // Check if the product exists
    if (!$product) {
        // Set a message and redirect to the product list page if the product is not found
        set_message("Product not found");
        header('Location: product_list.php');
        die();
    }

    // Retrieve the product name
    $product_name = $product['product_title'];
} else {
    // Set a message and redirect to the product list page if the 'delete' parameter is not set
    set_message("Product not found");
    header('Location: product_list.php');
    die();
}
?>

<!-- HTML for the product deletion confirmation page -->
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

            <!-- Form for confirming the deletion of the product -->
            <form method="post" action="" class="mt-4">
                <!-- Hidden input to store the product ID -->
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <!-- Submit button to confirm the deletion -->
                    <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                        <i class="bi bi-trash-fill me-1"></i> Yes, Delete
                    </button>
                    <!-- Cancel button to navigate back to the product list -->
                    <a href="product_list.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Check if the form is submitted to confirm the deletion
if (isset($_POST['confirm_delete'])) {
    // Convert the product ID to an integer to prevent SQL injection
    $id = intval($_POST['id']);
    // Prepare the SQL query to delete the product
    $query = "DELETE FROM product WHERE product_id = {$id} LIMIT 1";
    // Execute the query
    mysqli_query($connect, $query);
    // Set a success message
    set_message("Product has been deleted");
    // Redirect to the product list page
    header('Location: product_list.php');
    // Stop further script execution
    die();
}

// Include footer file for common HTML footer content
include('includes/footer.php');
?>