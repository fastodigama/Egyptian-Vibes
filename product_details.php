<?php


include('admin/includes/database.php');
include('frontend_includes/header.php');


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
        <div class="details-image-container">
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
             <!-- slider buttons -->
            <button id="previous" class="slider-btn">❮</button>
            <button id="next" class="slider-btn">❯</button>
        </div>
        <div class="product-info">
           <h2><?php echo htmlspecialchars($product["product_title"]); ?> </h2> 
        
           <p id="price">$ <?php echo htmlspecialchars($product["product_price"]); ?> CAD</p>
       
            <p id="description"> <?php echo htmlspecialchars($product["product_desc"]); ?> </p>
            <!-- add to cart logic -->
             <form action="set_cart.php" method="get">
                <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" name="title" value="<?php echo $product['product_title']; ?>">
                <input type="hidden" name="price" value="<?php echo $product['product_price']; ?>">
                <label for="qty_<?php echo $product['product_id'];?>">Qty:</label>
                <input type="number" class="cart-input" name="qty" id="qty_<?php echo $product['product_id']; ?>" value="1" min="1" class="qty-input">
                <button type="submit" class="add-to-cart-btn">Add to cart</button>
             </form>
        </div>
        
    <?php endif; ?>
    

<?php include('frontend_includes/footer.php') ?>
 