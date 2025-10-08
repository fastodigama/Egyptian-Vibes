<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['first'])) {

    //backend validation on inputs for errors

    $errors=[];

    //First name

    $first = trim($_POST['first'] ?? '');
    if (empty($first) || !preg_match("/^[a-zA-Z\s]+$/", $first)) {
    $errors[] = "First name is required and must contain only letters.";
    }

     //last name

    $last = trim($_POST['last'] ?? '');
    if (empty($last) || !preg_match("/^[a-zA-Z\s]+$/", $last)) {
    $errors[] = "Last name is required and must contain only letters.";
    }

    // Email
    $email = $_POST['email'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
    }

    // Password
    if (strlen($_POST['password']) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
    }


    if(empty($errors)){

    
    // Sanitize inputs
    $first   = mysqli_real_escape_string($connect, $_POST['first']);
    $last    = mysqli_real_escape_string($connect, $_POST['last']);
    $email   = mysqli_real_escape_string($connect, $_POST['email']);
    $pass    = mysqli_real_escape_string($connect, md5($_POST['password']));
    $active  = mysqli_real_escape_string($connect, $_POST['active']);

    $query = "INSERT INTO users (first, last, email, password, active) 
              VALUES ('$first', '$last', '$email', '$pass', '$active')";

    mysqli_query($connect, $query);
    set_message('A new user has been added');

    header('Location: users_list.php');
    die();
    }
}
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Add User</h4>
        </div>
        <div class="card-body">
            <!-- show php errors if eny -->
             <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first" class="form-label fw-semibold">First Name</label>
                        <input type="text" class="form-control" id="first" name="first">
                    </div>
                    <div class="col-md-6">
                        <label for="last" class="form-label fw-semibold">Last Name</label>
                        <input type="text" class="form-control" id="last" name="last">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <div class="mb-4">
                    <label for="active" class="form-label fw-semibold">Active</label>
                    <select name="active" id="active" class="form-select">
                        <?php
                        $values = ['Yes', 'No'];
                        foreach ($values as $value) {
                            echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="users_list.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
