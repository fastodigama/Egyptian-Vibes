<?php
include('frontend_includes/config.php');

// Handle item removal
if(isset($_GET['remove'])){
    $removeId = $_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
    header('Location: view_cart.php');
    die;
}

// Handle update quantity
if($_SERVER['REQUEST_METHOD']=== 'POST' && isset($_POST['update_qty'])) {
    $updateId = $_POST['product_id'];
    $newQty = (int) $_POST['new_quantity'];

    if(isset($_SESSION['cart'][$updateId])){
        if($newQty > 0){
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
foreach ($_SESSION['cart'] as $id => $item):
    $total = $item['price'] * $item['quantity'];
    $subtotal += $total;
?>
<div class="cart-item">
    <div class="cart-thumbnail">
        <img src="<?php echo htmlspecialchars($item['photo']); ?>" 
             alt="<?php echo htmlspecialchars($item['title']); ?>">
    </div>
    <div class="cart-details">
        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
        <form action="view_cart.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
            <input type="number" name="new_quantity" value="<?php echo $item['quantity']; ?>" min="0">
            <button type="submit" name="update_qty">Update</button>
        </form>
        <p>Total: $<?php echo number_format($total, 2); ?></p>
        <a href="view_cart.php?remove=<?php echo $id; ?>" class="remove-btn">Remove</a>
    </div>
</div>
<?php endforeach; ?>

<?php
$tax = $subtotal * $taxRate;
$grandTotal = $subtotal + $shipping + $tax;
?>

<div class="cart-summary">
    <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
    <p>Shipping: $<?php echo number_format($shipping, 2); ?></p>
    <p>Tax (<?php echo ($taxRate*100); ?>%): $<?php echo number_format($tax, 2); ?></p>
    <hr>
    <p><strong>Total: $<?php echo number_format($grandTotal, 2); ?></strong></p>
</div>

<?php else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>

<?php include('frontend_includes/footer.php'); ?>
