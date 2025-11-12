<?php
include('admin/includes/database.php');
include('frontend_includes/config.php');
include('frontend_includes/functions.php');

customer_secure();

include('frontend_includes/header.php');

// Get order ID from URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$userId = (int)$_SESSION['id'];

if (!$orderId) {
    header('Location: index.php');
    exit;
}

// Fetch order details
$orderQuery = "SELECT * FROM orders WHERE order_id = $orderId AND user_id = $userId LIMIT 1";
$orderResult = mysqli_query($connect, $orderQuery);
$order = mysqli_fetch_assoc($orderResult);

if (!$order) {
    echo "<div class='alert alert-error'>Order not found.</div>";
    include('frontend_includes/footer.php');
    exit;
}

// Fetch order items
$itemsQuery = "SELECT oi.*, pv.variant_id,
                      (SELECT photo FROM product_photos pp 
                       WHERE pp.product_id = pv.product_id 
                       LIMIT 1) AS photo,
                      p.product_title
               FROM order_items oi
               JOIN product_variants pv ON oi.variant_id = pv.variant_id
               JOIN product p ON pv.product_id = p.product_id
               WHERE oi.order_id = $orderId";

$itemsResult = mysqli_query($connect, $itemsQuery);
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $items[] = $row;
}
?>

<div class="registration-container" style="max-width: 700px;">
    <!-- Success Message -->
    <div class="order-confirmation-header">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1 class="form-title">Order Confirmed!</h1>
        <p class="confirmation-message">
            Thank you for your purchase! Your order has been successfully placed.
        </p>
    </div>

    <!-- Order Details Card -->
    <div class="order-details-card">
        <div class="order-header">
            <h2>Order #<?php echo $orderId; ?></h2>
            <span class="order-status status-<?php echo $order['status']; ?>">
                <?php echo ucfirst($order['status']); ?>
            </span>
        </div>

        <div class="order-info-grid">
            <div class="info-item">
                <span class="info-label">Order Date:</span>
                <span class="info-value"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Payment Status:</span>
                <span class="info-value payment-<?php echo $order['payment_status']; ?>">
                    <?php echo ucfirst($order['payment_status']); ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Shipping Address:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['shipping_address']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Postal Code:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['shipping_postal_code']); ?></span>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="order-items-section">
        <h2 class="section-title">Order Items</h2>
        
        <?php foreach ($items as $item): ?>
            <div class="order-item-row">
                <?php if ($item['photo']): ?>
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_title'] ?? 'Product'); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="item-details">
                    <h3><?php echo htmlspecialchars($item['product_title'] ?? 'Product'); ?></h3>
                    <?php if ($item['color_name'] || $item['size_name']): ?>
                        <p class="item-variant">
                            <?php if ($item['color_name']): ?>
                                Color: <?php echo htmlspecialchars($item['color_name']); ?>
                            <?php endif; ?>
                            <?php if ($item['size_name']): ?>
                                <?php echo $item['color_name'] ? ' | ' : ''; ?>
                                Size: <?php echo htmlspecialchars($item['size_name']); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($item['sku']): ?>
                        <p class="item-sku">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="item-quantity">
                    <span>Qty: <?php echo $item['quantity']; ?></span>
                </div>
                
                <div class="item-price">
                    <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Order Summary -->
    <div class="order-summary-final">
        <div class="summary-line">
            <span>Subtotal:</span>
            <span>$<?php echo number_format($order['total'] - $order['shipping_cost'] - $order['tax_amount'], 2); ?></span>
        </div>
        <div class="summary-line">
            <span>Shipping:</span>
            <span>$<?php echo number_format($order['shipping_cost'], 2); ?></span>
        </div>
        <div class="summary-line">
            <span>Tax:</span>
            <span>$<?php echo number_format($order['tax_amount'], 2); ?></span>
        </div>
        <hr>
        <div class="summary-line summary-total">
            <strong>Total:</strong>
            <strong>$<?php echo number_format($order['total'], 2); ?></strong>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-buttons">
        <a href="index.php" class="btn-checkout">Continue Shopping</a>
        <a href="account.php" class="btn-cancel">View My Account</a>
    </div>

    <!-- Additional Info -->
    <div class="order-next-steps">
        <h3>What's Next?</h3>
        <ul>
            <li><i class="fa-solid fa-envelope"></i> You'll receive an email confirmation shortly</li>
            <li><i class="fa-solid fa-truck"></i> We'll notify you when your order ships</li>
            <li><i class="fa-solid fa-user"></i> Track your order anytime in your account</li>
        </ul>
    </div>
</div>

<?php include('frontend_includes/footer.php'); ?>