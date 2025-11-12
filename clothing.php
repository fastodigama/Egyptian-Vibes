<?php
// Include the database connection file (sets up $connect for queries)
include('admin/includes/database.php');
include('frontend_includes/header.php');
include('frontend_includes/config.php');

// ----------------------
// 1. Pagination settings
// ----------------------
$results_per_page = 12; // how many products per page

// Detect current page from URL, default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) { $page = 1; }

// ----------------------
// 2. Count total products
// ----------------------
$count_query = "SELECT COUNT(DISTINCT p.product_id) AS total
                FROM product p
                INNER JOIN product_category pc ON p.product_id = pc.product_id
                INNER JOIN category c ON pc.category_id = c.category_id
                WHERE c.category_name = 'Clothing'";

$count_result = mysqli_query($connect, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];

// Calculate total pages
$total_pages = ceil($total_products / $results_per_page);

// If page is greater than total_pages, clamp it
if ($page > $total_pages) { $page = $total_pages; }

// ----------------------
// 3. Calculate OFFSET
// ----------------------
$offset = ($page - 1) * $results_per_page;

// ----------------------
// 4. Main product query with stock check
// ----------------------
$query = "SELECT p.*, 
                 MIN(pv.price) AS price,
                 SUM(pv.stock_qty) AS total_stock,
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
          INNER JOIN product_variants pv ON p.product_id = pv.product_id
              AND pv.price > 0
          WHERE c.category_name = 'Clothing'
          GROUP BY p.product_id
          ORDER BY p.dateAdded DESC
          LIMIT $results_per_page OFFSET $offset";

$result = mysqli_query($connect, $query);
?>

<!-- Page Content Starts -->
<h1>Clothing</h1>

<div class="product-list">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($product = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                    <div class="product-image-wrapper">
                        <img class="img" src="<?php echo $product['thumbnail']; ?>" alt="">
                        <?php if ($product['total_stock'] <= 0): ?>
                            <span class="out-of-stock-badge">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </a>
                <h3><?php echo $product['product_title']; ?></h3>
                <p>$ <?php echo $product['price']; ?> CAD</p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- ---------------------- -->
<!-- 5. Pagination controls -->
<!-- ---------------------- -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong><?php echo $i; ?></strong>
        <?php else: ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next</a>
    <?php endif; ?>
</div>

<?php include('frontend_includes/footer.php'); ?>