<?php
include('frontend_includes/config.php');
include('admin/includes/database.php'); // DB connection

// ---------------------------
// Handle AJAX requests
// ---------------------------
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    // REMOVE
    if (isset($_GET['remove'])) {
        $removeId = (int) $_GET['remove'];

        if (!isset($_SESSION['id'])) {
            unset($_SESSION['cart'][$removeId]);
        } else {
            $userId = (int) $_SESSION['id'];
            $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
            $res = mysqli_query($connect, $q);
            if ($row = mysqli_fetch_assoc($res)) {
                $cartId = (int) $row['cart_id'];
                mysqli_query($connect, "DELETE FROM cart_items WHERE cart_id = $cartId AND cart_item_id = $removeId");
            }
        }

        echo json_encode(['success' => true, 'message' => 'Item removed']);
        exit;
    }

    // UPDATE QTY
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
        $updateId = (int) $_POST['product_id']; 
        $newQty   = max(0, (int) $_POST['new_quantity']);

        if (!isset($_SESSION['id'])) {
            if (isset($_SESSION['cart'][$updateId])) {
                if ($newQty > 0) {
                    $_SESSION['cart'][$updateId]['quantity'] = $newQty;
                    echo json_encode(['success' => true, 'message' => 'Quantity updated']);
                } else {
                    unset($_SESSION['cart'][$updateId]);
                    echo json_encode(['success' => true, 'message' => 'Item removed']);
                }
            }
        } else {
            $userId = (int) $_SESSION['id'];
            $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
            $res = mysqli_query($connect, $q);
            if ($row = mysqli_fetch_assoc($res)) {
                $cartId = (int) $row['cart_id'];
                if ($newQty > 0) {
                    mysqli_query($connect, "UPDATE cart_items SET quantity = $newQty WHERE cart_id = $cartId AND cart_item_id = $updateId");
                    echo json_encode(['success' => true, 'message' => 'Quantity updated']);
                } else {
                    mysqli_query($connect, "DELETE FROM cart_items WHERE cart_id = $cartId AND cart_item_id = $updateId");
                    echo json_encode(['success' => true, 'message' => 'Item removed']);
                }
            }
        }
        exit;
    }
}

// ---------------------------
// Handle non-AJAX requests (fallback)
// ---------------------------
if (isset($_GET['remove'])) {
    $removeId = (int) $_GET['remove'];

    if (!isset($_SESSION['id'])) {
        unset($_SESSION['cart'][$removeId]);
    } else {
        $userId = (int) $_SESSION['id'];
        $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
        $res = mysqli_query($connect, $q);
        if ($row = mysqli_fetch_assoc($res)) {
            $cartId = (int) $row['cart_id'];
            mysqli_query($connect, "DELETE FROM cart_items WHERE cart_id = $cartId AND cart_item_id = $removeId");
        }
    }

    header('Location: view_cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $updateId = (int) $_POST['product_id'];
    $newQty   = max(0, (int) $_POST['new_quantity']);

    if (!isset($_SESSION['id'])) {
        if (isset($_SESSION['cart'][$updateId])) {
            if ($newQty > 0) {
                $_SESSION['cart'][$updateId]['quantity'] = $newQty;
            } else {
                unset($_SESSION['cart'][$updateId]);
            }
        }
    } else {
        $userId = (int) $_SESSION['id'];
        $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
        $res = mysqli_query($connect, $q);
        if ($row = mysqli_fetch_assoc($res)) {
            $cartId = (int) $row['cart_id'];
            if ($newQty > 0) {
                mysqli_query($connect, "UPDATE cart_items SET quantity = $newQty WHERE cart_id = $cartId AND cart_item_id = $updateId");
            } else {
                mysqli_query($connect, "DELETE FROM cart_items WHERE cart_id = $cartId AND cart_item_id = $updateId");
            }
        }
    }

    header('Location: view_cart.php');
    exit;
}

include('frontend_includes/header.php');

// ---------------------------
// Load cart items (session vs DB)
// ---------------------------
$items = [];
$subtotal = 0;
$shipping = 15.00;
$taxRate = 0.13;

if (!isset($_SESSION['id'])) {
    if (!empty($_SESSION['cart'])) {
        $items = $_SESSION['cart'];
    }
} else {
    $userId = (int) $_SESSION['id'];
    $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
    $res = mysqli_query($connect, $q);
    if ($row = mysqli_fetch_assoc($res)) {
        $cartId = (int) $row['cart_id'];
        $qItems = "SELECT ci.cart_item_id, ci.quantity, ci.price_at_add_time AS price,
                          p.product_title AS title, pv.sku, pc.color_name AS color, ps.size_name AS size,
                          (SELECT photo FROM product_photos WHERE product_id = p.product_id ORDER BY photo_id LIMIT 1) AS photo
                   FROM cart_items ci
                   JOIN product_variants pv ON ci.variant_id = pv.variant_id
                   JOIN product p ON pv.product_id = p.product_id
                   LEFT JOIN product_color pc ON pv.color_id = pc.color_id
                   LEFT JOIN product_size ps ON pv.size_id = ps.size_id
                   WHERE ci.cart_id = $cartId";
        $resItems = mysqli_query($connect, $qItems);
        while ($rowItem = mysqli_fetch_assoc($resItems)) {
            $items[$rowItem['cart_item_id']] = $rowItem;
        }
    }
}
?>

<div class="cart-header">
    <h1><i class="fa-solid fa-bag-shopping"></i> Your Cart</h1>
    <?php if (!empty($items)): ?>
        <span class="cart-count"><?php echo count($items); ?> item(s)</span>
    <?php endif; ?>
</div>

<?php if (!empty($items)): ?>
<div class="cart-grid">
    <div class="cart-items-column">
        <div class="column-header">
            <h2>Cart Items</h2>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        
        <?php foreach ($items as $id => $item): 
            $total = $item['price'] * $item['quantity'];
            $subtotal += $total;
        ?>
        <div class="cart-item" data-item-id="<?php echo $id; ?>">
            <div class="item-thumbnail">
                <img src="<?php echo htmlspecialchars($item['photo'] ?? ''); ?>"
                     alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>">
            </div>
            <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($item['title'] ?? ''); ?></h3>
                
                <?php if (!empty($item['sku'])): ?>
                    <p class="item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                <?php endif; ?>
                <?php if (!empty($item['color'])): ?>
                    <p class="item-color">Color: <?php echo htmlspecialchars($item['color']); ?></p>
                <?php endif; ?>
                <?php if (!empty($item['size'])): ?>
                    <p class="item-size">Size: <?php echo htmlspecialchars($item['size']); ?></p>
                <?php endif; ?>
                
                <p class="item-price">Unit Price: $<?php echo number_format($item['price'], 2); ?></p>
                <p class="item-total">Subtotal: <strong>$<?php echo number_format($total, 2); ?></strong></p>
                
                <div class="item-actions">
                    <form method="post" action="view_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                        <div class="quantity-control">
                            <label for="qty-<?php echo $id; ?>">Qty:</label>
                            <input type="number"
                                   id="qty-<?php echo $id; ?>"
                                   name="new_quantity"
                                   value="<?php echo $item['quantity']; ?>"
                                   min="1"
                                   max="99">
                                                        <button type="submit" name="update_qty" class="btn btn-update">
                                <i class="fa-solid fa-rotate"></i> Update
                            </button>
                        </div>
                    </form>

                    <form method="get" action="view_cart.php" style="display:inline;">
                    <input type="hidden" name="remove" value="<?php echo $id; ?>">
                    <button type="button" class="btn btn-remove" data-id="<?php echo $id; ?>">
                        <i class="fa-solid fa-trash"></i> Remove
                    </button>
                </form>

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
            
            <a href="checkout.php" class="btn btn-checkout" onclick="console.log('Checkout clicked'); return true;">

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
