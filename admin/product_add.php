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
    //categories logic

       // Check if at least one category is selected
    if (!$_POST['category_ids']) {
        set_message('Please select at least one category');
        header('Location: product_add.php');
        die();
    }
    //get the new product id to assign categories to it
    $product_id = mysqli_insert_id($connect);
    //insert the record into the joint table
    if($_POST['category_ids']){
        foreach($_POST['category_ids'] as $cat_id){
            $cat_id = (int)$cat_id; //for security
            $query = "INSERT INTO product_category(product_id,category_id) VALUES ($product_id, $cat_id)";

            mysqli_query($connect,$query);
        }
    }



    set_message('A new product has been added');

    header('Location: product_list.php');
    die();
}

//fetching categories

$category_query = "SELECT * FROM category
ORDER BY category_name ASC";
$category_result = mysqli_query($connect, $category_query);
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
    <div>
        <fieldset>

        <legend>Categories (select one or more) </legend>
        
        <?php while($category = mysqli_fetch_assoc($category_result)): ?>
            <div>

            
            <label>
                <input type="checkbox" name="category_ids[]" value="<?php echo $category['category_id']; ?>">
                <?php echo htmlspecialchars($category['category_name'])?>
            </label>
            
    </div>

    <?php endwhile;?>
    </fieldset>
</div>
    <input type="submit" value="Add Product">
    <a href="product_list.php"><button type="button">Cancel</button></a>
</form>
