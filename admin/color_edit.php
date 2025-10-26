<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

// Get color ID from URL
$color_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch existing color record
$query = "SELECT * FROM product_color WHERE color_id = $color_id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

// If no record found, redirect
if (!$record) {
    set_message("Color not found");
    header('Location: color_list.php');
    exit;
}

// Handle form submission
if (isset($_POST['color_name'])) {
    $new_color = mysqli_real_escape_string($connect, $_POST['color_name']);
    $new_hex   = mysqli_real_escape_string($connect, $_POST['hex_code']);
    

    $query = "UPDATE product_color 
              SET color_name = '$new_color',
                  hex_code = '$new_hex'
              WHERE color_id = $color_id";

    mysqli_query($connect, $query);

    set_message('Color has been updated');
    header('Location: color_list.php');
    exit;
}
?>

<h1>Edit Colour</h1>

<form method="post">
    

    <label for="color-name">Colour name</label>
    <input type="text" name="color_name" id="color-name"
           value="<?php echo htmlspecialchars($record['color_name']); ?>">
    <br>

    <label for="hex-code">Hex Code</label>
    <input type="text" name="hex_code" id="hex-code"
           value="<?php echo htmlspecialchars($record['hex_code']); ?>">
    <br>

    <input type="submit" value="Update">
</form>

<?php include('includes/footer.php'); ?>
