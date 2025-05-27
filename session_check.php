<?php
session_start();

if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /login.php"); // Or use BASE_URL if defined
    exit;
}
?>
