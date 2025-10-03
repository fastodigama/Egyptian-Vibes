<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_POST['first'])){
    $query = 'INSERT INTO users(first,last,email,password,active) 
                            VALUES (
                            "'.$_POST['first'].'",
                            "'.$_POST['last'].'",
                            "'.$_POST['email'].'",
                            "'.md5($_POST['password']).'",
                            "'.$_POST['active'].'"                        
                            )';

    mysqli_query($connect,$query);
    set_message('A new user has been added');

    header('Location: users_list.php');
    die();

}


?>

<h2> Add User </h2>

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
        foreach($values as $key => $value)
        {
           echo '<option value="'. $value .'"> '.$value.'</option>';
        }

        ?>
        </select>
    </div>
    <input type="submit" value="Add User">
</form>