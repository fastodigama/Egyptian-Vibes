<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();

include('../../includes/header.php');

if (isset($_POST['first'])) {
    // Sanitize inputs
    $id     = (int)$_GET['id'];
    $first  = mysqli_real_escape_string($connect, $_POST['first']);
    $last   = mysqli_real_escape_string($connect, $_POST['last']);
    $email  = mysqli_real_escape_string($connect, $_POST['email']);
    $active = mysqli_real_escape_string($connect, $_POST['active']);

    $query = "UPDATE users SET
                first  = '$first',
                last   = '$last',
                email  = '$email',
                active = '$active'
              WHERE id = $id
              LIMIT 1";
    mysqli_query($connect, $query);

    // If new password provided
    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($connect, md5($_POST['password']));
        $query = "UPDATE users SET password = '$password' WHERE id = $id LIMIT 1";
        mysqli_query($connect, $query);
    }

    set_message('User has been updated');
    header('Location: users_list.php');
    die();
}

?>

<h2>Edit User</h2>

<?php
$id = (int)$_GET['id'];
$query = "SELECT * FROM users WHERE id = $id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

if (!$record) {
    set_message("User not found");
    header('Location: users_list.php');
    die();
}
?>

<form action="" method="POST">
    <div>
        First
        <input type="text" name="first" value="<?php echo htmlspecialchars($record['first']); ?>">
    </div>
    <div>
        Last
        <input type="text" name="last" value="<?php echo htmlspecialchars($record['last']); ?>">
    </div>
    <div>
        Email
        <input type="text" name="email" value="<?php echo htmlspecialchars($record['email']); ?>">
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
            $selected = ($record['active'] === $value) ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($value) . '</option>';
        }
        ?>
        </select>
    </div>
    <input type="submit" value="Update User">
    <a href="users_list.php"><button type="button">Cancel</button></a>
</form>
