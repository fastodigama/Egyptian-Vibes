<?php
include('admin/includes/database.php');
include('frontend_includes/config.php');
include('frontend_includes/functions.php');

customer_secure();

// Stripe Secret Key (use environment variable in production!)
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: '';
if (empty($stripeSecretKey)) {
    die('Stripe secret key is not configured. Please set the STRIPE_SECRET_KEY environment variable.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

$userId = (int) $_SESSION['id'];

// Get form data
$first = mysqli_real_escape_string($connect, trim($_POST['first'] ?? ''));
$last = mysqli_real_escape_string($connect, trim($_POST['last'] ?? ''));
$email = mysqli_real_escape_string($connect, trim($_POST['email'] ?? ''));
$address = mysqli_real_escape_string($connect, trim($_POST['address'] ?? ''));
$postalCode = mysqli_real_escape_string($connect, trim($_POST['postal_code'] ?? ''));
$paymentMethodId = $_POST['stripeToken'] ?? '';

// Validate required fields
if (empty($first) || empty($last) || empty($email) || empty($address) || empty($postalCode) || empty($paymentMethodId)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: checkout.php');
    exit;
}

// Get cart
$cartQuery = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
$cartResult = mysqli_query($connect, $cartQuery);
$cartRow = mysqli_fetch_assoc($cartResult);

if (!$cartRow) {
    $_SESSION['error'] = 'Cart not found.';
    header('Location: view_cart.php');
    exit;
}

$cartId = (int) $cartRow['cart_id'];

// Get cart items
$itemsQuery = "SELECT ci.cart_item_id, ci.quantity, ci.price_at_add_time AS price,
                      p.product_title, pv.variant_id, pv.sku,
                      pc.color_name, ps.size_name
               FROM cart_items ci
               JOIN product_variants pv ON ci.variant_id = pv.variant_id
               JOIN product p ON pv.product_id = p.product_id
               LEFT JOIN product_color pc ON pv.color_id = pc.color_id
               LEFT JOIN product_size ps ON pv.size_id = ps.size_id
               WHERE ci.cart_id = $cartId";
$itemsResult = mysqli_query($connect, $itemsQuery);
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $items[] = $row;
}

if (empty($items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: view_cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = 15.00;
$taxRate = 0.13;
$tax = $subtotal * $taxRate;
$grandTotal = $subtotal + $shipping + $tax;

// Convert to cents for Stripe
$amountInCents = round($grandTotal * 100);

// ========================================
// PROCESS STRIPE PAYMENT
// ========================================
try {
    // Initialize Stripe API
    require_once('vendor/autoload.php'); // Make sure you've installed Stripe PHP library
    \Stripe\Stripe::setApiKey($stripeSecretKey);
    
    // Create Payment Intent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amountInCents,
        'currency' => 'cad',
        'payment_method' => $paymentMethodId,
        'confirm' => true,
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never'
        ],
        'description' => 'Order for ' . $email,
        'metadata' => [
            'user_id' => $userId,
            'email' => $email
        ]
    ]);
    
    // Check payment status
    if ($paymentIntent->status !== 'succeeded') {
        throw new Exception('Payment failed. Please try again.');
    }
    
    $stripeChargeId = $paymentIntent->id;
    $paymentStatus = 'paid';
    
} catch (\Stripe\Exception\CardException $e) {
    // Card was declined
    $_SESSION['error'] = 'Payment failed: ' . $e->getError()->message;
    header('Location: checkout.php');
    exit;
} catch (Exception $e) {
    // Other error
    $_SESSION['error'] = 'Payment error: ' . $e->getMessage();
    header('Location: checkout.php');
    exit;
}

// ========================================
// CREATE ORDER IN DATABASE 
// ========================================

// Insert order
$orderQuery = "INSERT INTO orders (user_id, order_date, total, shipping_cost, tax_amount, 
                                    status, payment_method, payment_status, stripe_charge_id,
                                    shipping_address, shipping_postal_code)
               VALUES ($userId, NOW(), " . $grandTotal . ", " . $shipping . ", " . $tax . ",
                       'pending', 'stripe', '" . $paymentStatus . "', '" . mysqli_real_escape_string($connect, $stripeChargeId) . "',
                       '" . $address . "', '" . $postalCode . "')";

mysqli_query($connect, $orderQuery);
$orderId = mysqli_insert_id($connect);

// Insert order items (Using YOUR table structure)
foreach ($items as $item) {
    $itemSubtotal = $item['price'] * $item['quantity'];
    
    $itemQuery = "INSERT INTO order_items (order_id, variant_id, quantity, unit_price, subtotal, color_name, size_name, sku)
                  VALUES ($orderId, 
                          " . $item['variant_id'] . ", 
                          " . $item['quantity'] . ", 
                          " . $item['price'] . ",
                          " . $itemSubtotal . ",
                          " . ($item['color_name'] ? "'" . mysqli_real_escape_string($connect, $item['color_name']) . "'" : "NULL") . ",
                          " . ($item['size_name'] ? "'" . mysqli_real_escape_string($connect, $item['size_name']) . "'" : "NULL") . ",
                          " . ($item['sku'] ? "'" . mysqli_real_escape_string($connect, $item['sku']) . "'" : "NULL") . ")";
    mysqli_query($connect, $itemQuery);
    
    // Decrease stock
    $updateStock = "UPDATE product_variants 
                    SET stock_qty = stock_qty - " . $item['quantity'] . "
                    WHERE variant_id = " . $item['variant_id'];
    mysqli_query($connect, $updateStock);
}

// Clear cart
mysqli_query($connect, "DELETE FROM cart_items WHERE cart_id = $cartId");

// Success message
$_SESSION['success'] = 'Order placed successfully! Order #' . $orderId;

// Redirect to order confirmation
header('Location: order_confirmation.php?order_id=' . $orderId);
exit;
?>