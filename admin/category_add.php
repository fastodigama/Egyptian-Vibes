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

$errors = [];

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Category name ---
    // Retrieve and trim the category name from the POST data
    $name = trim($_POST['category_name'] ?? '');
    // Validate if the category name is empty
    if (empty($name)) {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 100) {
        // Validate if the category name exceeds 100 characters
        $errors[] = "Category name must be less than 100 characters.";
    }

    // --- Check for duplicates ---
    // If no errors, check for duplicate category names
    if (empty($errors)) {
        // Escape the category name to prevent SQL injection
        $safeName = mysqli_real_escape_string($connect, $name);
        // Query the database to check for existing category name
        $check = mysqli_query($connect, "SELECT 1 FROM category WHERE category_name = '$safeName' LIMIT 1");
        // If a duplicate is found, add an error
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This category already exists.";
        }
    }

    // --- If no errors, insert ---
    // If no errors, insert the new category into the database
    if (empty($errors)) {
        // Prepare the SQL query to insert the new category
        $query = "INSERT INTO category (category_name) VALUES ('$safeName')";
        // Execute the query
        mysqli_query($connect, $query);

        // Set a success message
        set_message('A new category has been added successfully.');
        // Redirect to the category list page
        header('Location: category_list.php');
        // Stop further script execution
        die();
    }
}
?>

<!-- HTML for the category addition form -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center"><i class="bi bi-plus-circle"></i> Add Category</h2>
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

                <!-- Form for adding a new category -->
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
                        <!-- Submit button for adding the category -->
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Add Category
                        </button>
                        <!-- Cancel button to navigate back to the category list -->
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