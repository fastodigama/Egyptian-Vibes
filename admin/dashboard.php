<?php

include('../includes/config.php');
include('../includes/database.php');
include('../includes/functions.php');
secure();



?>
<body>
    <div>
         <h1>Egyptian Vibes Admin</h1>
         <a href="dashboard.php">Dashboard</a> 
        <a href="logout.php">Logout</a>
  <hr>  
</div>
<h2> Dashboard </h2>

<a href="projects.php">Manage Products</a>
<a href="users/users_list.php">Manage Users</a>

<?php
include('../includes/footer.php');
?>
</div>


<?php get_message(); ?>
<div>
