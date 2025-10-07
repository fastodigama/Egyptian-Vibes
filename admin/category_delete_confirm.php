<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 

    // Fetch the category name
    $query = "SELECT category_name FROM category WHERE category_id = {$id} LIMIT 1";
    $result = mysqli_query($connect, $query);
    $category = mysqli_fetch_assoc($result);

    // Check if category exists
    if (!$category) {
        set_message("Category not found");
        header('Location: category_list.php');
        die();
    }

    $category_name = $category['category_name'];
} else {
    set_message("Category not found");
    header('Location: category_list.php');
    die();
}
?>

<div class="container mt-5">
    <div class="card shadow-sm border-danger">
        <div class="card-body text-center py-5">
            <h2 class="mb-4 text-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Confirm Deletion
            </h2>
            <p class="fs-5">
                Are you sure you want to delete the category 
                <strong class="text-dark">"<?php echo htmlspecialchars($category_name); ?>"</strong>?
            </p>

            <form method="post" action="" class="mt-4">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                        <i class="bi bi-trash-fill me-1"></i> Yes, Delete
                    </button>
                    <a href="category_list.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['confirm_delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM category WHERE category_id = {$id} LIMIT 1";
    mysqli_query($connect, $query);
    set_message("Category has been deleted");
    header('Location: category_list.php');
    die();
}

include('includes/footer.php');
?>
