<?php
include('admin/includes/database.php');

$query = "SELECT p.*,
                (
                    SELECT pp.photo
                    FROM product_photos pp
                    WHERE pp.product_id = p.product_id
                    ORDER BY pp.photo_id DESC
                    LIMIT 1
                ) AS thumbnail
            FROM product p
            ORDER BY p.dateAdded
        ";
$result = mysqli_query($connect, $query);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Egyptian Vibes</title>
</head>

<body>
    <header id="header">

        <nav class="main-navigation">
            <ul class="menu sidebar">
                <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path
                                d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                        </svg></a></li>
                <li><a href="#">Egyptian Vibes</a></li>
                <li><a href="#">Home</a></li>
                <li><a href="#">Clothes</a></li>
                <li><a href="#">Accessories</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Login</a></li>


            </ul>


            <ul class="menu">
                <li><a href="#">Egyptian Vibes</a></li>
                <li class="hideOnMobile"><a href="#">Home</a></li>
                <li class="hideOnMobile"><a href="#">Clothes</a></li>
                <li class="hideOnMobile"><a href="#">Accessories</a></li>
                <li class="hideOnMobile"><a href="#">Contact</a></li>
                <li class="hideOnMobile"><a href="#">About</a></li>
                <li class="hideOnMobile"><a href="#">Login</a></li>

                <li class="menu-button" onclick=showSidebar()>
                    <a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                            width="24px" fill="#1f1f1f">
                            <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
                        </svg></a>
                </li>





            </ul>
        </nav>

    </header>

    <main id="main">

        <h1>Our Products</h1>

        <div class="product-list">


            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($result)): ?>

            <div class="product-card">
                <?php echo '<a href="#"><img class="img" src="' . $product["thumbnail"]. '" alt=""></a>'; ?>

                <h3><?php echo $product['product_title']; ?> </h3>
                <p><?php echo  $product['product_price']; ?></p>
                <p><?php echo $product['product_desc']; ?></p>
                <a href="#" class="btn" role="button"> Add to cart </a>
                
            </div>
            



        

        <?php endwhile; ?>
        <?php endif; ?>
</div>
    </main>

    <footer id="footer">

    </footer>
    <script>
        function showSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'flex';

        }

        function hideSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'none';

        }
    </script>

</body>

</html>