<?php

include('../includes/config.php');
include('../includes/database.php');
include('../includes/functions.php');

if(isset($_POST['email'])){
    $query = 'SELECT  *
            FROM users
            WHERE email = "'. $_POST['email']. '"
            AND password = "'. md5( $_POST['password']). '"
            AND active ="yes"
            LIMIT 1';

            $result = mysqli_query($connect, $query);

            if(mysqli_num_rows($result)){
                $record = mysqli_fetch_assoc($result);

                $_SESSION['id'] = $record['id'];
                $_SESSION['email'] = $record['email'];

                header('Location: dashboard.php');

                die();
            }

}




?>
<h1>Egyptian Vibes Admin</h1>
<form action="" method="post">
    <div>
    Email:
    <input type ="text" name="email">

    </div>

     <div>
    password:
    <input type ="password" name="password">

    </div>
    <div>
        <input type="submit" value="Login">
    </div>
</form>

<?php
include('../includes/footer.php');
?>
