<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_POST['product_title'])){
    $query = 'UPDATE product SET
                        product_title=  "'.$_POST['product_title'].'",
                        product_desc= "'.$_POST['product_desc'].'",
                        product_price= "'.$_POST['product_price'].'", 
                        product_stock = "'.$_POST['product_stock'].'",
                        product_size ="'.$_POST['product_size'].'"                        
                        WHERE product_id = '.$_GET['product_id'];
    mysqli_query($connect,$query); //execute the query

   

 

  

    
    set_message('Product has been updated');

   header('Location: products_list.php');
    die();

}


?>

<h2> Edit User </h2>
<!-- TODO: verify if the record exist and the id is a number  -->


<!-- prepopulate the form with existing user data -->
<?php
$query ='SELECT *
    FROM product
    WHERE product_id = '.$_GET['product_id'].'
    LIMIT 1';

$result = mysqli_query($connect,$query);
$record = mysqli_fetch_assoc($result);

?>

<form action="" method="POST">
    <div>
        Title:
        <input type="text" name="product_title" value="<?php echo $record['product_title']; ?> ">
    </div>
    <div>
        Description
        <input type="text" name="product_desc" value="<?php echo $record['product_desc']; ?>">
    </div>
    <div>
        Price:
        <input type="text" name="product_price" value="<?php echo $record['product_price']; ?>">
    </div>
    
    <div>
        Size:
        <select name="product_size">
        <?php

        $values = array('S', 'M','L','XL','XXL');
        foreach($values as $key => $value)
        {
           echo '<option value="'. $value .'"';
           if($record['product_size'] ==$value) echo 'selected';
            echo '>'.$value.'</option>';
        }

        ?>
        </select>
    </div>

    <div>
        Stock:
        <input type="number" name="product_stock" value="<?php echo $record['product_stock']; ?>">

    </div>
    <input type="submit" value="update User">
    <a href="products_list.php"><button type="button">Cancel</button></a>
</form>