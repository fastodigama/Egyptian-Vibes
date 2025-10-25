<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

secure();
include('includes/header.php');

$id = (int)$_GET['product_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['product_title'] ?? '');
    $desc  = trim($_POST['product_desc'] ?? '');
    $product_price = (float)($_POST['product_price'] ?? 0);
    $variants = $_POST['variants'] ?? [];
    $category_ids = $_POST['category_ids'] ?? [];

    if (empty($title)) $errors[] = "Product title is required.";
    if (empty($desc))  $errors[] = "Product description is required.";
    if ($product_price <= 0) $errors[] = "Product price must be greater than 0.";
    if (empty($category_ids)) $errors[] = "Please select at least one category.";

    foreach ($variants as $index => $variant) {
        $hasSize  = !empty($variant['size_id']);
        $hasStock = !empty($variant['stock']) && (int)$variant['stock'] > 0;
        $hasColor = !empty($variant['color_id']);
        if (($hasSize || $hasStock) && !$hasColor) {
            $errors[] = "Variant row " . ($index + 1) . " has size/stock but no color selected.";
        }
    }

    if (empty($errors)) {
        $title = mysqli_real_escape_string($connect, $title);
        $desc  = mysqli_real_escape_string($connect, $desc);
        $product_price = mysqli_real_escape_string($connect, $product_price);

        mysqli_query($connect, "
            UPDATE product
            SET product_title = '$title',
                product_desc  = '$desc'
            WHERE product_id = $id
            LIMIT 1
        ");

        mysqli_query($connect, "DELETE FROM product_category WHERE product_id = $id");
        foreach ($category_ids as $cat_id) {
            $cat_id = (int)$cat_id;
            mysqli_query($connect, "
                INSERT INTO product_category (product_id, category_id)
                VALUES ($id, $cat_id)
            ");
        }

        mysqli_query($connect, "DELETE FROM product_variants WHERE product_id = $id");
        foreach ($variants as $variant) {
            $size_id = !empty($variant['size_id']) ? (int)$variant['size_id'] : 20;
            $color_id = !empty($variant['color_id']) ? (int)$variant['color_id'] : null;
            $stock = isset($variant['stock']) ? (int)$variant['stock'] : 0;
            $price = $product_price;

            $sku = generateSkuWithIds($title, $size_id, $color_id);
            $sku = mysqli_real_escape_string($connect, $sku);

            $color_sql = $color_id !== null ? $color_id : "NULL";
            $size_sql  = $size_id !== null ? $size_id : "NULL";

            mysqli_query($connect, "
                INSERT INTO product_variants (
                    product_id, size_id, color_id, sku, price, stock_qty, available
                ) VALUES (
                    $id, $size_sql, $color_sql, '$sku', $price, $stock, 'Yes'
                )
            ");
        }

        set_message('Product has been updated');
        header('Location: product_list.php');
        die();
    }
}

$product = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM product WHERE product_id = $id LIMIT 1"));
if (!$product) {
    set_message("Product not found");
    header('Location: product_list.php');
    die();
}

$category_result = mysqli_query($connect, "SELECT * FROM category ORDER BY category_name ASC");
$linked_ids = [];
$res = mysqli_query($connect, "SELECT category_id FROM product_category WHERE product_id = $id");
while ($row = mysqli_fetch_assoc($res)) $linked_ids[] = $row['category_id'];

$size_result  = mysqli_query($connect, "SELECT * FROM product_size ORDER BY size_name ASC");
$color_result = mysqli_query($connect, "SELECT * FROM product_color ORDER BY color_name ASC");

$sizes = [];
$colors = [];
while ($s = mysqli_fetch_assoc($size_result)) $sizes[] = $s;
while ($c = mysqli_fetch_assoc($color_result)) $colors[] = $c;

$variant_result = mysqli_query($connect, "SELECT * FROM product_variants WHERE product_id = $id");
$variant_rows = [];
$current_price = 0;
while ($row = mysqli_fetch_assoc($variant_result)) {
    $variant_rows[] = [
        'variant_id' => $row['variant_id'],
        'size_id' => $row['size_id'],
        'color_id' => $row['color_id'],
        'stock' => $row['stock_qty']
    ];
    if ($current_price === 0 && isset($row['price'])) {
        $current_price = $row['price'];
    }
}
?>

<div class="container mt-5">
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

    <form action="" method="POST" class="card shadow-sm p-4">
        <div class="mb-3 w-50">
            <label for="product_title" class="form-label">Title</label>
            <input type="text" id="product_title" name="product_title" class="form-control"
                   value="<?= htmlspecialchars($product['product_title']) ?>">
        </div>

        <div class="mb-3">
            <label for="product_desc" class="form-label">Description</label>
            <textarea id="product_desc" name="product_desc" class="form-control" rows="4"><?= htmlspecialchars($product['product_desc']) ?></textarea>
        </div>

        <div class="mb-3 w-25">
            <label for="product_price" class="form-label">Default Product Price (CAD)</label>
            <input type="number" step="0.01" id="product_price" name="product_price" class="form-control"
                   value="<?= htmlspecialchars($_POST['product_price'] ?? $current_price) ?>">
        </div>

        <h5 class="mt-4">Variants (Color + Size + Stock)</h5>
        <div id="variants-container">
            <?php 
            $rows = $_POST['variants'] ?? $variant_rows;
            if (empty($rows)) $rows = array_fill(0, 3, []);
            foreach ($rows as $i => $variant): ?>
                <div class="row mb-2 variant-row align-items-center">
                 <input type="hidden" name="variants[<?= $i ?>][variant_id]" 
                    value="<?= htmlspecialchars($variant['variant_id'] ?? '') ?>" 
                    class="variant-id">

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

                    <div class="col-md-3">
                        <input type="number" min="0" name="variants[<?= $i ?>][stock]" class="form-control"
                               placeholder="Stock quantity"
                               value="<?= htmlspecialchars($variant['stock'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger btn-sm delete-variant w-100" title="Delete variant">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-start">
            <button type="button" id="add-variant" class="btn btn-outline-primary mt-2 w-auto">
                + Add another variant
            </button>
        </div>

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
                                <?= in_array($category['category_id'], $linked_ids) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cat_<?= $category['category_id'] ?>">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </fieldset>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Update Product
            </button>
            <a href="product_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>



<?php include('includes/footer.php'); ?>
