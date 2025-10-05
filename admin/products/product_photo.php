<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if (isset($_FILES['photos'])) {
    $product_id = (int)$_GET['product_id'];

    foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK) {
           // Resize and convert to base64
            $photo = resizeImageToBase64($tmpName, 800, 800);

            if ($photo) {
                $query = "INSERT INTO product_photos (product_id, photo)
                          VALUES ($product_id, '" . mysqli_real_escape_string($connect, $photo) . "')";
                mysqli_query($connect, $query);
            }
        }
    }

    set_message('Photos have been added');
    header('Location: product_list.php');
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
<form method="POST" enctype="multipart/form-data">
  <div>
    Photos:
    <input type="file" name="photos[]" multiple
           accept="image/png, image/jpeg, image/jpg, image/gif">
  </div>

  <input type="submit" value="Upload">
  <a href="product_list.php"><button type="button">Cancel</button></a>
</form>
