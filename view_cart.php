<?php
include('frontend_includes/config.php');

// Handle item removal
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
    header('Location: view_cart.php');
    die;
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $updateId = $_POST['product_id'];
    $newQty = (int) $_POST['new_quantity'];

    if (isset($_SESSION['cart'][$updateId])) {
        if ($newQty > 0) {
            $_SESSION['cart'][$updateId]['quantity'] = $newQty;
        } else {
            unset($_SESSION['cart'][$updateId]);
        }
    }
}

include('frontend_includes/header.php');
?>

<h1><i class="fa-solid fa-bag-shopping"></i> Your Cart</h1>

<?php if (!empty($_SESSION['cart'])): ?>
<?php
$subtotal = 0;
$shipping = 15.00;
$taxRate = 0.13;
?>

<div class="cart-grid">
    <!-- Column 1: Cart Items -->
    <div class="cart-items-column">
        <?php foreach ($_SESSION['cart'] as $id => $item): 
            $total = $item['price'] * $item['quantity'];
            $subtotal += $total;
        ?>
        <div class="cart-item">
            <div class="item-thumbnail">
                <img src="<?php echo htmlspecialchars($item['photo']); ?>" 
                     alt="<?php echo htmlspecialchars($item['title']); ?>">
            </div>
            <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="item-price">Price: $<?php echo number_format($item['price'], 2); ?></p>
                <p class="item-total">Total: $<?php echo number_format($total, 2); ?></p>
                
                <form action="view_cart.php" method="post" class="quantity-form">
                    <label for="qty-<?php echo $id; ?>">Quantity:</label>
                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                    <input type="number" 
                           id="qty-<?php echo $id; ?>"
                           name="new_quantity" 
                           value="<?php echo $item['quantity']; ?>" 
                           min="0">
                    <button type="submit" name="update_qty" class="btn btn-update">Update</button>
                </form>
                
                <a href="view_cart.php?remove=<?php echo $id; ?>" class="btn btn-remove">Remove</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Column 2: Order Summary -->
    <div class="cart-summary-column">
        <?php
        $tax = $subtotal * $taxRate;
        $grandTotal = $subtotal + $shipping + $tax;
        ?>
        <div class="cart-summary">
            <h2>Order Summary</h2>
            <p>Subtotal: <span class="summary-amount">$<?php echo number_format($subtotal, 2); ?></span></p>
            <p>Shipping: <span class="summary-amount">$<?php echo number_format($shipping, 2); ?></span></p>
            <p>Tax (<?php echo ($taxRate * 100); ?>%): <span class="summary-amount">$<?php echo number_format($tax, 2); ?></span></p>
            <hr>
            <p class="summary-total"><strong>Total: $<?php echo number_format($grandTotal, 2); ?></strong></p>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>
    </div>
</div>

<?php else: ?>
<p class="empty-cart-message">Your cart is empty. <a href="index.php">Continue Shopping</a></p>
<?php endif; ?>

<?php include('frontend_includes/footer.php'); ?>