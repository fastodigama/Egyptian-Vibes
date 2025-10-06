<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

$id = (int)$_GET['product_id'];

if (isset($_POST['product_title'])) {
    // Sanitize inputs
    $title  = mysqli_real_escape_string($connect, $_POST['product_title']);
    $desc   = mysqli_real_escape_string($connect, $_POST['product_desc']);
    $price  = (float)$_POST['product_price'];
    $stock  = (int)$_POST['product_stock'];
    $size   = mysqli_real_escape_string($connect, $_POST['product_size']);

    // Update product
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

    if (!empty($_POST['category_ids'])) {
        foreach ($_POST['category_ids'] as $cat_id) {
            $cat_id = (int)$cat_id;
            mysqli_query($connect, "INSERT INTO product_category (product_id, category_id) VALUES ($id, $cat_id)");
        }
    }

    set_message('Product has been updated');
    header('Location: product_list.php');
    die();
}
?>


<h2>Edit Product</h2>

<?php
$id = (int)$_GET['product_id'];
$query = "SELECT * FROM product WHERE product_id = $id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

if (!$record) {
    set_message("Product not found");
    header('Location: product_list.php');
    die();
}
?>



<form action="" method="POST">
    <div>
        Title:
        <input type="text" name="product_title" value="<?php echo htmlspecialchars($record['product_title']); ?>">
    </div>
    <div>
        Description:
        <textarea name="product_desc"><?php echo htmlspecialchars($record['product_desc']); ?></textarea>
    </div>
    <div>
        Price:
        <input type="text" name="product_price" value="<?php echo htmlspecialchars($record['product_price']); ?>">
    </div>
    <div>
        Size:
        <select name="product_size">
        <?php
        $values = array('S', 'M', 'L', 'XL', 'XXL');
        foreach ($values as $value) {
            $selected = ($record['product_size'] === $value) ? 'selected' : '';
            echo '<option value="'.htmlspecialchars($value).'" '.$selected.'>'.$value.'</option>';
        }
        ?>
        </select>
    </div>
    <div>
        Stock:
        <input type="number" name="product_stock" value="<?php echo (int)$record['product_stock']; ?>">
    </div>
    <?php
    // Fetch all categories
    $category_query = "SELECT * FROM category ORDER BY category_name ASC";
    $category_result = mysqli_query($connect, $category_query);

    // Fetch linked category IDs
    $linked_query = "SELECT category_id FROM product_category WHERE product_id = $id";
    $linked_result = mysqli_query($connect, $linked_query);
    $linked_ids = [];
    while ($record = mysqli_fetch_assoc($linked_result)) {
        $linked_ids[] = $record['category_id'];
}
?>
    <div>
    <fieldset>
        <legend>Categories (select one or more)</legend>
        <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
            <div>
                <label>
                    <?php
                    $checked = '';
                    foreach ($linked_ids as $linked_id) {
                        if ($linked_id == $category['category_id']) {
                            $checked = 'checked';
                            break;
                        }
                    }
                    ?>
                    <input type="checkbox" name="category_ids[]" value="<?php echo $category['category_id']; ?>" <?php echo $checked; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </label>
            </div>
        <?php endwhile; ?>
    </fieldset>
</div>
    <input type="submit" value="Update Product">
    <a href="product_list.php"><button type="button">Cancel</button></a>
</form>
