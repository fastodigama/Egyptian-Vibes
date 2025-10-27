<?php
// Include configuration file for database connection settings
include('includes/config.php');

// Include database connection file
include('includes/database.php');

// Include functions file for reusable functions
include('includes/functions.php');

// Include header file for common HTML header content
include('includes/header.php');

// Check if the form is submitted
if(isset($_POST['email'])){
    // Escape the email input to prevent SQL injection
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    // Hash the password using MD5
    $password = md5($_POST['password']);

    // Query the database to check for a matching user
    $query = "SELECT * FROM users
              WHERE email = '$email'
              AND password = '$password'
              AND active = 'yes'
              LIMIT 1";
    $result = mysqli_query($connect, $query);

    // Check if a user is found
if(mysqli_num_rows($result)){
    $record = mysqli_fetch_assoc($result);

    // Check if the user is an admin
    if ($record['role'] === 'admin') {
        $_SESSION['id'] = $record['id'];
        $_SESSION['email'] = $record['email'];
        $_SESSION['role'] = 'admin'; // Set role in session

        header('Location: dashboard.php');
        die();
    } else {
        echo '<div class="alert alert-danger mt-3" role="alert">Access denied. Admins only.</div>';
    }
}
}
?>

<!-- HTML for the admin login form -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center">Admin Login</h2>
                <!-- Form for admin login -->
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                    </div>
                    <div class="d-grid">
                        <!-- Submit button for the login form -->
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer file for common HTML footer content
include('includes/footer.php');
?>