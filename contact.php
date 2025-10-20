<?php


include('admin/includes/database.php');
include('frontend_includes/header.php');

include('frontend_includes/config.php');

?>

<section class="form-section">
          <h2>Comments</h2>
          <form method="post">
            <div class="form-field inline">

              <label>Your name:</label>
              <input type="text" id="name" name="name" placeholder="e.g. John Doe">
            </div>
            <div class="form-field block">
              <label for="comment">Share your thoughts about this store:</label>
              <textarea id="comment" name="comment" placeholder="Write your comment here."></textarea>
            </div>
            <button type="submit">Post comment</button>
          </form>
        </section>

<?php include('frontend_includes/footer.php'); ?>