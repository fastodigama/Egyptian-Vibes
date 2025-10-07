<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
include('includes/header.php');
secure();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 
        //Fetch the category name
    $query = "SELECT category_name from category WHERE category_id = $id LIMIT 1";
    $result = mysqli_query($connect, $query);
     $category = mysqli_fetch_assoc($result);
     //check if category exist
     if (!$category) {
    set_message("Category not found");
    header('Location: category_list.php');
    die();
}
      $category_name = $category['category_name'];
} else {
    set_message("category not found");
    header('Location: category_list.php');
    
    die();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Confirm Deletion</title>
</head>
<body>
    <h2>Are you sure you want to delete this category <?php echo htmlspecialchars($category_name); ?>?</h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <a href="category_list.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>

<?php
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM category WHERE category_id = {$id} LIMIT 1";
    mysqli_query($connect, $query);
    // Optional: set_message() if you have a flash message system
    set_message("Category has been deleted");
    header('Location: category_list.php');
    die();
}
?>


