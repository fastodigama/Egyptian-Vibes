<?php

// Include the database connection file (sets up $connect for queries)
include('admin/includes/database.php');

// Include the header file (common HTML head, navigation, etc.)
include('frontend_includes/header.php');

// Include the configuration file (site-wide settings or constants)
include('frontend_includes/config.php');


// SQL query to fetch all products in the "Accessories" category
// - Selects all product fields (p.*)
// - Also fetches the latest photo (thumbnail) for each product
// - Joins product with category tables to filter by category name
// - Orders results by the date the product was added
$query = "SELECT p.*,
                (
                    SELECT pp.photo
                    FROM product_photos pp
                    WHERE pp.product_id = p.product_id
                    ORDER BY pp.photo_id DESC
                    LIMIT 1
                ) AS thumbnail
            FROM product p
            INNER JOIN product_category pc ON p.product_id = pc.product_id
            INNER JOIN category c ON pc.category_id = c.category_id
            WHERE c.category_name = 'Accessories'
            ORDER BY p.dateAdded;
        ";

// Run the query against the database
$result = mysqli_query($connect, $query);

?>

<!-- Page Content Starts -->

<h1>Clothing</h1> <!-- Page heading (note: currently says "Clothing" though query is for Accessories) -->

<div class="product-list">

    <!-- Check if query returned any products -->
    <?php if(mysqli_num_rows($result) > 0): ?>
        
        <!-- Loop through each product row -->
        <?php while($product = mysqli_fetch_assoc($result)): ?>

            <div class="product-card">
                <!-- Product image with link to product details page -->
                <?php echo '<a href="product_details.php?id=' . $product["product_id"] . '"><img class="img" src="' . $product["thumbnail"] . '" alt=""></a>'; ?>

                <!-- Product title -->
                <h3><?php echo $product['product_title']; ?> </h3>

                <!-- Product price -->
                <p>$ <?php echo  $product['product_price']; ?> CAD</p>
            </div>

        <?php endwhile; ?>
    <?php endif; ?>

</div>

<!-- Include footer (common closing HTML, scripts, etc.) -->
<?php include('frontend_includes/footer.php'); ?>


