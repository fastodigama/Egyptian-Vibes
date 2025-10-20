<?php
// Include configuration file for database connection settings
include('includes/config.php');

// Include database connection file
include('includes/database.php');

// Include functions file for reusable functions
include('includes/functions.php');

// Call a security function to ensure the page is accessed securely
secure();

// Include header file for common HTML header content
include('includes/header.php');

// Check if the form is submitted via POST method
if (isset($_POST['first'])) {
    // Initialize an array to collect validation errors
    $errors = [];

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
    }

    // Validate Password
    $password = $_POST['password'] ?? '';
    if (!empty($password) && strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // If no validation errors, proceed to update the user in the database
    if (empty($errors)) {
        // Retrieve and convert the user ID from the GET request to an integer
        $id     = (int)$_GET['id'];
        // Sanitize inputs to prevent SQL injection
        $first  = mysqli_real_escape_string($connect, $_POST['first']);
        $last   = mysqli_real_escape_string($connect, $_POST['last']);
        $email  = mysqli_real_escape_string($connect, $_POST['email']);
        $active = mysqli_real_escape_string($connect, $_POST['active']);

        // Prepare the SQL query to update the user information
        $query = "UPDATE users SET
                    first  = '$first',
                    last   = '$last',
                    email  = '$email',
                    active = '$active'
                  WHERE id = $id
                  LIMIT 1";
        // Execute the query
        mysqli_query($connect, $query);

        // If a new password is provided, update the password
        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($connect, md5($_POST['password']));
            $query = "UPDATE users SET password = '$password' WHERE id = $id LIMIT 1";
            mysqli_query($connect, $query);
        }

        // Set a success message
        set_message('User has been updated');
        // Redirect to the users list page
        header('Location: users_list.php');
        // Stop further script execution
        die();
    }
}

// Retrieve and convert the user ID from the GET request to an integer
$id = (int)$_GET['id'];
// Prepare the SQL query to retrieve the user information
$query = "SELECT * FROM users WHERE id = $id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

// Check if the user exists
if (!$record) {
    // Set a message and redirect to the users list page if the user is not found
    set_message("User not found");
    header('Location: users_list.php');
    die();
}
?>

<!-- HTML for the user editing form -->
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit User</h4>
        </div>
        <div class="card-body">
            <!-- Show validation errors if any -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form for editing the user information -->
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first" class="form-label fw-semibold">First Name</label>
                        <input type="text" class="form-control" id="first" name="first"
                               value="<?php echo htmlspecialchars($record['first']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="last" class="form-label fw-semibold">Last Name</label>
                        <input type="text" class="form-control" id="last" name="last"
                               value="<?php echo htmlspecialchars($record['last']); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?php echo htmlspecialchars($record['email']); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep existing)</small></label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <div class="mb-4">
                    <label for="active" class="form-label fw-semibold">Active</label>
                    <select name="active" id="active" class="form-select">
                        <?php
                        // Define the allowed values for the active status
                        $values = ['Yes', 'No'];
                        // Generate options for the active status dropdown
                        foreach ($values as $value) {
                            $selected = ($record['active'] === $value) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($value) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <!-- Cancel button to navigate back to the users list -->
                    <a href="users_list.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <!-- Submit button to update the user information -->
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php include('includes/footer.php'); ?>