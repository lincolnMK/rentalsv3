<?php
// auth_check.php
//include_once 'config.php';
//include_once 'db_connection.php';

// Redirect to login if not logged in
//if (!isset($_SESSION['user_id'])) {
//    header("Location: " . BASE_URL . "/login.php");
 //   exit;
//}

// Load user permissions only once per session

if (!defined('ALLOW_PAGE_ACCESS')) {
    header("Location: ../login.php"); // relative to site root
    exit;
}
