<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Egyptian Vibes</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="css/style.css"> -->
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <header class="navbar navbar-expand-lg navbar-dark bg-dark" role="banner">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="dashboard.php">Egyptian Vibes Admin</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" 
              aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <nav id="mainNav" class="collapse navbar-collapse" role="navigation">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Logout</a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container py-4" role="main">
    <?php get_message(); ?>
    <div>
      <!-- Page content goes here -->
    </div>
  </main>

  <!-- Bootstrap JS (for responsive navbar) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
