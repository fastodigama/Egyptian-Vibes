<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_POST['category_name'])){
    $name = mysqli_real_escape_string($connect, $_POST['category_name']);
    $query = "INSERT INTO category(category_name) 
                            VALUES (
                           '$name'
                                               
                            )";

    mysqli_query($connect,$query);
    set_message('A new category has been added');

    header('Location: category_list.php');
    die();

}


?>

<h2> Add Category </h2>

<form action="" method="POST">
    <div>
        Category Name
        <input type="text" name="category_name">
    </div>
    
    <input type="submit" value="Add Category">
    <a href="category_list.php"><button type="button">Cancel</button></a>
</form>