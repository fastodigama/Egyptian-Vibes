<?php
// Include configuration file for database connection settings
include('includes/config.php');

// Include database connection file
include('includes/database.php');

// Include functions file for reusable functions
include('includes/functions.php');

// Include header file for common HTML header content
include('includes/header.php');

// Call a security function to ensure the page is accessed securely
secure();
?>

<!-- Main content for the admin dashboard -->
<main class="container py-5" role="main">
  <!-- Display any messages set in the session -->
  <?php get_message(); ?>

  <!-- Welcome message for the admin dashboard -->
  <div class="text-center mb-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    <p class="text-muted">Welcome to the Egyptian Vibes Admin Panel. Use the options below to manage your content.</p>
  </div>

  <!-- Grid layout for admin dashboard options -->
  <div class="row justify-content-center g-3">
    <!-- Option to manage products -->
    <div class="col-md-3 col-sm-6">
      <a href="product_list.php" class="btn btn-outline-primary w-100 py-3" aria-label="Manage Products">
        <i class="bi bi-box-seam me-2"></i> Manage Products
      </a>
    </div>
    <!-- Option to manage categories -->
    <div class="col-md-3 col-sm-6">
      <a href="category_list.php" class="btn btn-outline-success w-100 py-3" aria-label="Manage Categories">
        <i class="bi bi-tags me-2"></i> Manage Categories
      </a>
    </div>
    <!-- Option to manage users -->
    <div class="col-md-3 col-sm-6">
      <a href="users_list.php" class="btn btn-outline-dark w-100 py-3" aria-label="Manage Users">
        <i class="bi bi-people me-2"></i> Manage Users
      </a>
    </div>

    <!-- Option to manage colors -->
    <div class="col-md-3 col-sm-6">
      <a href="color_add.php" class="btn btn-outline-dark w-100 py-3" aria-label="Manage Users">
        <i class="bi bi-people me-2"></i> Manage Product Colours
      </a>
    </div>
  </div>
</main>

<?php include('includes/footer.php'); ?>