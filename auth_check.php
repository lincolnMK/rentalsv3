<?php
// check_auth.php


include_once 'config.php';  // Ensure the config file is included to access constants like BASE_URL

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;  // Ensure the script stops executing after redirection
}
