<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 
        //Fetch the product name
    $query = "SELECT product_title from product WHERE product_id = {$id} LIMIT 1";
    $result = mysqli_query($connect, $query);
     $product = mysqli_fetch_assoc($result);
    //check if product exist
    if(!$product){
        set_message("Product not found");
         header('Location: product_list.php');
         die();

    }

     $product_name= $product['product_title'];
      
} else {
    set_message("user not found");
    header('Location: product_list.php');
    
    die();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Confirm Deletion</title>
</head>
<body>
    <h2>Are you sure you want to delete this product <?php echo htmlspecialchars($product_name); ?>?</h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <a href="product_list.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>

<?php
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM product WHERE product_id = {$id} LIMIT 1";
    mysqli_query($connect, $query);
    set_message("Product has been deleted");
    header('Location: product_list.php');
    die();
}
?>


