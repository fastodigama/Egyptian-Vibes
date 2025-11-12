<?php
include('admin/includes/database.php');
include('frontend_includes/config.php');
include('frontend_includes/functions.php');

customer_secure(); // redirect to login if not logged in

include('frontend_includes/header.php');

// Always load DB cart
$items = [];
$subtotal = 0;
$shipping = 15.00;
$taxRate = 0.13;

$userId = (int) $_SESSION['id'];

// Load user info
$userQuery = "SELECT first, last, email, address, postal_code 
              FROM users 
              WHERE id = $userId LIMIT 1";
$userRes = mysqli_query($connect, $userQuery);
$userInfo = mysqli_fetch_assoc($userRes);

// Ensure cart exists
$q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
$res = mysqli_query($connect, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $cartId = (int) $row['cart_id'];
} else {
    mysqli_query($connect, "INSERT INTO cart (user_id) VALUES ($userId)");
    $cartId = mysqli_insert_id($connect);
}

// Load items
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

// Redirect if empty
if (empty($items)) {
    header('Location: view_cart.php');
    exit;
}

// Totals
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * $taxRate;
$grandTotal = $subtotal + $shipping + $tax;

// Convert to cents for Stripe (Stripe uses smallest currency unit)
$totalCents = round($grandTotal * 100);
?>

<div class="checkout-wrapper">
    <div class="checkout-container">
        <h1 class="form-title"><i class="fa-solid fa-lock"></i> Secure Checkout</h1>

        <!-- Error/Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="checkout-grid">
            <!-- Billing & Shipping Form -->
            <div class="checkout-form-section">
                <h2 class="checkout-section-title">Billing & Shipping Information</h2>
                
                <form method="POST" action="process_order.php" id="checkoutForm">
                    <!-- Name Fields -->
                    <div class="form-row">
                        <div class="form-field block">
                            <label for="first">First Name *</label>
                            <input type="text" id="first" name="first" 
                                   value="<?php echo htmlspecialchars($userInfo['first'] ?? ''); ?>" 
                                   class="cart-input form-input-full" 
                                   required>
                        </div>
                        
                        <div class="form-field block">
                            <label for="last">Last Name *</label>
                            <input type="text" id="last" name="last" 
                                   value="<?php echo htmlspecialchars($userInfo['last'] ?? ''); ?>" 
                                   class="cart-input form-input-full" 
                                   required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-field block">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($userInfo['email'] ?? ''); ?>" 
                               class="cart-input form-input-full" 
                               required>
                    </div>

                    <!-- Address -->
                    <div class="form-field block">
                        <label for="address">Shipping Address *</label>
                        <textarea id="address" name="address" rows="3"
                                  class="cart-input form-textarea" 
                                  required><?php echo htmlspecialchars($userInfo['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- Postal Code -->
                    <div class="form-field block">
                        <label for="postal_code">Postal Code *</label>
                        <input type="text" id="postal_code" name="postal_code" 
                               value="<?php echo htmlspecialchars($userInfo['postal_code'] ?? ''); ?>" 
                               class="cart-input postal-code-input" 
                               maxlength="7"
                               required>
                    </div>

                    <!-- Stripe Card Element -->
                    <div class="form-field block">
                        <label for="card-element">Credit or Debit Card *</label>
                        <div id="card-element" class="stripe-card-element">
                            <!-- Stripe.js injects the Card Element here -->
                        </div>
                        <div id="card-errors" class="error-message"></div>
                    </div>

                    <!-- Hidden field for Stripe token -->
                    <input type="hidden" name="stripeToken" id="stripeToken">

                    <div class="form-buttons">
                        <button type="submit" class="btn-checkout" id="submit-button">
                            <i class="fa-solid fa-credit-card"></i> Place Order - $<?php echo number_format($grandTotal, 2); ?>
                        </button>
                        <a href="view_cart.php" class="btn-cancel">Back to Cart</a>
                    </div>

                    <div class="secure-checkout-badge">
                        <i class="fa-solid fa-lock"></i> Secure payment powered by Stripe
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="checkout-summary-section">
                <h2 class="checkout-section-title">Order Summary</h2>
                
                <div class="checkout-summary-box">
                    <ul class="checkout-items-list">
                        <?php foreach ($items as $item): ?>
                            <li class="checkout-item">
                                <span class="item-info">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                    <?php if ($item['color']): ?>
                                        <small>(<?php echo htmlspecialchars($item['color']); ?>
                                        <?php if ($item['size']): ?>
                                            - <?php echo htmlspecialchars($item['size']); ?>
                                        <?php endif; ?>)
                                        </small>
                                    <?php endif; ?>
                                    <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                                </span>
                                <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="checkout-totals">
                        <div class="summary-line">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Shipping:</span>
                            <span>$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Tax (13%):</span>
                            <span>$<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <hr>
                        <div class="summary-line summary-total">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($grandTotal, 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe.js Library -->
<script src="https://js.stripe.com/v3/"></script>

<!-- Stripe Integration Script -->
<script>
// Initialize Stripe with your Publishable Key
const stripe = Stripe('pk_test_51SSQj3CNYNh7dtMSE4gkKb4yccijGzHACJCZukUN8VzQpW6c33fUpby7bhJWrBnzHDvKOqgC9R9WP1t4Jo6Puhs400p9TUc0IB'); // Replace with your actual key
const elements = stripe.elements();

// Custom styling for Stripe card element
const style = {
    base: {
        fontSize: '16px',
        color: '#1a1a1a',
        fontFamily: '"Josefin Sans", sans-serif',
        '::placeholder': {
            color: '#999'
        }
    },
    invalid: {
        color: '#dc3545',
        iconColor: '#dc3545'
    }
};

// Create card element
const cardElement = elements.create('card', {style: style});
cardElement.mount('#card-element');

// Handle real-time validation errors
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
        displayError.style.display = 'block';
    } else {
        displayError.textContent = '';
        displayError.style.display = 'none';
    }
});

// Handle form submission
const form = document.getElementById('checkoutForm');
const submitButton = document.getElementById('submit-button');

form.addEventListener('submit', async function(event) {
    event.preventDefault();
    
    // Disable submit button to prevent multiple clicks
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    
    // Create payment method
    const {paymentMethod, error} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: document.getElementById('first').value + ' ' + document.getElementById('last').value,
            email: document.getElementById('email').value,
            address: {
                line1: document.getElementById('address').value,
                postal_code: document.getElementById('postal_code').value
            }
        }
    });
    
    if (error) {
        // Show error to customer
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;
        errorElement.style.display = 'block';
        
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fa-solid fa-credit-card"></i> Place Order - $<?php echo number_format($grandTotal, 2); ?>';
    } else {
        // Send paymentMethod.id to your server
        document.getElementById('stripeToken').value = paymentMethod.id;
        form.submit();
    }
});
</script>

<?php include('frontend_includes/footer.php'); ?>