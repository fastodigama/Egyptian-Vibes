<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

// Handle photo upload
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

// Handle photo delete
if (isset($_GET['delete'])) {
    $query = 'DELETE FROM product_photos 
              WHERE photo_id = ' . (int)$_GET['delete'] . ' 
              LIMIT 1';

    mysqli_query($connect, $query);
    header('Location: product_photo.php?product_id=' . (int)$_GET['product_id']);
    die();
}

?>

<h2>Add Photo</h2>

<!-- upload photo form -->
<form method="POST" enctype="multipart/form-data">
  <div>
    Photos:
    <input type="file" name="photos[]" multiple
           accept="image/png, image/jpeg, image/jpg, image/gif">
  </div>

  <input type="submit" value="Upload">
  <a href="product_list.php"><button type="button">Cancel</button></a>
</form>

<?php
// Fetch existing photos
$product_id = (int)$_GET['product_id'];
$query = "SELECT * FROM product_photos WHERE product_id = $product_id";
$result = mysqli_query($connect, $query);
?>

<h2>Current Photos:</h2>

<table border="1">
  <tr>
    <th>Photo</th>
    <th>Date Added</th>
    <th>Actions</th>
  </tr>
  <?php while ($record = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td>
      <?php if ($record['photo']): ?>
        <img src="<?php echo htmlspecialchars($record['photo']); ?>" width="100">
      <?php endif; ?>
    </td>
    <td><?php echo htmlspecialchars($record['dateAdded']); ?></td>
    <td>
      <a href="product_photo.php?product_id=<?php echo $product_id; ?>&delete=<?php echo (int)$record['photo_id']; ?>">Delete</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
