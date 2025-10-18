<?php


include('admin/includes/database.php');
include('frontend_includes/header.php');

include('frontend_includes/config.php');

?>

<header class="image-title-overlay">
    <h1 class="title">Egyptian Vibes</h1>
    <!-- image source: https://pureboutique.ca/ -->
    <img src="IMAGES/New_Arrivals_92605665-aac7-47a9-a5de-768963e7890a_1600x1067_crop_center.webp" alt="welcome image">
    
</header>
   

<section id="suggested-articles">
    <h2>Shop by Category</h2>
    <div class="flex-container-row">
        <div class="flex-item">
            <a href="clothing.php"><img src="" alt=""></a>
            <h3><a href="clothing.php">Clothing</a></h3>
        </div>
        <div class="flex-item">
            <a href="accessories.php"><img src="" alt=""></a>
            <h3><a href="accessories.php">Accessories</a></h3>
        </div>

    </div>
</section>


<?php include('frontend_includes/footer.php'); ?>