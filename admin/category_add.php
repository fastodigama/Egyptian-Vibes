<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

if(isset($_POST['category_name'])){
    $name = mysqli_real_escape_string($connect, $_POST['category_name']);
    $query = "INSERT INTO category (category_name) VALUES ('$name')";
    mysqli_query($connect, $query);

    set_message('✅ A new category has been added successfully.');
    header('Location: category_list.php');
    die();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4 text-center"><i class="bi bi-plus-circle"></i> Add Category</h2>
                
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
