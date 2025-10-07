<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['first'])) {
    // Sanitize inputs
    $id     = (int)$_GET['id'];
    $first  = mysqli_real_escape_string($connect, $_POST['first']);
    $last   = mysqli_real_escape_string($connect, $_POST['last']);
    $email  = mysqli_real_escape_string($connect, $_POST['email']);
    $active = mysqli_real_escape_string($connect, $_POST['active']);

    $query = "UPDATE users SET
                first  = '$first',
                last   = '$last',
                email  = '$email',
                active = '$active'
              WHERE id = $id
              LIMIT 1";
    mysqli_query($connect, $query);

    // If new password provided
    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($connect, md5($_POST['password']));
        $query = "UPDATE users SET password = '$password' WHERE id = $id LIMIT 1";
        mysqli_query($connect, $query);
    }

    set_message('User has been updated');
    header('Location: users_list.php');
    die();
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM users WHERE id = $id LIMIT 1";
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

if (!$record) {
    set_message("User not found");
    header('Location: users_list.php');
    die();
}
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit User</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first" class="form-label fw-semibold">First Name</label>
                        <input type="text" class="form-control" id="first" name="first" 
                               value="<?php echo htmlspecialchars($record['first']); ?>" required>
                        <div class="invalid-feedback">Please enter the first name.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="last" class="form-label fw-semibold">Last Name</label>
                        <input type="text" class="form-control" id="last" name="last" 
                               value="<?php echo htmlspecialchars($record['last']); ?>" required>
                        <div class="invalid-feedback">Please enter the last name.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($record['email']); ?>" required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep existing)</small></label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <div class="mb-4">
                    <label for="active" class="form-label fw-semibold">Active</label>
                    <select name="active" id="active" class="form-select" required>
                        <?php
                        $values = ['Yes', 'No'];
                        foreach ($values as $value) {
                            $selected = ($record['active'] === $value) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($value) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="users_list.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
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
