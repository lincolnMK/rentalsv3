<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'config.php';       // Defines BASE_URL
include_once 'db_connection.php'; // Database connection

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? BASE_URL . '/assets/images/default_avatar.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Styles -->
    <link href="<?php echo BASE_URL; ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="main-container container-fluid" style="max-width: 1440px; margin: 0 auto;">
        <header>
            <?php include('assets/templates/topbar.php'); ?>
        </header>
