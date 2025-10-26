<?php

include('includes\config.php');
include('includes\database.php');
include('includes\functions.php');
secure();
include('includes\header.php');

if(isset($_POST['color_name'])) {
 
    $new_color = $_POST['color_name'];
    $new_hex = $_POST['hex_code'];
 
 $query = "INSERT INTO product_color (color_name, hex_code)
 VALUES ('$new_color', '$new_hex');";

  // Execute the query
  mysqli_query($connect, $query);

 set_message('Color has been added');

 header('Location: color_list.php');

 die();


}





?>


<form method="post">

    <div class="container">

        <h1>Add Colour</h1>
        <p class="text-muted small mb-3">Customers will see this color name, be precise.</p>

    
    <div>
        <label for="color-name">Colour name</label>
    </div>
    <div>
        <input type="text" name="color_name" id="color-name" placeholder="Red">
    </div>
    
    
    <div>
        <label for="hex-code">Hex Code</label>
    </div>
    <div>
        <input type="text" name="hex_code" id="hex-code" placeholder="#F00000">
    </div>
   
    <div class ="mt-4">
        <input type="submit" class="btn btn-sm btn-success px-3" value="submit">
    </div>
    </div>

</form>





<?php include('includes\footer.php'); ?>
