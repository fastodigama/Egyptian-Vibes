<?php
// Include configuration file for database connection settings
include('includes/config.php');

// Destroy the session to log out the user
session_destroy();

// Redirect to the admin login page
header('Location: /egyptian-vibes/admin');
// Stop further script execution
die();
?>