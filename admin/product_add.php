<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

secure();
include('includes/header.php');

$errors = [];

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['product_title'] ?? '');
    $desc  = trim($_POST['product_desc'] ?? '');
    $product_price = (float)($_POST['product_price'] ?? 0);
    $variants = $_POST['variants'] ?? [];
    $category_ids = $_POST['category_ids'] ?? [];

    // --- Validation ---
// --- Validation ---
        if (empty($title)) $errors[] = "Product title is required.";
        if (empty($desc))  $errors[] = "Product description is required.";
        if ($product_price <= 0) $errors[] = "Product price must be greater than 0.";
        if (empty($category_ids) || !is_array($category_ids)) {
            $errors[] = "Please select at least one category.";
        }

        // --- Variant validation ---
        $hasVariant = false;

        foreach ($variants as $index => $variant) {
            $hasColor = !empty($variant['color_id']);
            $hasSize  = !empty($variant['size_id']);
            $stock    = isset($variant['stock']) ? (int)$variant['stock'] : 0;

            // If row has size or stock, it must also have a color
            if (($hasSize || $stock > 0) && !$hasColor) {
                $errors[] = "Variant row " . ($index + 1) . " has size/stock but no color selected.";
            }

            // If row is filled (color or size chosen), then stock must be > 0
            if ($hasColor || $hasSize) {
                $hasVariant = true; // at least one variant exists

                if ($stock <= 0) {
                    $errors[] = "Variant row " . ($index + 1) . " must have stock greater than 0.";
                }
            }
        }

        // If no variant rows were filled at all
        if (!$hasVariant) {
            $errors[] = "Please add at least one product variant.";
        }


    // --- If no errors, insert into DB ---
    if (empty($errors)) {
        $title = mysqli_real_escape_string($connect, $title);
        $desc  = mysqli_real_escape_string($connect, $desc);
        $product_price = mysqli_real_escape_string($connect, $product_price);

        // Insert product
        mysqli_query($connect, "
            INSERT INTO product (product_title, product_desc)
            VALUES ('$title', '$desc')
        ");
        $product_id = mysqli_insert_id($connect);

        // Insert category links
        foreach ($category_ids as $cat_id) {
            $cat_id = (int)$cat_id;
            mysqli_query($connect, "
                INSERT INTO product_category (product_id, category_id)
                VALUES ($product_id, $cat_id)
            ");
        }

        // Insert variants
        foreach ($variants as $variant) {
            $color_id = !empty($variant['color_id']) ? (int)$variant['color_id'] : null;
            $size_id  = !empty($variant['size_id']) ? (int)$variant['size_id'] : 20; // fallback
            $stock    = isset($variant['stock']) ? (int)$variant['stock'] : 0;

            // Skip completely empty rows
            if (!$color_id && !$size_id && $stock <= 0) {
                continue;
            }

            $price = $product_price;
            $sku = generateSkuWithIds($title, $size_id, $color_id);
            $sku = mysqli_real_escape_string($connect, $sku);

            $color_sql = $color_id !== null ? $color_id : "NULL";
            $size_sql  = $size_id !== null ? $size_id : "NULL";

            mysqli_query($connect, "
                INSERT INTO product_variants (
                    product_id, size_id, color_id, sku, price, stock_qty, available
                ) VALUES (
                    $product_id, $size_sql, $color_sql, '$sku', $price, $stock, 'Yes'
                )
            ");
        }


        set_message('A new product has been added');
        header('Location: product_list.php');
        die();
    }
}

        // --- Fetch sizes, colors, categories for form ---
        $size_result     = mysqli_query($connect, "SELECT * FROM product_size ORDER BY size_id ASC");
        $color_result    = mysqli_query($connect, "SELECT * FROM product_color ORDER BY color_name ASC");
        $category_result = mysqli_query($connect, "SELECT * FROM category ORDER BY category_name ASC");

        // Store arrays for dropdowns
        $sizes  = [];
        $colors = [];
        mysqli_data_seek($size_result, 0);
        while ($s = mysqli_fetch_assoc($size_result)) $sizes[] = $s;
        mysqli_data_seek($color_result, 0);
        while ($c = mysqli_fetch_assoc($color_result)) $colors[] = $c;
        ?>

        <div class="container mt-5">
            <h2 class="mb-4">Add Product</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="card shadow-sm p-4">
                <!-- Title -->
                <div class="mb-3 w-50">
                    <label for="product_title" class="form-label">Title</label>
                    <input type="text" id="product_title" name="product_title" 
                        class="form-control"
                        placeholder="e.g. Egyptian Cotton T-Shirt"
                        value="<?= htmlspecialchars($_POST['product_title'] ?? '') ?>">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="product_desc" class="form-label">Description</label>
                    <textarea id="product_desc" name="product_desc" 
                            class="form-control" rows="4"
                            placeholder="What customers will read before buying"><?= htmlspecialchars($_POST['product_desc'] ?? '') ?></textarea>
                </div>

        <!-- Product Price -->
        <div class="mb-3 w-25">
            <label for="product_price" class="form-label">Default Product Price (CAD)</label>
            <input type="number" step="0.01" id="product_price" name="product_price" 
                   class="form-control"
                   placeholder="e.g. 29.99"
                   value="<?= htmlspecialchars($_POST['product_price'] ?? '') ?>">
        </div>

        <!-- Variants -->
       <h5 class="mt-4">Variants (Color + Size + Stock)</h5>

            <div id="variants-container">
                <?php 
                $rows = $_POST['variants'] ?? array_fill(0, 1, []);
                foreach ($rows as $i => $variant): ?>
                    <div class="row mb-2 variant-row align-items-center">
                <!-- Color -->
                <div class="col-md-3">
                    <select name="variants[<?= $i ?>][color_id]" class="form-select">
                        <option value="">Color</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= $color['color_id'] ?>"
                                <?= (($variant['color_id'] ?? '') == $color['color_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($color['color_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Size -->
                <div class="col-md-3">
                    <select name="variants[<?= $i ?>][size_id]" class="form-select">
                        <option value="">Size</option>
                        <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size['size_id'] ?>"
                                <?= (($variant['size_id'] ?? '') == $size['size_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($size['size_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Stock -->
                <div class="col-md-3">
                    <input type="number" min="1" 
                        name="variants[<?= $i ?>][stock]" 
                        class="form-control"
                        placeholder="Stock quantity"
                        value="<?= htmlspecialchars($variant['stock'] ?? '') ?>">
                </div>

                <!-- Delete Button -->
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-sm delete-variant w-100" title="Delete variant">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Button -->
             <div class="text-start">
                     <button type="button" id="add-variant" class="btn btn-outline-primary mt-2 w-auto">
                + Add another variant
            </button>                   
             </div>
            

        <!-- Categories -->
        <fieldset class="mb-4 mt-4">
            <legend class="col-form-label fw-bold">Categories (select one or more)</legend>
            <div class="row">
                <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="cat_<?= $category['category_id'] ?>"
                                   name="category_ids[]"
                                   value="<?= $category['category_id'] ?>"
                                   <?= in_array($category['category_id'], $_POST['category_ids'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cat_<?= $category['category_id'] ?>">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </fieldset>

        <!-- Buttons -->
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