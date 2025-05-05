<?php
session_start();
include('db_connection.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Query to count total payments
$total_payments_query = "SELECT COUNT(*) AS total_payments FROM payments";
$result = $conn->query($total_payments_query);
$total_payments_row = $result->fetch_assoc();
$total_payments = $total_payments_row['total_payments'];

// Query to count approved payments
$approved_payments_query = "SELECT COUNT(*) AS approved_payments FROM payments WHERE approval_status = 'approved'";
$result = $conn->query($approved_payments_query);
$approved_payments_row = $result->fetch_assoc();
$approved_payments = $approved_payments_row['approved_payments'];

//quesries for barchart
// Define all the periods for 2025
$all_periods = [
    "Jan 2025", "Feb 2025", "Mar 2025", "Apr 2025", "May 2025", 
    "Jun 2025", "Jul 2025", "Aug 2025", "Sep 2025", "Oct 2025", 
    "Nov 2025", "Dec 2025"
];

// Query to get approved payments for each period (string format)
$approved_payments_period = "
    SELECT period, COUNT(*) AS approved_payments_bar
    FROM payments 
    WHERE approval_status = 'Approved'
    GROUP BY period
    ORDER BY FIELD(period, 'Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 
                    'Jun 2025', 'Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 
                    'Nov 2025', 'Dec 2025')
";

$result_bar = $conn->query($approved_payments_period);

// Initialize arrays for periods and approved payments
$approved_payments_bar = array_fill(0, count($all_periods), 0);

// Map results to the correct periods
while ($row = $result_bar->fetch_assoc()) {
    $index = array_search($row['period'], $all_periods);
    if ($index !== false) {
        $approved_payments_bar[$index] = (int) $row['approved_payments_bar'];
    }
}


//end of quesries for barchart




// Query for total number of approved and paid payments for the current month
$current_month = date('Y-m');
$paidquery = "SELECT COUNT(*) AS total_approved_paid_payments FROM payments WHERE approval_status = 'Approved' AND payment_received_by_landlord = 1 AND DATE_FORMAT(payment_date, '%Y-%m') = ?";
$stmt = $conn->prepare($paidquery);
$stmt->bind_param("s", $current_month);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_approved_paid_payments_this_month = $row['total_approved_paid_payments'];
$stmt->close();

// Query for total number of properties
$paidquery1 = "SELECT COUNT(*) AS number_of_properties FROM properties";
$stmt = $conn->prepare($paidquery1);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$number_of_properties = $row['number_of_properties'];
$stmt->close();


//qury for total payments due
$balancequery2 = "SELECT SUM(balance_due) AS total_balance_due FROM payments";
$stmt = $conn->prepare($balancequery2);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
// Check if there was a valid result
$total_balance_due = $row['total_balance_due'] ? $row['total_balance_due'] : 0;
$stmt->close();



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
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_users.php">
                                <i class="fas fa-users"></i> Users Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_properties.php">
                                <i class="fas fa-home"></i> Property Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_landlords.php">
                                <i class="fas fa-user"></i> LandLords Management
                            </a>
                        </li>

                         <li class="nav-item">
                            <a class="nav-link" href="view_payments.php">
                                <i class="fas fa-user"></i> Payments Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_occupants.php">
                                <i class="fas fa-chart-line"></i> Occupants Management
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="view_tenancy.php">
                                <i class="fas fa-chart-line"></i> Tenancy Management
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="view_leases.php">
                                <i class="fas fa-chart-line"></i> Leases Management
                            </a>
                        </li>

                         <li class="nav-item">
                            <a class="nav-link" href="view_reports.php">
                                <i class="fas fa-chart-line"></i> Reports and Analytics
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
                        <h4 class="text-primary">Dashboard</h4>
                    </div>
                </div>


            


                  




            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
   
</body>
</html>
