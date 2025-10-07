<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Handle update
if (isset($_POST['category_name'])) {
    $id = (int)$_GET['id']; // Sanitize input
    $name = mysqli_real_escape_string($connect, $_POST['category_name']);

    $query = "UPDATE category 
              SET category_name = '$name'
              WHERE category_id = $id
              LIMIT 1";
    mysqli_query($connect, $query);

    set_message('Category has been updated');
    header('Location: category_list.php');
    die();
}

// Fetch category info
$query = "SELECT * FROM category WHERE category_id = " . (int)$_GET['id'] . " LIMIT 1";
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

            <form action="" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="category_name" class="form-label fw-bold">Category Name</label>
                    <input 
                        type="text" 
                        id="category_name" 
                        name="category_name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($record['category_name']); ?>" 
                        required 
                        aria-describedby="categoryHelp">
                    <div class="form-text" id="categoryHelp">Enter the updated name of the category.</div>
                    <div class="invalid-feedback">Please enter a category name.</div>
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
