<?php
// Include database and config
include('admin/includes/database.php');
include('frontend_includes/config.php');
include('frontend_includes/functions.php');

// Include header
customer_secure();
include('frontend_includes/header.php');


if (!isset($_SESSION['id'])) {
    header("Location: customer_login.php");
    exit;
}

$user_id = (int)$_SESSION['id'];

// Fetch current user data
$query  = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($connect, $query);
$user   = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<div class='alert alert-error'>User not found.</div>";
    include('frontend_includes/footer.php');
    exit;
}

// Initialize variables with current values
$first       = $user['first'] ?? '';
$last        = $user['last'] ?? '';
$email       = $user['email'] ?? '';
$address     = $user['address'] ?? '';
$postal_code = $user['postal_code'] ?? '';

$errors  = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate First Name
    $first = trim($_POST['first'] ?? '');
    if (empty($first) || !preg_match("/^[a-zA-Z\s]+$/", $first)) {
        $errors[] = "First name is required and must contain only letters.";
    }

    // Validate Last Name
    $last = trim($_POST['last'] ?? '');
    if (empty($last) || !preg_match("/^[a-zA-Z\s]+$/", $last)) {
        $errors[] = "Last name is required and must contain only letters.";
    }

    // Validate Email
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } else {
        // Check if email belongs to another user
        $email_check = mysqli_real_escape_string($connect, $email);
        $check_query = "SELECT id FROM users WHERE email = '$email_check' AND id != $user_id";
        $check_result = mysqli_query($connect, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email already in use by another account.";
        }
    }

    // Validate Address (optional)
    $address = trim($_POST['address'] ?? '');

    // Validate Postal Code
    $postal_code = strtoupper(trim($_POST['postal_code'] ?? ''));
    if (empty($postal_code)) {
        $errors[] = "Postal code is required.";
    } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/', $postal_code)) {
        $errors[] = "Invalid Canadian postal code format (e.g., A1A 1A1).";
    }

    // Password update (optional)
    $password_sql = "";
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        } elseif ($_POST['password'] !== ($_POST['confirm_password'] ?? '')) {
            $errors[] = "Passwords do not match.";
        } else {
            $pass = mysqli_real_escape_string(
                $connect,
                password_hash($_POST['password'], PASSWORD_DEFAULT)
            );
            $password_sql = ", password = '$pass'";
        }
    }

    // If no errors, update the database
    if (empty($errors)) {
        $first       = mysqli_real_escape_string($connect, $first);
        $last        = mysqli_real_escape_string($connect, $last);
        $email       = mysqli_real_escape_string($connect, $email);
        $address     = mysqli_real_escape_string($connect, $address);
        $postal_code = mysqli_real_escape_string($connect, $postal_code);

        $update_query = "
            UPDATE users 
            SET first = '$first',
                last = '$last',
                email = '$email',
                address = '$address',
                postal_code = '$postal_code'
                $password_sql
            WHERE id = $user_id
            LIMIT 1
        ";

        mysqli_query($connect, $update_query);
        $success = "Account updated successfully!";
    }
}
?>

<div class="registration-container">
    <h1 class="form-title">My Account</h1>

    <!-- Success -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- Errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Account Update Form -->
    <form method="POST" action="" id="accountForm" novalidate>
        <div class="form-row">
            <div class="form-field block">
                <label for="first">First Name *</label>
                <input type="text" id="first" name="first"
                       value="<?php echo htmlspecialchars($first); ?>"
                       class="cart-input form-input-full" required>
            </div>
            <div class="form-field block">
                <label for="last">Last Name *</label>
                <input type="text" id="last" name="last"
                       value="<?php echo htmlspecialchars($last); ?>"
                       class="cart-input form-input-full" required>
            </div>
        </div>

        <div class="form-field block">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($email); ?>"
                   class="cart-input form-input-full" required>
        </div>

        <div class="form-field block">
            <label for="address">Address (optional)</label>
            <textarea id="address" name="address" rows="3"
                      class="cart-input form-textarea"><?php echo htmlspecialchars($address); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-field block">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password"
                       class="cart-input form-input-full">
            </div>
            <div class="form-field block">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="cart-input form-input-full">
            </div>
        </div>

        <div class="form-field block">
            <label for="postal_code">Canadian Postal Code *</label>
            <input type="text" id="postal_code" name="postal_code"
                   value="<?php echo htmlspecialchars($postal_code); ?>"
                   class="cart-input postal-code-input"
                   maxlength="7" required>
        </div>

        <!-- With this -->
        <div class="form-buttons">
            <button type="submit" class="btn-checkout">Update Account</button>
            <a href="index.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include('frontend_includes/footer.php'); ?>
