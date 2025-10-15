<?php


include('admin/includes/database.php');
include('frontend_includes/header.php');

include('frontend_includes/config.php');


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
$result = mysqli_query($connect, $query);


?>




        <h1>Clothing</h1>

        <div class="product-list">


            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($result)): ?>

            <div class="product-card">
                <?php echo '<a href="#"><img class="img" src="' . $product["thumbnail"]. '" alt=""></a>'; ?>

                <h3><?php echo $product['product_title']; ?> </h3>
                <p><?php echo  $product['product_price']; ?></p>
                <p><?php echo $product['product_desc']; ?></p>
                <!-- <a href="#" class="btn" role="button"> Add to cart </a> -->
                
            </div>
            



        

        <?php endwhile; ?>
        <?php endif; ?>

<?php include('frontend_includes/footer.php'); ?>