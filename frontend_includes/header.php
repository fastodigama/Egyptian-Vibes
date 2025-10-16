<?php
    include('frontend_includes/config.php');
    $cartQty = 0;

    if(isset($_SESSION['cart'])) {
        foreach($_SESSION['cart'] as $item) {
            $cartQty += $item['quantity'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FontAwesome script -->
    <script src="https://kit.fontawesome.com/0f5f52e660.js" crossorigin="anonymous"></script>
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
                <li><a href="index.php">Egyptian Vibes</a></li>
                <li><a href="#">Home</a></li>
                <li><a href="clothing.php">Clothing</a></li>
                <li><a href="accessories.php">Accessories</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Login</a></li>
                <li><a href="view_cart.php"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="black" width="24" height="24"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/></svg></a></li>
                


            </ul>


            <ul class="menu">
                <li><a href="index.php">Egyptian Vibes</a></li>
                <li class="hideOnMobile"><a href="#">Home</a></li>
                <li class="hideOnMobile"><a href="clothing.php">Clothing</a></li>
                <li class="hideOnMobile"><a href="accessories.php">Accessories</a></li>
                <li class="hideOnMobile"><a href="#">Contact</a></li>
                <li class="hideOnMobile"><a href="#">About</a></li>
                <li class="hideOnMobile"><a href="#">Login</a></li>
                <li class="hideOnMobile "><a href="view_cart.php" class="cart-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="black" width="24" height="24"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/></svg><?php if($cartQty >0): ?>
                <span class="cart-badge"><?php echo $cartQty; ?></span>
                <?php endif; ?>
                </a></li>
                

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