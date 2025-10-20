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

// Retrieve and convert the category ID from the GET request to an integer
$id = (int)$_GET['id'];
$errors = [];

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim the category name from the POST data
    $name = trim($_POST['category_name'] ?? '');

    // --- Validation ---
    // Validate if the category name is empty
    if (empty($name)) {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 100) {
        // Validate if the category name exceeds 100 characters
        $errors[] = "Category name must be less than 100 characters.";
    }

    // Check for duplicates (excluding the current category)
    if (empty($errors)) {
        // Escape the category name to prevent SQL injection
        $safeName = mysqli_real_escape_string($connect, $name);
        // Query the database to check for existing category names, excluding the current category
        $check = mysqli_query(
            $connect,
            "SELECT 1 FROM category
             WHERE category_name = '$safeName'
             AND category_id != $id
             LIMIT 1"
        );
        // If a duplicate is found, add an error
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This category name already exists.";
        }
    }

    // --- If no errors, update ---
    // If no errors, update the category in the database
    if (empty($errors)) {
        // Prepare the SQL query to update the category
        $query = "UPDATE category
                  SET category_name = '$safeName'
                  WHERE category_id = $id
                  LIMIT 1";
        // Execute the query
        mysqli_query($connect, $query);

        // Set a success message
        set_message('Category has been updated');
        // Redirect to the category list page
        header('Location: category_list.php');
        // Stop further script execution
        die();
    }
}

    // Fetch category info
    // Query the database to retrieve the category information
    $query = "SELECT * FROM category WHERE category_id = $id LIMIT 1";
    $result = mysqli_query($connect, $query);
    $record = mysqli_fetch_assoc($result);

    // Check if the category exists
    if (!$record) {
        // Set a message and redirect to the category list page if the category is not found
        set_message("Category not found");
        header('Location: category_list.php');
        die();
    }
?>

<!-- HTML for the category editing form -->
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-4">
                <i class="bi bi-pencil-square me-2"></i> Edit Category
            </h2>
            <?php if (!empty($errors)): ?>
            <!-- Display errors if any -->
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

            <!-- Form for updating the category -->
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
                    <!-- Submit button to update the category -->
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save-fill me-1"></i> Update Category
                    </button>
                    <!-- Cancel button to navigate back to the category list -->
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