<?php


include('admin/includes/database.php');
include('frontend_includes/header.php');

include('frontend_includes/config.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // cast to int for safety

    // Get product details
    $productQuery = "SELECT * FROM product WHERE product_id = $id";
    $productResult = mysqli_query($connect, $productQuery);
    $product = mysqli_fetch_assoc($productResult);

    // Get product photos
    $photosQuery = "SELECT photo FROM product_photos WHERE product_id = $id";
    $photosResult = mysqli_query($connect, $photosQuery);
}
?>

<h1>Product Details</h1>

<?php if ($product): ?>
    <div class="product-details-container">
    <?php 
        //fitch all photos into an array
        $photos = [];
        while($record=mysqli_fetch_assoc($photosResult)){
            $photos[] = $record['photo'];
        }
        ?>
        <div class="image-container">
            <!-- hero image -->
            <div id="heroPic">
            <img id="mainImg" src="<?php echo htmlspecialchars($photos[0]); ?>" 
            alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">
            </div>
            <!-- Gallery thumbnails -->
            <div id="gallery">
                <?php foreach($photos as $photo): ?>
            <img src="<?php echo htmlspecialchars($photo); ?>" 
            alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">
            <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info">
           <h2><?php echo htmlspecialchars($product["product_title"]); ?> </h2> 
        
           <p> <?php echo htmlspecialchars($product["product_price"]); ?> </p>
       
            <p> <?php echo htmlspecialchars($product["product_desc"]); ?> </p>
            <a href="#" class="btn" role="button"> Add to cart </a>
        </div>
        
    <?php endif; ?>

<?php include('frontend_includes/footer.php') ?>
 