<?php

include ('admin\includes\database.php');

function customer_secure() {
    // Check if the user is NOT logged in OR is not a customer
    if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'customer') {

        // Save the current page URL so we can return here after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

        // Redirect the user to the customer login page
        header('Location: customer_login.php');

        // Stop further execution of the page
        die();
    }
}
