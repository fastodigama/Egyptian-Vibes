<?php
// ================================================
// Product Details Page
// Shows one product, its variants (color, size), and allows adding to cart
// ================================================

// ---------------------------
// Include dependencies
// ---------------------------

// Connect to the database (provides $connect for queries)
include('admin/includes/database.php');

// Include the shared header (HTML head, navbar, etc.)
include('frontend_includes/header.php');

// ---------------------------
// Check for a product ID in the URL
// ---------------------------
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Always cast to integer to prevent SQL injection

    // ---------------------------
    // Fetch product information
    // ---------------------------
    $productQuery = "SELECT * FROM product WHERE product_id = $id";
    $productResult = mysqli_query($connect, $productQuery);
    $product = mysqli_fetch_assoc($productResult);

    // ---------------------------
    // Fetch product photos
    // ---------------------------
    $photosQuery = "SELECT photo FROM product_photos WHERE product_id = $id";
    $photosResult = mysqli_query($connect, $photosQuery);

    // ---------------------------
    // Fetch product variants (color + size + stock info)
    // ---------------------------
    $variantsQuery = "
        SELECT pv.*, pc.color_name, pc.hex_code, ps.size_name 
        FROM product_variants pv
        LEFT JOIN product_color pc ON pv.color_id = pc.color_id
        LEFT JOIN product_size ps ON pv.size_id = ps.size_id
        WHERE pv.product_id = $id AND pv.available = 'Yes'
        ORDER BY pc.color_name, ps.size_name
    ";
    $variantsResult = mysqli_query($connect, $variantsQuery);

    // Prepare arrays for colors and sizes
    $variants = [];
    $colorsData = [];
    $sizes = [];

    while ($variant = mysqli_fetch_assoc($variantsResult)) {
        $variants[] = $variant;

        // Collect unique colors
        if ($variant['color_name'] && !isset($colorsData[$variant['color_name']])) {
            $colorsData[$variant['color_name']] = $variant['hex_code'];
        }

        // Collect unique sizes
        if ($variant['size_name'] && !in_array($variant['size_name'], $sizes)) {
            $sizes[] = $variant['size_name'];
        }
            }
            // Sort sizes in logical order
        $sizeOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        usort($sizes, function($a, $b) use ($sizeOrder) {
            $posA = array_search($a, $sizeOrder);
            $posB = array_search($b, $sizeOrder);
            return $posA - $posB;
});

}
?>

<h1>Product Details</h1>

<?php if ($product): ?> <!-- Only show if product found -->
<div class="product-details-container">

    <?php 
    // ---------------------------
    // Prepare photo array for slider/gallery
    // ---------------------------
    $photos = [];
    while ($record = mysqli_fetch_assoc($photosResult)) {
        $photos[] = $record['photo'];
    }
    ?>

    <!-- =======================
         LEFT SIDE: Product Images
    ======================== -->
    <div class="details-image-container">
        <div id="heroPic">
            <!-- Main (hero) product image -->
            <img id="mainImg" src="<?php echo htmlspecialchars($photos[0]); ?>" 
                 alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">

            <!-- Navigation buttons for image slider -->
            <button id="previous" class="slider-btn">❮</button>
            <button id="next" class="slider-btn">❯</button>
        </div>

        <!-- Thumbnail gallery -->
        <div id="gallery">
            <?php foreach ($photos as $photo): ?>
                <img src="<?php echo htmlspecialchars($photo); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_title']); ?> photo">
            <?php endforeach; ?>
        </div>
    </div>

    <!-- =======================
         RIGHT SIDE: Product Info
    ======================== -->
    <div class="product-info">
        <!-- Product Title -->
        <h2><?php echo htmlspecialchars($product["product_title"]); ?></h2>

        <!-- ---------------------------
             Product Price Display
             Shows sale price if available
        --------------------------- -->
        <div id="price" class="price-container">
            <?php 
            if (!empty($variants)) {
                // Pick first variant as default reference
                $variant = $variants[0];

                $price = (float)$variant['price'];
                $salePrice = (float)$variant['sale_price'];

                if (!empty($salePrice) && $salePrice > 0 && $salePrice < $price) {
                    // If product is on sale
                    echo '<span class="original-price" style="text-decoration: line-through; color: #999;">$ ' 
                         . number_format($price, 2) . '</span> ';
                    echo '<span class="sale-price" style="color: #e60000; font-weight: bold;">$ ' 
                         . number_format($salePrice, 2) . '</span>';
                } else {
                    // Regular price only
                    echo '<span class="regular-price" style="font-weight: bold;">$ ' 
                         . number_format($price, 2) . '</span>';
                }
            } else {
                // Fallback to product table price
                echo '<span class="regular-price" style="font-weight: bold;">$ ' 
                     . number_format($product["product_price"], 2) . '</span>';
            }
            ?> CAD
        </div>

        <!-- Product Description -->
        <p id="description"><?php echo htmlspecialchars($product["product_desc"]); ?></p>

        <!-- ---------------------------
             Add to Cart Form
        --------------------------- -->
        <form action="set_cart.php" method="get" id="addToCartForm">
            <!-- Hidden fields for backend -->
            <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
            <input type="hidden" name="title" value="<?php echo htmlspecialchars($product['product_title']); ?>">
            <input type="hidden" name="price" id="selectedPrice" value="">
            <input type="hidden" name="variant_id" id="selectedVariantId" value="">
            <input type="hidden" name="sku" id="selectedSku" value="">

            <!-- Color Selection -->
            <?php if (!empty($colorsData)): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <strong>Color:</strong> 
                        <span id="selectedColorName">Select a color</span>
                    </label>
                    <div class="color-selector">
                        <?php foreach ($colorsData as $colorName => $hexCode): ?>
                            <label class="color-option" 
                                   style="background-color: <?php echo htmlspecialchars($hexCode); ?>;"
                                   title="<?php echo htmlspecialchars($colorName); ?>"
                                   data-color="<?php echo htmlspecialchars($colorName); ?>">
                                <input type="radio" name="product_color" value="<?php echo htmlspecialchars($colorName); ?>">
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Size Selection -->
            <?php if (!empty($sizes)): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <strong>Size:</strong> 
                        <span id="selectedSizeName">Select a size</span>
                    </label>
                    <div class="size-selector">
                        <?php foreach ($sizes as $size): ?>
                            <label class="size-option" data-size="<?php echo htmlspecialchars($size); ?>">
                                <input type="radio" name="product_size" value="<?php echo htmlspecialchars($size); ?>">
                                <?php echo htmlspecialchars($size); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stock Info (hidden by default) -->
            <div id="stockInfo" style="display:none;">
                <span id="stockBadge" class="stock-badge"></span>
            </div>

            <!-- Quantity Input -->
            <div class="mb-3">
                <label for="qty_<?php echo $product['product_id'];?>"></label>
                <input type="number" placeholder="qty" class="cart-input" 
                       name="qty" id="qty_<?php echo $product['product_id']; ?>" value="1" min="1">
            </div>

            <!-- Add to Cart Button -->
            <button type="submit" class="add-to-cart-btn" id="addToCartBtn" disabled>
                Select options to continue
            </button>
        </form>
    </div> <!-- End product-info -->
</div> <!-- End product-details-container -->
<?php endif; ?>

<!-- ---------------------------
     JavaScript Data for Variants
--------------------------- -->
<script>
    // Pass PHP variant data to JS
    const productVariantsData = <?php echo json_encode($variants); ?>;
    const productIdData = <?php echo $product['product_id']; ?>;
</script>

<!-- Include footer (closes HTML structure and loads JS scripts) -->
<?php include('frontend_includes/footer.php'); ?>
