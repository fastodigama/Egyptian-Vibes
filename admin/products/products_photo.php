<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_FILES['photo'])){

    
    switch($_FILES['photo']['type'])
    {
        case 'image/png': $type = 'png'; break;
        case 'image/jpg':
        case 'image/jpeg': $type = 'jpg'; break;
        case 'image/gif': $type = 'gif'; break;
        
        default:header('Location:products_list.php');
    }

    $photo = 'data:image/'.$type.';base64,'.base64_encode(file_get_contents($_FILES['photo']['tmp_name']));

    

    $query = 'UPDATE product SET
                        product_photo =  "'.$photo.'"
                                                
                        WHERE product_id = '.$_GET['product_id'];
    mysqli_query($connect,$query); //execute the query
    set_message('Photo has been updated');

   header('Location: products_list.php');
    die();

}


?>

<h2> Edit Photo </h2>
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

<form  method="POST" enctype="multipart/form-data">
    <div>
        Photo:
        <input type="file" name="photo">
    </div>
    
    <input type="submit" value="Add photo">
    <a href="products_list.php"><button type="button">Cancel</button></a>
</form>