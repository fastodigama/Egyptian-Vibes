<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_POST['product_title'])){
    $sku = generateSku($_POST['product_title']);
    $query = 'INSERT INTO product(product_title,product_desc,product_price,product_sku,product_size, product_stock) 
                            VALUES (
                            "'.$_POST['product_title'].'",
                            "'.$_POST['product_desc'].'",
                            "'.$_POST['product_price'].'",                            
                            "'.$sku.'",
                            "'.$_POST['product_size'].'",
                            "'.$_POST['product_stock'].'"

                            )';
    

    mysqli_query($connect,$query);
    set_message('A new product has been added');

    header('Location: products_list.php');
    die();

}


?>

<h2> Add product </h2>

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

        $values = array('S', 'L', 'XL', 'XXL');
        foreach($values as $key => $value)
        {
           echo '<option value="'. $value .'"> '.$value.'</option>';
        }

        ?>
        </select>
    </div>

    <div>
        Quantity:
        <input type="number" name="product_stock">
    </div>

     
    <input type="submit" value="Add Product">
</form>