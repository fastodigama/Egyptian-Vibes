<?php

// Include the database connection file (sets up $connect for queries)
include('admin/includes/database.php');

// Include the header file (common HTML head, navigation, etc.)
include('frontend_includes/header.php');

// Include the configuration file (site-wide settings or constants)
include('frontend_includes/config.php');

?>

<!-- Hero/Header Section -->
<header class="image-title-overlay">
    <!-- Main site title -->
    <h1 class="title">Egyptian Vibes</h1>
    
    <!-- Background/hero image for the homepage -->
    <!-- image source credit: https://pureboutique.ca/ -->
    <img src="IMAGES/New_Arrivals_92605665-aac7-47a9-a5de-768963e7890a_1600x1067_crop_center.webp" alt="welcome image">

    
</header>
   

<!-- Category Section -->
<section id="suggested-articles">
    <h2>Shop by Category</h2>
    
    <!-- Flex container for category cards -->
    <div class="flex-container-row">
        
        <!-- Category: Clothing -->
        <div class="flex-item">
            <!-- Link to clothing page -->
            <a href="clothing.php"><img src="" alt=""></a>
            <h3><a href="clothing.php">Clothing</a></h3>
        </div>
        
        <!-- Category: Accessories -->
        <div class="flex-item">
            <!-- Link to accessories page -->
            <a href="accessories.php"><img src="" alt=""></a>
            <h3><a href="accessories.php">Accessories</a></h3>
        </div>

    </div>
</section>

<!-- Footer include (common closing HTML, scripts, etc.) -->
<?php include('frontend_includes/footer.php'); ?>
