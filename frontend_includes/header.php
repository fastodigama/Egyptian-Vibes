<?php
    // Include the configuration file
    include('frontend_includes/config.php');

    // Initialize cart quantity
    $cartQty = 0;

    // Check if the cart session is set
    if(isset($_SESSION['cart'])) {
        // Calculate the total quantity of items in the cart
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
        <nav id="main-navigation" aria-label="Main navigation">
            <ul id="main-menu">
                <li><a href="index.php">Egyptian Vibes</a></li>
                <li class="hideOnMobile"><a href="clothing.php">Clothing</a></li>
                <li class="hideOnMobile"><a href="accessories.php">Accessories</a></li>
                <li class="hideOnMobile"><a href="contact.php">Contact</a></li>
                <li class="hideOnMobile"><a href="about.php">About</a></li>
                <?php if(isset($_SESSION['id'])): ?>
                <li class="hideOnMobile"><a href="account.php">My Account</a></li>
                <li class="hideOnMobile"><a href="customer_logout.php">Logout</a></li>
                <?php else: ?>
                <li class="hideOnMobile"><a href="customer_login.php">Login</a></li>
                <li class="hideOnMobile"><a href="customer_login.php">Register</a></li>
                <?php endif; ?>
                <li class="menu-button" onclick="showSidebar()">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 0 960 960" width="26px" fill="#1f1f1f">
                            <path d="M120 720h720v80H120zm0-200h720v80H120zm0-200h720v80H120z"/>
                        </svg>
                    </a>
                </li>
            </ul>

            <ul id="sidebar">
                <li onclick="hideSidebar()">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" height="26" width="26" viewBox="0 0 960 960" fill="#1f1f1f">
                            <path d="M256 760 200 704l224-224L200 256l56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224L256 760Z"/>
                        </svg>
                    </a>
                </li>
                <li><a href="clothing.php">Clothing</a></li>
                <li><a href="accessories.php">Accessories</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="about.php">About</a></li>
               <?php if(isset($_SESSION['id'])): ?>
                <li><a href="account.php">My Account</a></li>
                <li><a href="customer_logout.php">Logout</a></li>
                <?php else: ?>
                <li><a href="customer_login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <nav id="cart-nav">
            <ul>
                <li>
                    <a href="view_cart.php" class="cart-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="26" height="26">
                            <path d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/>
                        </svg>
                        <?php if($cartQty > 0): ?>
                            <span class="cart-badge"><?php echo $cartQty; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <div class="flex-container-row">
        <main id="main">