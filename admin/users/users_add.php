<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

if (isset($_POST['first'])) {
    // Sanitize inputs
    $first   = mysqli_real_escape_string($connect, $_POST['first']);
    $last    = mysqli_real_escape_string($connect, $_POST['last']);
    $email   = mysqli_real_escape_string($connect, $_POST['email']);
    $pass    = mysqli_real_escape_string($connect, md5($_POST['password']));
    $active  = mysqli_real_escape_string($connect, $_POST['active']);

    $query = "INSERT INTO users (first, last, email, password, active) 
              VALUES ('$first', '$last', '$email', '$pass', '$active')";

    mysqli_query($connect, $query);
    set_message('A new user has been added');

    header('Location: users_list.php');
    die();
}

?>

<h2>Add User</h2>

<form action="" method="POST">
    <div>
        First
        <input type="text" name="first">
    </div>
    <div>
        Last
        <input type="text" name="last">
    </div>
    <div>
        Email
        <input type="text" name="email">
    </div>
    <div>
        Password:
        <input type="password" name="password">
    </div>
    <div>
        Active:
        <select name="active">
        <?php
        $values = array('Yes', 'No');
        foreach ($values as $value) {
            echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</option>';
        }
        ?>
        </select>
    </div>
    <input type="submit" value="Add User">
    <a href="users_list.php"><button type="button">Cancel</button></a>
</form>
