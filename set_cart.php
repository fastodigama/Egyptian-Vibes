<?php
// ================================================
// set_cart.php
// Handles adding products/variants to the cart
// ================================================

// ---------------------------
// Include dependencies
// ---------------------------
include("frontend_includes/config.php");
include("admin/includes/database.php");

// ---------------------------
// Get product + variant info from GET
// ---------------------------
$productId   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$title       = $_GET['title'] ?? '';
$price       = isset($_GET['price']) ? (float) $_GET['price'] : 0;
$qty         = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
$variantId   = isset($_GET['variant_id']) ? (int) $_GET['variant_id'] : 0;
$color       = $_GET['product_color'] ?? '';
$size        = $_GET['product_size'] ?? '';
$sku         = $_GET['sku'] ?? '';

// ---------------------------
// Get product photo (first photo)
// ---------------------------
$photoQuery = "SELECT photo 
               FROM product_photos 
               WHERE product_id = $productId 
               ORDER BY photo_id 
               LIMIT 1";
$result = mysqli_query($connect, $photoQuery);
$photo  = mysqli_fetch_assoc($result)['photo'] ?? '';

// ---------------------------
// Branch: guest vs logged-in
// ---------------------------
if (!isset($_SESSION['id'])) {
    // ---------------------------
    // Guest user → store in SESSION
    // ---------------------------
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $key = $variantId > 0 ? $variantId : $productId;

    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'title'      => $title,
            'price'      => $price,
            'quantity'   => $qty,
            'photo'      => $photo,
            'sku'        => $sku,
            'color'      => $color,
            'size'       => $size
        ];
    }

} else {
    // ---------------------------
    // Logged-in user → store in DB
    // ---------------------------
    $userId = (int) $_SESSION['id'];

    // Ensure user has a cart
    $cartId = null;
    $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
    $res = mysqli_query($connect, $q);
    if ($row = mysqli_fetch_assoc($res)) {
        $cartId = (int) $row['cart_id'];
    } else {
        mysqli_query($connect, "INSERT INTO cart (user_id) VALUES ($userId)");
        $cartId = (int) mysqli_insert_id($connect);
    }

    // Upsert cart item
    $qItem = "SELECT cart_item_id, quantity 
              FROM cart_items 
              WHERE cart_id = $cartId AND variant_id = $variantId LIMIT 1";
    $resItem = mysqli_query($connect, $qItem);

    if ($item = mysqli_fetch_assoc($resItem)) {
        $newQty = (int)$item['quantity'] + $qty;
        mysqli_query($connect, "UPDATE cart_items 
                                SET quantity = $newQty 
                                WHERE cart_item_id = " . (int)$item['cart_item_id']);
    } else {
        $priceSafe = number_format($price, 2, '.', '');
        mysqli_query($connect, "INSERT INTO cart_items 
            (cart_id, variant_id, quantity, price_at_add_time) 
            VALUES ($cartId, $variantId, $qty, $priceSafe)");
    }
}

// ---------------------------
// Redirect back to product page
// ---------------------------
header('Location: product_details.php?id=' . $productId);
exit;
