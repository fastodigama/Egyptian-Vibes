<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();
include('includes/header.php');

$product_id = (int)$_GET['product_id'];

// Collect errors
$errors = [];

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photos'])) {
    foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {

        // 1. Check upload error
        if ($_FILES['photos']['error'][$index] !== UPLOAD_ERR_OK) {
            $errors[] = "Upload error for " . htmlspecialchars($_FILES['photos']['name'][$index]);
            continue;
        }

        // 2. Check file size (max 2MB)
        if ($_FILES['photos']['size'][$index] > 2 * 1024 * 1024) {
            $errors[] = htmlspecialchars($_FILES['photos']['name'][$index]) . " exceeds 2MB.";
            continue;
        }

        // 3. Validate MIME type and dimensions using getimagesize()
        $info = @getimagesize($tmpName);
        if ($info === false) {
            $errors[] = htmlspecialchars($_FILES['photos']['name'][$index]) . " is not a valid image.";
            continue;
        }

        $mime = $info['mime'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowed)) {
            $errors[] = htmlspecialchars($_FILES['photos']['name'][$index]) . " is not an allowed image type.";
            continue;
        }

        // Optional: check dimensions (max 5000x5000)
        if ($info[0] > 5000 || $info[1] > 5000) {
            $errors[] = htmlspecialchars($_FILES['photos']['name'][$index]) . " exceeds maximum dimensions (5000x5000).";
            continue;
        }

        // 4. If all checks pass → resize + insert
        $photo = resizeImageToBase64($tmpName, 800, 800);
        if ($photo) {
            $query = "INSERT INTO product_photos (product_id, photo)
                      VALUES ($product_id, '" . mysqli_real_escape_string($connect, $photo) . "')";
            mysqli_query($connect, $query);
        }
    }

    // Redirect only if no errors
    if (empty($errors)) {
        set_message('Photos have been added');
        header('Location: product_list.php');
        die();
    }
}

// Handle photo delete
if (isset($_GET['delete'])) {
    $query = 'DELETE FROM product_photos 
              WHERE photo_id = ' . (int)$_GET['delete'] . ' 
              LIMIT 1';
    mysqli_query($connect, $query);
    header('Location: product_photo.php?product_id=' . $product_id);
    die();
}

// Fetch existing photos
$query = "SELECT * FROM product_photos WHERE product_id = $product_id";
$result = mysqli_query($connect, $query);
?>

<div class="container my-5">

    <div class="card shadow-sm p-4 mb-4">
        <h2 class="mb-4">Add Photos</h2>

        <!-- Show validation errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="photos" class="form-label">Select Photos</label>
                <input type="file" class="form-control" id="photos" name="photos[]" multiple
                       accept="image/png, image/jpeg, image/jpg, image/gif" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="product_list.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm p-4">
        <h2 class="mb-4">Current Photos</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Photo</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($record = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php if ($record['photo']): ?>
                                    <img src="<?= htmlspecialchars($record['photo']); ?>" 
                                         class="img-thumbnail" style="width:100px; height:auto;" alt="Product Photo">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($record['dateAdded']); ?></td>
                            <td>
                                <a href="product_photo.php?product_id=<?= $product_id; ?>&delete=<?= (int)$record['photo_id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this photo?');">
                                   <i class="bi bi-trash me-1"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="3" class="text-muted">No photos uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
