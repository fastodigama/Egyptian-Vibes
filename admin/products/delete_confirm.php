<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
include('../../includes/header.php');
secure();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 
        //Fetch the username
    $query = "SELECT product_title from product WHERE product_id = $id LIMIT 1";
    $result = mysqli_query($connect, $query);
     $product = mysqli_fetch_assoc($result);
     $product_name= $product['product_title'];
      
} else {
    set_message("user not found");
    header('Location: products_list.php');
    
    exit;
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Confirm Deletion</title>
</head>
<body>
    <h2>Are you sure you want to delete this product <?php echo $product_name; ?>?</h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <a href="products_list.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>

<?php
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM product WHERE product_id = {$id} LIMIT 1";
    mysqli_query($connect, $query);
    // Optional: set_message() if you have a flash message system
    set_message("Product has been deleted");
    header('Location: products_list.php');
    exit;
}
?>


