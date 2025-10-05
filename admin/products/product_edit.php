<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

if (isset($_POST['product_title'])) {
    // Sanitize inputs
    $id     = (int)$_GET['product_id'];
    $title  = mysqli_real_escape_string($connect, $_POST['product_title']);
    $desc   = mysqli_real_escape_string($connect, $_POST['product_desc']);
    $price  = (float)$_POST['product_price'];
    $stock  = (int)$_POST['product_stock'];
    $size   = mysqli_real_escape_string($connect, $_POST['product_size']);

    $query = "UPDATE product SET
                product_title = '$title',
                product_desc  = '$desc',
                product_price = $price,
                product_stock = $stock,
                product_size  = '$size'
              WHERE product_id = $id
              LIMIT 1";

    mysqli_query($connect, $query);

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
    <input type="submit" value="Update Product">
    <a href="product_list.php"><button type="button">Cancel</button></a>
</form>
