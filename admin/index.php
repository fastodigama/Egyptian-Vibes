<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
include('includes/header.php');

if(isset($_POST['email'])){
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users 
              WHERE email = '$email' 
              AND password = '$password' 
              AND active = 'yes' 
              LIMIT 1";
    $result = mysqli_query($connect, $query);

    if(mysqli_num_rows($result)){
        $record = mysqli_fetch_assoc($result);
        $_SESSION['id'] = $record['id'];
        $_SESSION['email'] = $record['email'];
        header('Location: dashboard.php');
        die();
    } else {
        echo '<div class="alert alert-danger mt-3" role="alert">Invalid credentials or inactive account.</div>';
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center">Admin Login</h2>
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
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
?>
