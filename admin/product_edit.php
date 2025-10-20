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

// Retrieve and convert the product ID from the GET request to an integer
$id = (int)$_GET['product_id'];
$errors = [];

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Title ---
    // Retrieve and trim the product title from the POST data
    $title = trim($_POST['product_title'] ?? '');
    // Validate if the product title is empty
    if (empty($title)) {
        $errors[] = "Product title is required.";
    }

    // --- Description ---
    // Retrieve and trim the product description from the POST data
    $desc = trim($_POST['product_desc'] ?? '');
    // Validate if the product description is empty
    if (empty($desc)) {
        $errors[] = "Product description is required.";
    }

    // --- Price ---
    // Retrieve and convert the product price to a float
    $price = (float)($_POST['product_price'] ?? 0);
    // Validate if the product price is greater than 0
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0.";
    }

    // --- Stock ---
    // Retrieve and convert the product stock to an integer
    $stock = (int)($_POST['product_stock'] ?? -1);
    // Validate if the product stock is 0 or greater
    if ($stock < 0) {
        $errors[] = "Stock must be 0 or greater.";
    }

    // --- Size ---
    // Retrieve the product size from the POST data
    $size = $_POST['product_size'] ?? '';
    // Define allowed sizes
    $allowed_sizes = ['S','M','L','XL','XXL'];
    // Validate if the product size is in the allowed sizes
    if (!in_array($size, $allowed_sizes)) {
        $errors[] = "Invalid product size.";
    }

    // --- Categories ---
    // Validate if at least one category is selected
    if (empty($_POST['category_ids']) || !is_array($_POST['category_ids'])) {
        $errors[] = "Please select at least one category.";
    }

    // --- If no errors, update DB ---
    // If no errors, prepare the data for database update
    if (empty($errors)) {
        // Escape the data to prevent SQL injection
        $title = mysqli_real_escape_string($connect, $title);
        $desc  = mysqli_real_escape_string($connect, $desc);
        $size  = mysqli_real_escape_string($connect, $size);

        // Prepare the SQL query to update the product
        $query = "UPDATE product SET
                    product_title = '$title',
                    product_desc  = '$desc',
                    product_price = $price,
                    product_stock = $stock,
                    product_size  = '$size'
                  WHERE product_id = $id
                  LIMIT 1";
        // Execute the query
        mysqli_query($connect, $query);

        // Update category links
        // Delete existing category links for the product
        mysqli_query($connect, "DELETE FROM product_category WHERE product_id = $id");
        // Insert new category links for the product
        foreach ($_POST['category_ids'] as $cat_id) {
            $cat_id = (int)$cat_id;
            mysqli_query($connect, "INSERT INTO product_category (product_id, category_id) VALUES ($id, $cat_id)");
        }

        // Set a success message
        set_message('Product has been updated');
        // Redirect to the product list page
        header('Location: product_list.php');
        // Stop further script execution
        die();
    }
}

    // Fetch product
    // Query the database to retrieve the product information
    $query = "SELECT * FROM product WHERE product_id = $id LIMIT 1";
    $result = mysqli_query($connect, $query);
    $product = mysqli_fetch_assoc($result);

    // Check if the product exists
    if (!$product) {
        // Set a message and redirect to the product list page if the product is not found
        set_message("Product not found");
        header('Location: product_list.php');
        die();
    }

    // Fetch categories
    // Query the database to retrieve all categories, ordered by category name
    $category_query = "SELECT * FROM category ORDER BY category_name ASC";
    $category_result = mysqli_query($connect, $category_query);

    // Fetch linked category IDs
    // Query the database to retrieve the category IDs linked to the product
    $linked_query = "SELECT category_id FROM product_category WHERE product_id = $id";
    $linked_result = mysqli_query($connect, $linked_query);
    $linked_ids = [];
    // Populate the linked IDs array
    while ($row = mysqli_fetch_assoc($linked_result)) {
        $linked_ids[] = $row['category_id'];
    }
?>

<!-- HTML for the product editing form -->
<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">Edit Product</h2>
        <!-- Display input errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form for updating the product -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="product_title" class="form-label">Title</label>
                <input type="text" id="product_title" name="product_title" class="form-control"
                       value="<?php echo htmlspecialchars($product['product_title']); ?>" >
            </div>

            <div class="mb-3">
                <label for="product_desc" class="form-label">Description</label>
                <textarea id="product_desc" name="product_desc" class="form-control" rows="4"><?php echo htmlspecialchars($product['product_desc']); ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="product_price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" id="product_price" name="product_price" class="form-control"
                           value="<?php echo htmlspecialchars($product['product_price']); ?>" >
                </div>

                <div class="col-md-4">
                    <label for="product_size" class="form-label">Size</label>
                    <select id="product_size" name="product_size" class="form-select" >
                        <?php
                        // Define the allowed sizes
                        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                        // Generate options for the size dropdown
                        foreach ($sizes as $size) {
                            $selected = ($product['product_size'] === $size) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($size)."' $selected>$size</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="product_stock" class="form-label">Stock</label>
                    <input type="number" id="product_stock" name="product_stock" class="form-control"
                           value="<?php echo (int)$product['product_stock']; ?>" >
                </div>
            </div>

            <fieldset class="mb-4">
                <legend class="col-form-label fw-bold">Categories (select one or more)</legend>
                <div class="row">
                    <!-- Generate checkboxes for each category -->
                    <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="cat_<?php echo $category['category_id']; ?>"
                                       name="category_ids[]"
                                       value="<?php echo $category['category_id']; ?>"
                                       <?php echo in_array($category['category_id'], $linked_ids) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cat_<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </label>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </fieldset>

            <div class="d-flex justify-content-end gap-2">
                <!-- Submit button to update the product -->
                <button type="submit" class="btn btn-success">Update Product</button>
                <!-- Cancel button to navigate back to the product list -->
                <a href="product_list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>