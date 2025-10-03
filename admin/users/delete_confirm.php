<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 
        //Fetch the username
    $query = "SELECT first, last from users WHERE id = $id LIMIT 1";
    $result = mysqli_query($connect, $query);
     $user = mysqli_fetch_assoc($result);
      $name = $user['first'] . " " . $user['last'];
} else {
    set_message("user not found");
    header('Location: users_list.php');
    
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Deletion</title>
</head>
<body>
    <h2>Are you sure you want to delete this user <?php echo $name; ?>?</h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="confirm_delete">Yes, Delete</button>
        <a href="users_list.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>

<?php
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM users WHERE id = {$id} LIMIT 1";
    mysqli_query($connect, $query);
    // Optional: set_message() if you have a flash message system
    set_message("User has been deleted");
    header('Location: users_list.php');
    exit;
}
?>


