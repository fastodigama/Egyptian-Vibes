<?php
// Include the database connection file
include('admin/includes/database.php');

// Include the configuration file
include('frontend_includes/config.php');
include('frontend_includes/functions.php');





// Include the header file
include('frontend_includes/header.php');

// Initialize variables
$errors = [];
$success = '';

// Check if the form is submitted via POST method
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
    $email = $_POST['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } else {
        // Check if email already exists
        $email_check = mysqli_real_escape_string($connect, $email);
        $check_query = "SELECT id FROM users WHERE email = '$email_check'";
        $check_result = mysqli_query($connect, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email already registered. Please use a different email.";
        }
    }

    // Validate Password
    if (strlen($_POST['password'] ?? '') < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Validate Confirm Password
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }

    // Validate Address (optional)
    $address = trim($_POST['address'] ?? '');

    // Validate Canadian Postal Code
    $postal_code = strtoupper(trim($_POST['postal_code'] ?? ''));
    if (empty($postal_code)) {
        $errors[] = "Postal code is required.";
    } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/', $postal_code)) {
        $errors[] = "Invalid Canadian postal code format (e.g., A1A 1A1).";
    }

    // If no validation errors, proceed to insert the user into the database
    if (empty($errors)) {
        // Sanitize inputs to prevent SQL injection
        $first       = mysqli_real_escape_string($connect, $_POST['first']);
        $last        = mysqli_real_escape_string($connect, $_POST['last']);
        $email       = mysqli_real_escape_string($connect, $_POST['email']);
        $pass        = mysqli_real_escape_string($connect, password_hash($_POST['password'], PASSWORD_DEFAULT));
        $address     = mysqli_real_escape_string($connect, $address);
        $postal_code = mysqli_real_escape_string($connect, $postal_code);
        $role        = 'customer'; // Default role for registration
        $active      = 'Yes'; // Default active status

        // Prepare the SQL query to insert the user into the database
        $query = "INSERT INTO users (first, last, email, password, role, address, postal_code, active, dateAdded)
                  VALUES ('$first', '$last', '$email', '$pass', '$role', '$address', '$postal_code', '$active', NOW())";
        
        // Execute the query
        mysqli_query($connect, $query);
        
        // Set success message
        $success = "Registration successful! You can now <a href='customer_login.php' class='success-link'>login</a>.";
        
        // Clear form data
        $first = $last = $email = $address = $postal_code = '';
    }
}
?>

    <div class="registration-container">
        <h1 class="form-title">Create Account</h1>
        
        <!-- Display success message -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Show validation errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Registration Form -->
        <form method="POST" action="" id="registrationForm" novalidate>
            
            <!-- Name Fields -->
            <div class="form-row">
                <div class="form-field block">
                    <label for="first">First Name *</label>
                    <input type="text" id="first" name="first" 
                           value="<?php echo htmlspecialchars($first ?? ''); ?>" 
                           class="cart-input form-input-full" 
                           required
                           aria-required="true"
                           aria-describedby="first-error">
                    <span id="first-error" class="error-message"></span>
                </div>
                
                <div class="form-field block">
                    <label for="last">Last Name *</label>
                    <input type="text" id="last" name="last" 
                           value="<?php echo htmlspecialchars($last ?? ''); ?>" 
                           class="cart-input form-input-full" 
                           required
                           aria-required="true"
                           aria-describedby="last-error">
                    <span id="last-error" class="error-message"></span>
                </div>
            </div>
            
            <!-- Email -->
            <div class="form-field block">
                <label for="email">Email Address (Username) *</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                       class="cart-input form-input-full" 
                       required
                       aria-required="true"
                       aria-describedby="email-error">
                <span id="email-error" class="error-message"></span>
            </div>
            
            <!-- Address -->
            <div class="form-field block">
                <label for="address">Address (optional)</label>
                <textarea id="address" name="address" rows="3"
                          class="cart-input form-textarea" 
                          aria-describedby="address-error"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                <span id="address-error" class="error-message"></span>
            </div>
            
            <!-- Password Fields -->
            <div class="form-row">
                <div class="form-field block">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" 
                           class="cart-input form-input-full" 
                           required
                           aria-required="true"
                           aria-describedby="password-error">
                    <span id="password-error" class="error-message"></span>
                </div>
                
                <div class="form-field block">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="cart-input form-input-full" 
                           required
                           aria-required="true"
                           aria-describedby="confirm-password-error">
                    <span id="confirm-password-error" class="error-message"></span>
                </div>
            </div>
            
            <!-- Postal Code -->
            <div class="form-field block">
                <label for="postal_code">Canadian Postal Code * (e.g., A1A 1A1)</label>
                <input type="text" id="postal_code" name="postal_code" 
                       value="<?php echo htmlspecialchars($postal_code ?? ''); ?>" 
                       class="cart-input postal-code-input" 
                       placeholder="A1A 1A1"
                       maxlength="7"
                       required
                       aria-required="true"
                       aria-describedby="postal-code-error">
                <span id="postal-code-error" class="error-message"></span>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn-checkout">
                Create Account
            </button>
            
            <!-- Login Link -->
            <p class="form-footer-text">
                Already have an account? <a href="customer_login.php" class="form-link">Login here</a>
            </p>
            
        </form>



<?php include('frontend_includes/footer.php'); ?>