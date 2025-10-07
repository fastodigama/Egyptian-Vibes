<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
include('includes/header.php');
secure();
?>

<main class="container py-5" role="main">
  <?php get_message(); ?>

  <div class="text-center mb-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    <p class="text-muted">Welcome to the Egyptian Vibes Admin Panel. Use the options below to manage your content.</p>
  </div>

  <div class="row justify-content-center g-3">
    <div class="col-md-3 col-sm-6">
      <a href="product_list.php" class="btn btn-outline-primary w-100 py-3" aria-label="Manage Products">
        <i class="bi bi-box-seam me-2"></i> Manage Products
      </a>
    </div>
    <div class="col-md-3 col-sm-6">
      <a href="category_list.php" class="btn btn-outline-success w-100 py-3" aria-label="Manage Categories">
        <i class="bi bi-tags me-2"></i> Manage Categories
      </a>
    </div>
    <div class="col-md-3 col-sm-6">
      <a href="users_list.php" class="btn btn-outline-dark w-100 py-3" aria-label="Manage Users">
        <i class="bi bi-people me-2"></i> Manage Users
      </a>
    </div>
  </div>
</main>

<?php include('includes/footer.php'); ?>
