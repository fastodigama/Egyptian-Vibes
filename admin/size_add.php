<?php

include('includes\config.php');
include('includes\database.php');
include('includes\functions.php');
secure();
include('includes\header.php');

if(isset($_POST['size_name'])) {
 
    $new_size = $_POST['size_name'];
    
 
 $query = "INSERT INTO product_size (size_name)
 VALUES ('$new_size');";

  // Execute the query
  mysqli_query($connect, $query);

 set_message('size has been added');

 header('Location: size_list.php');

 die();


}





?>


<form method="post">

    <div class="container">

        <h1>Add Colour</h1>
        <p class="text-muted small mb-3">Customers will see this size name, be precise.</p>

    
    <div>
        <label for="size-name">Colour name</label>
    </div>
    <div>
        <input type="text" name="size_name" id="size-name" placeholder="42">
    </div>
    
    
    
   
    <div class ="mt-4">
        <input type="submit" class="btn btn-sm btn-success px-3" value="submit">
    </div>
    </div>

</form>





<?php include('includes\footer.php'); ?>
