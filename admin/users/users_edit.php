<?php

include('../../includes/config.php');
include('../../includes/database.php');
include('../../includes/functions.php');
secure();


include('../../includes/header.php');

if(isset($_POST['first'])){
    $query = 'UPDATE users SET
                        first=  "'.$_POST['first'].'",
                        last= "'.$_POST['last'].'",
                        email= "'.$_POST['email'].'", 
                        active ="'.$_POST['active'].'"                        
                        WHERE id = '.$_GET['id'];
    mysqli_query($connect,$query); //execute the query

   

    //if new password provided

    if($_POST['password']){
        $query  = 'UPDATE users SET
        password = "'.md5($_POST['password']).'"
        WHERE id = '.$_GET['id'];
        mysqli_query($connect, $query); //execute the query
    }

  

    
    set_message('A new user has been updated');

   header('Location: users_list.php');
    die();

}


?>

<h2> Edit User </h2>
<!-- TODO: verify if the record exist and the id is a number  -->


<!-- prepopulate the form with existing user data -->
<?php
$query ='SELECT *
    FROM users
    WHERE id = '.$_GET['id'].'
    LIMIT 1';

$result = mysqli_query($connect,$query);
$record = mysqli_fetch_assoc($result);

?>

<form action="" method="POST">
    <div>
        First
        <input type="text" name="first" value="<?php echo $record['first']; ?> ">
    </div>
    <div>
        Last
        <input type="text" name="last" value="<?php echo $record['last']; ?>">
    </div>
    <div>
        Email
        <input type="text" name="email" value="<?php echo $record['email']; ?>">
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
           echo '<option value="'. $value .'"';
           if($record['active'] ==$value) echo 'selected';
            echo '>'.$value.'</option>';
        }

        ?>
        </select>
    </div>
    <input type="submit" value="update User">
    <a href="users_list.php"><button type="button">Cancel</button></a>
</form>