<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['product_title'])) {
    // Escape strings and cast numbers
    $title = mysqli_real_escape_string($connect, $_POST['product_title']);
    $desc  = mysqli_real_escape_string($connect, $_POST['product_desc']);
    $price = (float)$_POST['product_price'];
    $sku   = mysqli_real_escape_string($connect, generateSku($_POST['product_title']));
    $size  = mysqli_real_escape_string($connect, $_POST['product_size']);
    $stock = (int)$_POST['product_stock'];

    $query = "INSERT INTO product (product_title, product_desc, product_price, product_sku, product_size, product_stock)
              VALUES ('$title', '$desc', $price, '$sku', '$size', $stock)";

    mysqli_query($connect, $query);

    // Categories logic
    if (!$_POST['category_ids']) {
        set_message('Please select at least one category');
        header('Location: product_add.php');
        die();
    }

    $product_id = mysqli_insert_id($connect);

    if ($_POST['category_ids']) {
        foreach ($_POST['category_ids'] as $cat_id) {
            $cat_id = (int)$cat_id;
            $query = "INSERT INTO product_category(product_id,category_id) VALUES ($product_id, $cat_id)";
            mysqli_query($connect, $query);
        }
    }

    set_message('A new product has been added');
    header('Location: product_list.php');
    die();
}

// Fetching categories
$category_query = "SELECT * FROM category ORDER BY category_name ASC";
$category_result = mysqli_query($connect, $category_query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add Product</h2>
        <a href="product_list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="" method="POST" class="card shadow-sm p-4">
        <div class="mb-3">
            <label for="product_title" class="form-label">Title</label>
            <input type="text" id="product_title" name="product_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="product_desc" class="form-label">Description</label>
            <textarea id="product_desc" name="product_desc" class="form-control" rows="4"></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="product_price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" id="product_price" name="product_price" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="product_size" class="form-label">Size</label>
                <select id="product_size" name="product_size" class="form-select" required>
                    <?php
                    $values = array('S', 'M', 'L', 'XL', 'XXL');
                    foreach ($values as $value) {
                        echo '<option value="'.htmlspecialchars($value).'">'.$value.'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="product_stock" class="form-label">Stock</label>
                <input type="number" id="product_stock" name="product_stock" class="form-control" required>
            </div>
        </div>

        <fieldset class="mb-4">
            <legend class="col-form-label fw-bold">Categories (select one or more)</legend>
            <div class="row">
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
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Add Product
            </button>
            <a href="product_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>
