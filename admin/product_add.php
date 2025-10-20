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

    // --- Size ---
    // Retrieve the product size from the POST data
    $size = $_POST['product_size'] ?? '';
    // Define allowed sizes
    $allowed_sizes = ['S','M','L','XL','XXL'];
    // Validate if the product size is in the allowed sizes
    if (!in_array($size, $allowed_sizes)) {
        $errors[] = "Invalid product size.";
    }

    // --- Stock ---
    // Retrieve and convert the product stock to an integer
    $stock = (int)($_POST['product_stock'] ?? -1);
    // Validate if the product stock is 0 or greater
    if ($stock < 0) {
        $errors[] = "Stock must be 0 or greater.";
    }

    // --- Categories ---
    // Validate if at least one category is selected
    if (empty($_POST['category_ids']) || !is_array($_POST['category_ids'])) {
        $errors[] = "Please select at least one category.";
    }

    // --- If no errors, insert into DB ---
    // If no errors, prepare the data for database insertion
    if (empty($errors)) {
        // Escape the data to prevent SQL injection
        $title = mysqli_real_escape_string($connect, $title);
        $desc  = mysqli_real_escape_string($connect, $desc);
        $sku   = mysqli_real_escape_string($connect, generateSku($title));
        $size  = mysqli_real_escape_string($connect, $size);

        // Prepare the SQL query to insert the new product
        $query = "INSERT INTO product (product_title, product_desc, product_price, product_sku, product_size, product_stock)
                  VALUES ('$title', '$desc', $price, '$sku', '$size', $stock)";
        // Execute the query
        mysqli_query($connect, $query);

        // Retrieve the ID of the newly inserted product
        $product_id = mysqli_insert_id($connect);

        // Insert the product into the product_category table for each selected category
        foreach ($_POST['category_ids'] as $cat_id) {
            $cat_id = (int)$cat_id;
            $query = "INSERT INTO product_category(product_id, category_id) VALUES ($product_id, $cat_id)";
            mysqli_query($connect, $query);
        }

        // Set a success message
        set_message('A new product has been added');
        // Redirect to the product list page
        header('Location: product_list.php');
        // Stop further script execution
        die();
    }
}

    // Fetch categories
    // Query the database to retrieve all categories, ordered by category name
    $category_query = "SELECT * FROM category ORDER BY category_name ASC";
    $category_result = mysqli_query($connect, $category_query);
?>

<!-- HTML for the product addition form -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add Product</h2>
        <!-- Button to navigate back to the product list page -->
        <a href="product_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

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

    <!-- Form for adding a new product -->
    <form action="" method="POST" class="card shadow-sm p-4">
        <div class="mb-3">
            <label for="product_title" class="form-label">Title</label>
            <input type="text" id="product_title" name="product_title" class="form-control">
        </div>

        <div class="mb-3">
            <label for="product_desc" class="form-label">Description</label>
            <textarea id="product_desc" name="product_desc" class="form-control" rows="4"></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="product_price" class="form-label">Price (CAD)</label>
                <input type="number" step="0.01" id="product_price" name="product_price" class="form-control" >
            </div>

            <div class="col-md-4 mb-3">
                <label for="product_size" class="form-label">Size</label>
                <select id="product_size" name="product_size" class="form-select" >
                    <?php
                    // Define the allowed sizes
                    $values = array('S', 'M', 'L', 'XL', 'XXL');
                    // Generate options for the size dropdown
                    foreach ($values as $value) {
                        echo '<option value="'.htmlspecialchars($value).'">'.$value.'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="product_stock" class="form-label">Stock</label>
                <input type="number" id="product_stock" name="product_stock" class="form-control" >
            </div>
        </div>

        <fieldset class="mb-4">
            <legend class="col-form-label fw-bold">Categories (select one or more)</legend>
            <div class="row">
                <!-- Generate checkboxes for each category -->
                <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="category_ids[]"
                                   id="cat_<?php echo $category['category_id']; ?>"
                                   value="<?php echo $category['category_id']; ?>">
                            <label class="form-check-label" for="cat_<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </fieldset>

        <div class="d-flex justify-content-end gap-2">
            <!-- Submit button to add the product -->
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Add Product
            </button>
            <!-- Cancel button to navigate back to the product list -->
            <a href="product_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>