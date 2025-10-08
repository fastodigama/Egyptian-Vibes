<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

$id = (int)$_GET['id'];
$errors = [];

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name'] ?? '');

    // --- Validation ---
    if (empty($name)) {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Category name must be less than 100 characters.";
    }

    // Check for duplicates (excluding current category)
    if (empty($errors)) {
        $safeName = mysqli_real_escape_string($connect, $name);
        $check = mysqli_query(
            $connect,
            "SELECT 1 FROM category 
             WHERE category_name = '$safeName' 
             AND category_id != $id 
             LIMIT 1"
        );
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This category name already exists.";
        }
    }

    // --- If no errors, update ---
    if (empty($errors)) {
        $query = "UPDATE category 
                  SET category_name = '$safeName'
                  WHERE category_id = $id
                  LIMIT 1";
        mysqli_query($connect, $query);

        set_message('Category has been updated');
        header('Location: category_list.php');
        die();
        }
    }

        // Fetch category info
        $query = "SELECT * FROM category WHERE category_id = $id LIMIT 1";
        $result = mysqli_query($connect, $query);
        $record = mysqli_fetch_assoc($result);

        if (!$record) {
            set_message("Category not found");
            header('Location: category_list.php');
            die();
    }
?>


<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-4">
                <i class="bi bi-pencil-square me-2"></i> Edit Category
            </h2>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


            <form action="" method="POST"  >
                <div class="mb-3">
                    <label for="category_name" class="form-label fw-bold">Category Name</label>
                    <input 
                        type="text" 
                        id="category_name" 
                        name="category_name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($record['category_name']); ?>" 
                         
                        aria-describedby="categoryHelp">
                    <div class="form-text" id="categoryHelp">Enter the updated name of the category.</div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save-fill me-1"></i> Update Category
                    </button>
                    <a href="category_list.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
    // Enable Bootstrap form validation feedback
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
