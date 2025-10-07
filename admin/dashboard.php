<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
include('includes/header.php');
secure();



?>

<h2> Dashboard </h2>

<a href="product_list.php" class="btn btn-primary">Manage Products</a>
<a href="category_list.php" class="btn btn-primary">Manage Categories</a>
<a href="users_list.php" class="btn btn-primary">Manage Users</a>

<?php
include('includes/footer.php');
?>
</div>


<?php get_message(); ?>
<div>
