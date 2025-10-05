<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

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
    set_message('A new product has been added');

    header('Location: product_list.php');
    die();
}

?>

<h2>Add product</h2>

<form action="" method="POST">
    <div>
        Title:
        <input type="text" name="product_title">
    </div>
    <div>
        Description:
        <textarea name="product_desc"></textarea>
    </div>
    <div>
        Price:
        <input type="text" name="product_price">
    </div>
    <div>
        Size:
        <select name="product_size">
        <?php
        $values = array('S', 'M', 'L', 'XL', 'XXL');
        foreach ($values as $value) {
            echo '<option value="'.htmlspecialchars($value).'">'.$value.'</option>';
        }
        ?>
        </select>
    </div>
    <div>
        Stock:
        <input type="number" name="product_stock">
    </div>

    <input type="submit" value="Add Product">
    <a href="product_list.php"><button type="button">Cancel</button></a>
</form>
