<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

// Fetch all colors
$query = "SELECT * FROM product_color ORDER BY color_name ASC";
$result = mysqli_query($connect, $query);

//delete color

if(isset($_GET['delete'])){


$deleteColorId = $_GET['delete'];

$deleteQuery = "DELETE FROM product_color
WHERE color_id = $deleteColorId";
mysqli_query($connect,$deleteQuery);
set_message("Color deleted");
header('Location: color_list.php');
die;
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Color List</h2>
        <a href="color_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Color
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Color Name</th>
                    <th scope="col">Hex Code</th>
                    <th scope="col" class="text-center">Preview</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($color = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($color['color_name']); ?></td>
                        <td><?php echo htmlspecialchars($color['hex_code']); ?></td>
                        <td class="text-center">
                            <span class="badge" style="background-color: <?php echo htmlspecialchars($color['hex_code']); ?>;">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="color_edit.php?id=<?php echo (int)$color['color_id']; ?>"
                               class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="color_list.php?delete=<?php echo (int)$color['color_id']; ?>"
                               class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this color?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php'); ?>
