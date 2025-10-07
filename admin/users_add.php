<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['first'])) {
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
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Add User</h4>
        </div>
        <div class="card-body">
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
