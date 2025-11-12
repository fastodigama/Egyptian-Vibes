<?php
// Include database and config
include('admin/includes/database.php');
include('frontend_includes/config.php');
include('frontend_includes/functions.php');

// Include header for CSS and layout
include('frontend_includes/header.php');

// Initialize variables
$errors = [];
$success = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If no validation errors, check credentials
    if (empty($errors)) {
        $email_safe = mysqli_real_escape_string($connect, $email);
        $query = "SELECT * FROM users
                  WHERE email = '$email_safe'
                  AND role = 'customer'
                  AND active = 'Yes'
                  LIMIT 1";
        $result = mysqli_query($connect, $query);

        if (mysqli_num_rows($result)) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // --- MIGRATE GUEST CART TO DB ---
                if (!empty($_SESSION['cart'])) {
                    $userId = (int) $_SESSION['id'];

                    // Ensure user has a cart
                    $q = "SELECT cart_id FROM cart WHERE user_id = $userId LIMIT 1";
                    $res = mysqli_query($connect, $q);
                    if ($row = mysqli_fetch_assoc($res)) {
                        $cartId = (int) $row['cart_id'];
                    } else {
                        mysqli_query($connect, "INSERT INTO cart (user_id) VALUES ($userId)");
                        $cartId = mysqli_insert_id($connect);
                    }

                    // Move session items into DB
                    foreach ($_SESSION['cart'] as $variantId => $item) {
                        $qty   = (int) $item['quantity'];
                        $price = (float) $item['price'];
                        mysqli_query($connect, "INSERT INTO cart_items (cart_id, variant_id, quantity, price_at_add_time)
                                                VALUES ($cartId, $variantId, $qty, $price)");
                    }

                    // Clear session cart
                    unset($_SESSION['cart']);
                }

                // Redirect to original page or default
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No active customer found with that email.";
        }
    }
}
?>

<!-- Login Form UI -->
<div class="registration-container">
    <h1 class="form-title">Customer Login</h1>

    <!-- Success Message -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="" id="loginForm" novalidate>
        <div class="form-field block">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                   class="cart-input form-input-full"
                   required>
        </div>

        <div class="form-field block">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password"
                   class="cart-input form-input-full"
                   required>
        </div>

        <button type="submit" class="btn-checkout">Login</button>

        <p class="form-footer-text">
            Don't have an account? <a href="register.php" class="form-link">Register here</a>
        </p>
    </form>
</div>

<?php include('frontend_includes/footer.php'); ?>
