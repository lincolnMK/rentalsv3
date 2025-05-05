<?php
session_start();
include('db_connection.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}





// Fetch the username from the session (assuming user details are stored in session)
$username = $_SESSION['username'];  // Adjust this depending on how user data is stored
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/bootstrap/js/Chart.min.js"></script>
  


</head>
<body>
    <div class="container-fluid">
        
     <!-- Top Bar -->
    <div class="row bg-dark text-white py-2">
        <div class="col-md-6">
            <!-- Optional: Place your content here (e.g., logo, title, etc.) -->
        </div>
        <div class="col-md-6 text-end">
            <!-- User Dropdown -->
            <div class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($username); ?></span>
                    <?php
                        // Assuming $profile_picture contains the path to the uploaded image
                        if (!empty($profile_picture)) {
                            // If the user has uploaded a profile picture, display it
                            echo '<img class="img-profile rounded-circle img-fluid" src="' . htmlspecialchars($profile_picture) . '" alt="User Profile" style="width: 30px; height: 30px;">';
                        } else {
                            // If no profile picture, display a default image
                            echo '<img class="img-profile rounded-circle img-fluid" src="assets/images/default-avatar.png" alt="0" style="width: 30px; height: 30px;">';
                        }
                    ?>
                </a>
                <!-- Dropdown - User Information -->
                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Activity Log</a></li>
                    <div class="dropdown-divider"></div>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>






        <!-- Sidebar -->
        <div class="row">
            <nav class="col-md-2 bg-light vh-100">
                <div class="d-flex flex-column align-items-start py-3">
                    
                    <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                    <h3 class="ms-3">Rental System</h3>
                    <ul class="nav flex-column w-100 mt-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="homepage.php">
                                <i class="fas fa-tachometer-alt"></i> Home
                            </a>
                        </li>
                      
                        <li class="nav-item">
                            <a class="nav-link" href="rental_analysis.php" target="_blank">
                                <i class="fas fa-home"></i> >Rental_analysis
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="properties_report.php" target="_blank">
                                <i class="fas fa-home"></i> >Property Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="landlords_report.php" target="_blank">
                                <i class="fas fa-user"></i>  >LandLords Reports
                            </a>
                        </li>

                         <li class="nav-item">
                            <a class="nav-link" href="payments_report.php" target="_blank">
                                <i class="fas fa-user"></i>  >Payments report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="occupants_report.php" target="_blank">
                                <i class="fas fa-chart-line"></i> >Occupants Report
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="tenancy_report.php" target="_blank">
                            <i class="fas fa-chart-line"></i>  >Tenancy Report
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="leases_report.php" target="_blank">
                                <i class="fas fa-chart-line"></i>  >Leases Report
                            </a>
                        </li>

                         
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 bg-light">
                <!-- Topbar (Inside Main Content) -->
                <div class="row bg-white py-3 shadow-sm">
                    <div class="col">
                        <h4 class="text-primary">Reports</h4>
                    </div>
                </div>


            


                  




            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
   
</body>
</html>
