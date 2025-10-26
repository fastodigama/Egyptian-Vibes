<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

// Get size ID from URL
$size_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch existing size record
$query = "SELECT * FROM product_size WHERE size_id = $size_id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

// If no record found, redirect
if (!$record) {
    set_message("size not found");
    header('Location: size_list.php');
    exit;
}

// Handle form submission
if (isset($_POST['size_name'])) {
    $new_size = mysqli_real_escape_string($connect, $_POST['size_name']);
    
    

    $query = "UPDATE product_size 
              SET size_name = '$new_size'
                  
              WHERE size_id = $size_id";

    mysqli_query($connect, $query);

    set_message('size has been updated');
    header('Location: size_list.php');
    exit;
}
?>

<h1>Edit Size</h1>

<form method="post">
    

    <label for="size-name">Size name</label>
    <input type="text" name="size_name" id="size-name"
           value="<?php echo htmlspecialchars($record['size_name']); ?>">
    

    

    <input type="submit" value="Update">
</form>

<?php include('includes/footer.php'); ?>
