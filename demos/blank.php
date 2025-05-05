<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username from the session (adjust this depending on how user data is stored)
$username = $_SESSION['username'];  // Adjust as necessary
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blank Page</title>

    <!-- Attempt to load Bootstrap from CDN -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    
   

   
</head>
<body>

<div class="container-fluid">
        <!-- Top Bar -->
        <div class="row bg-dark text-white py-2">
            <div class="col-md-6">
                <h4 class="ms-4">Welcome, <?= htmlspecialchars($username); ?></h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="logout.php" class="btn btn-light me-4">Logout</a>
            </div>
        </div>

        <!-- Sidebar and Main Content Wrapper -->
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
                <div class="d-flex flex-column align-items-start py-3">
                    <h3 class="ms-3">Rental System</h3>
                    <ul class="nav flex-column w-100 mt-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="homepage.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_houses.php">
                                <i class="fas fa-home"></i> Manage Houses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_tenants.php">
                                <i class="fas fa-user"></i> Manage Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-line"></i> Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-10 bg-light">
                <!-- Button to toggle sidebar visibility -->
                <button id="sidebarToggle" class="btn btn-dark d-md-none">☰</button>

                <!-- Topbar (Inside Main Content) -->
                <div class="row bg-white py-3 shadow-sm">
                    <div class="col">
                        <h4 class="text-primary">Page Title</h4>
                    </div>
                </div>

                <!-- Content Goes Here -->
                <div class="row p-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Content Title</h5>
                                <p class="card-text">This is where the content for your page will go.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
  
</body>
</html>
