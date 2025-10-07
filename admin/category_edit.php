<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();


include('includes/header.php');

if(isset($_POST['category_name'])){
    $id = (int)$_GET['id']; //convert to int for security
    $name = mysqli_real_escape_string($connect, $_POST['category_name']);
    $query = "UPDATE category SET
                        category_name =  '$name'
                        
                        WHERE category_id = $id
                        LIMIT 1";
    mysqli_query($connect,$query); //execute the query

   



    
    set_message('A Category has been updated');

   header('Location: category_list.php');
    die();

}


?>

<h2> Edit User </h2>



<!-- prepopulate the form with existing user data -->
<?php
$query ='SELECT *
    FROM category
    WHERE category_id = '.$_GET['id'].'
    LIMIT 1';

$result = mysqli_query($connect,$query);
$record = mysqli_fetch_assoc($result);

?>

<form action="" method="POST">
    <div>
        Category Name
        <input type="text" name="category_name" value="<?php echo htmlspecialchars($record['category_name']); ?> ">
    </div>
    
    <input type="submit" value="update Category">
    <a href="category_list.php"><button type="button">Cancel</button></a>
</form>