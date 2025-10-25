<?php

include('includes\config.php');
include('includes\database.php');
include('includes\functions.php');
include('includes\header.php');

if(isset($_POST['color_name'])) {
 
    $new_color = $_POST['color_name'];
    $new_hex = $_POST['hex_code'];
 
 $query = "INSERT INTO product_color (color_name, hex_code)
 VALUES ('$new_color', '$new_hex');";

  // Execute the query
  mysqli_query($connect, $query);

 set_message('Color has been added');

 header('Location: color_list');

 die();


}





?>

<h1>Add Colour</h1>

<form method="post">
    <label for="color-name">Colour name</label>
    <input type="text" name="color_name" id="color-name">
    <br>
    <label for="hex-code">Hex Code</label>
    <input type="text" name="hex_code" id="hex-code">
    <br>
    <input type="submit" value="submit">

</form>





<?php include('includes\footer.php'); ?>
