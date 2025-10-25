<?php
include('frontend_includes/config.php');

// Handle AJAX requests for better UX
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');
    
    // Handle item removal
    if (isset($_GET['remove'])) {
        $removeId = $_GET['remove'];
        unset($_SESSION['cart'][$removeId]);
        echo json_encode(['success' => true, 'message' => 'Item removed']);
        exit;
    }
    
    // Handle update quantity
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
        $updateId = $_POST['product_id'];
        $newQty = (int) $_POST['new_quantity'];
        
        if (isset($_SESSION['cart'][$updateId])) {
            if ($newQty > 0) {
                $_SESSION['cart'][$updateId]['quantity'] = $newQty;
                echo json_encode(['success' => true, 'message' => 'Quantity updated']);
            } else {
                unset($_SESSION['cart'][$updateId]);
                echo json_encode(['success' => true, 'message' => 'Item removed']);
            }
        }
        exit;
    }
}

// Handle non-AJAX requests (fallback)
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
    header('Location: view_cart.php');
    exit;
}

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
    header('Location: view_cart.php');
    exit;
}

include('frontend_includes/header.php');
?>

<div class="cart-header">
    <h1><i class="fa-solid fa-bag-shopping"></i> Your Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
        <span class="cart-count"><?php echo count($_SESSION['cart']); ?> item(s)</span>
    <?php endif; ?>
</div>

<?php if (!empty($_SESSION['cart'])): ?>
<?php 
$subtotal = 0;
$shipping = 15.00;
$taxRate = 0.13;
?>

<div class="cart-grid">
    <!-- Column 1: Cart Items -->
    <div class="cart-items-column">
        <div class="column-header">
            <h2>Cart Items</h2>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        
        <?php foreach ($_SESSION['cart'] as $id => $item): 
            $total = $item['price'] * $item['quantity'];
            $subtotal += $total;
        ?>
        <div class="cart-item" data-item-id="<?php echo $id; ?>">
            <div class="item-thumbnail">
                <img src="<?php echo htmlspecialchars($item['photo']); ?>"
                     alt="<?php echo htmlspecialchars($item['title']); ?>">
            </div>
            <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="item-price">Unit Price: $<?php echo number_format($item['price'], 2); ?></p>
                <p class="item-total">Subtotal: <strong>$<?php echo number_format($total, 2); ?></strong></p>
                
                <div class="item-actions">
                    <div class="quantity-control">
                        <label for="qty-<?php echo $id; ?>">Qty:</label>
                        <div class="quantity-input-group">
                            <button type="button" class="qty-btn qty-decrease" data-id="<?php echo $id; ?>">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number"
                                   id="qty-<?php echo $id; ?>"
                                   class="qty-input"
                                   data-id="<?php echo $id; ?>"
                                   value="<?php echo $item['quantity']; ?>"
                                   min="1"
                                   max="99">
                            <button type="button" class="qty-btn qty-increase" data-id="<?php echo $id; ?>">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-remove" data-id="<?php echo $id; ?>">
                        <i class="fa-solid fa-trash"></i> Remove
                    </button>
                </div>
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
            <div class="summary-line">
                <span>Subtotal:</span>
                <span class="summary-amount" id="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-line">
                <span>Shipping:</span>
                <span class="summary-amount">$<?php echo number_format($shipping, 2); ?></span>
            </div>
            <div class="summary-line">
                <span>Tax (<?php echo ($taxRate * 100); ?>%):</span>
                <span class="summary-amount" id="tax">$<?php echo number_format($tax, 2); ?></span>
            </div>
            <hr>
            <div class="summary-line summary-total">
                <span><strong>Total:</strong></span>
                <span id="grand-total"><strong>$<?php echo number_format($grandTotal, 2); ?></strong></span>
            </div>
            
            <a href="checkout.php" class="btn btn-checkout">
                <i class="fa-solid fa-lock"></i> Proceed to Checkout
            </a>
            
            <div class="secure-checkout-badge">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Secure Checkout</span>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="empty-cart">
    <div class="empty-cart-icon">
        <i class="fa-solid fa-cart-shopping"></i>
    </div>
    <h2>Your cart is empty</h2>
    <p>Looks like you haven't added anything to your cart yet.</p>
    <a href="index.php" class="btn btn-primary">
        <i class="fa-solid fa-store"></i> Start Shopping
    </a>
</div>
<?php endif; ?>

<!-- Confirmation Modal -->
<div id="remove-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Remove Item?</h3>
        <p>Are you sure you want to remove this item from your cart?</p>
        <div class="modal-actions">
            <button id="confirm-remove" class="btn btn-danger">Remove</button>
            <button id="cancel-remove" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>



<?php include('frontend_includes/footer.php'); ?>