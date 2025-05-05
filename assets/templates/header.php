<?php
session_start();
include_once 'config.php';  // This will provide the BASE_URL
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login.php");  // Use BASE_URL for dynamic redirection
    exit;
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Use BASE_URL for dynamic asset linking -->
    <link href="<?php echo BASE_URL; ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Avoid duplicate linking -->
    <link href="<?php echo BASE_URL; ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="main-container container-fluid" style="max-width: 1440px; margin: 0 auto;">
        <header>
            <?php include('assets/templates/topbar.php'); ?>
        </header>
