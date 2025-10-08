<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Category name ---
    $name = trim($_POST['category_name'] ?? '');
    if (empty($name)) {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Category name must be less than 100 characters.";
    }

    // --- Check for duplicates ---
    if (empty($errors)) {
        $safeName = mysqli_real_escape_string($connect, $name);
        $check = mysqli_query($connect, "SELECT 1 FROM category WHERE category_name = '$safeName' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This category already exists.";
        }
    }

    // --- If no errors, insert ---
    if (empty($errors)) {
        $query = "INSERT INTO category (category_name) VALUES ('$safeName')";
        mysqli_query($connect, $query);

        set_message('A new category has been added successfully.');
        header('Location: category_list.php');
        die();
    }
}
?>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center"><i class="bi bi-plus-circle"></i> Add Category</h2>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <div class="input-group">
                            <span class="input-group-text" id="categoryIcon">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input 
                                type="text" 
                                id="category_name" 
                                name="category_name" 
                                class="form-control" 
                                placeholder="Enter category name" 
                                required 
                                aria-describedby="categoryIcon"
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Add Category
                        </button>
                        <a href="category_list.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
