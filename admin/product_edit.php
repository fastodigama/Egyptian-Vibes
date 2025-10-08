<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

$id = (int)$_GET['product_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Title ---
    $title = trim($_POST['product_title'] ?? '');
    if (empty($title)) {
        $errors[] = "Product title is required.";
    }

    // --- Description ---
    $desc = trim($_POST['product_desc'] ?? '');
    if (empty($desc)) {
        $errors[] = "Product description is required.";
    }

    // --- Price ---
    $price = (float)($_POST['product_price'] ?? 0);
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0.";
    }

    // --- Stock ---
    $stock = (int)($_POST['product_stock'] ?? -1);
    if ($stock < 0) {
        $errors[] = "Stock must be 0 or greater.";
    }

    // --- Size ---
    $size = $_POST['product_size'] ?? '';
    $allowed_sizes = ['S','M','L','XL','XXL'];
    if (!in_array($size, $allowed_sizes)) {
        $errors[] = "Invalid product size.";
    }

    // --- Categories ---
    if (empty($_POST['category_ids']) || !is_array($_POST['category_ids'])) {
        $errors[] = "Please select at least one category.";
    }

    // --- If no errors, update DB ---
    if (empty($errors)) {
        $title = mysqli_real_escape_string($connect, $title);
        $desc  = mysqli_real_escape_string($connect, $desc);
        $size  = mysqli_real_escape_string($connect, $size);

        $query = "UPDATE product SET
                    product_title = '$title',
                    product_desc  = '$desc',
                    product_price = $price,
                    product_stock = $stock,
                    product_size  = '$size'
                  WHERE product_id = $id
                  LIMIT 1";
        mysqli_query($connect, $query);

        // Update category links
        mysqli_query($connect, "DELETE FROM product_category WHERE product_id = $id");
        foreach ($_POST['category_ids'] as $cat_id) {
            $cat_id = (int)$cat_id;
            mysqli_query($connect, "INSERT INTO product_category (product_id, category_id) VALUES ($id, $cat_id)");
        }

        set_message('Product has been updated');
        header('Location: product_list.php');
        die();
        }
    }

        // Fetch product
        $query = "SELECT * FROM product WHERE product_id = $id LIMIT 1";
        $result = mysqli_query($connect, $query);
        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            set_message("Product not found");
            header('Location: product_list.php');
            die();
        }

        // Fetch categories
        $category_query = "SELECT * FROM category ORDER BY category_name ASC";
        $category_result = mysqli_query($connect, $category_query);

        // Fetch linked category IDs
        $linked_query = "SELECT category_id FROM product_category WHERE product_id = $id";
        $linked_result = mysqli_query($connect, $linked_query);
        $linked_ids = [];
        while ($row = mysqli_fetch_assoc($linked_result)) {
            $linked_ids[] = $row['category_id'];
        }
    ?>


    <div class="container my-5">
     <div class="card shadow-sm p-4">
        <h2 class="mb-4">Edit Product</h2>
                <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

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
                        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
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
                <button type="submit" class="btn btn-success">Update Product</button>
                <a href="product_list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>
