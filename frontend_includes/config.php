<?php
// Check if a session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Start a new session if no session is active
    session_start();
}
?>