<?php

// Include the database connection file (sets up $connect for queries)
include('admin/includes/database.php');

// Include the header file (common HTML head, navigation, etc.)
include('frontend_includes/header.php');


// Check if a product ID was passed in the URL (via GET request)
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Cast to integer for safety against injection

    // Query to get product details by ID
    $productQuery = "SELECT * FROM product WHERE product_id = $id";
    $productResult = mysqli_query($connect, $productQuery);
    $product = mysqli_fetch_assoc($productResult);

    // Query to get all photos for this product
    $photosQuery = "SELECT photo FROM product_photos WHERE product_id = $id";
    $photosResult = mysqli_query($connect, $photosQuery);
}
?>

<h1>Product Details</h1>

<?php if ($product): ?> <!-- Only show details if product exists -->
    <div class="product-details-container">
    <?php 
        // Fetch all product photos into an array
        $photos = [];
        while($record = mysqli_fetch_assoc($photosResult)){
            $photos[] = $record['photo'];
        }
    ?>
        <div class="details-image-container">
            <!-- Main (hero) product image -->
            <div id="heroPic">
                <img id="mainImg" src="<?php echo htmlspecialchars($photos[0]); ?>" 
                alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">
            </div>

            <!-- Thumbnail gallery -->
            <div id="gallery">
                <?php foreach($photos as $photo): ?>
                    <img src="<?php echo htmlspecialchars($photo); ?>" 
                    alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">
                <?php endforeach; ?>
            </div>

            <!-- Slider navigation buttons -->
            <button id="previous" class="slider-btn">❮</button>
            <button id="next" class="slider-btn">❯</button>
        </div>

        <!-- Product information section -->
        <div class="product-info">
            <!-- Product title -->
            <h2><?php echo htmlspecialchars($product["product_title"]); ?> </h2> 
        
            <!-- Product price -->
            <p id="price">$ <?php echo htmlspecialchars($product["product_price"]); ?> CAD</p>
       
            <!-- Product description -->
            <p id="description"><?php echo htmlspecialchars($product["product_desc"]); ?></p>

            <!-- Add to cart form -->
            <form action="set_cart.php" method="get">
                <!-- Hidden fields to pass product data -->
                <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" name="title" value="<?php echo $product['product_title']; ?>">
                <input type="hidden" name="price" value="<?php echo $product['product_price']; ?>">
                    <div class="col-md-4 mb-3">
                <label for="product_size" class="form-label">Size</label>
                <select id="product_size" name="product_size" class="form-select" >
                    <?php
                    // Define the allowed sizes
                    $values = array('S', 'M', 'L', 'XL', 'XXL');
                    // Generate options for the size dropdown
                    foreach ($values as $value) {
                        echo '<option value="'.htmlspecialchars($value).'">'.$value.'</option>';
                    }
                    ?>
                </select>
            </div>

                <!-- Quantity input -->
                <label for="qty_<?php echo $product['product_id'];?>">Qty:</label>
                <input type="number" class="cart-input" name="qty" id="qty_<?php echo $product['product_id']; ?>" value="1" min="1">

                <!-- Submit button -->
                <button type="submit" class="add-to-cart-btn">Add to cart</button>
            </form>
        </div>
        
    </div>
<?php endif; ?>

<!-- Footer include (common closing HTML, scripts, etc.) -->
<?php include('frontend_includes/footer.php') ?>
