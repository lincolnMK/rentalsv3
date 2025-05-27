<?php
session_start();

if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
   header("Location: " . BASE_URL . "/login.php"); 
    exit;
}
?>
