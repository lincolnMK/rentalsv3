<?php
session_start();
include('db_connection.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}



// Query for total number of properties
$paidquery1 = "SELECT COUNT(*) AS number_of_properties FROM property;";
$stmt = $conn->prepare($paidquery1);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$number_of_properties = $row['number_of_properties'];
$stmt->close();


//query for total payments due

$total_balance_due = 0;


// tootal approved payments
$total_approved_paid_payments_this_month = 40;


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
     <?php
   include('assets/templates/topbar.php'); 
   ?>

        <!-- Sidebar -->
        <div class="row">
            <nav class="col-md-2 bg-light vh-100">
                <div class="d-flex flex-column align-items-start py-3">
                    
                    <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                    <h3 class="ms-3">Rental System</h3>
                    <ul class="nav flex-column w-100 mt-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
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

                <!-- Dashboard Content -->
                <div class="row p-4">
                    <div class="col-md-4 mb-4">
                        <div class="card border-primary shadow">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Total Properties</h5>
                                <p class="card-text h4"><?=$number_of_properties; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                    <div class="card border-success shadow">
                        <div class="card-body">
                            <h5 class="card-title text-success">Approved & Paid Rentals</h5>
                            <p class="card-text h4"><?= $total_approved_paid_payments_this_month; ?></p>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card border-warning shadow">
                            <div class="card-body">
                                <h5 class="card-title text-warning">Total Payments Due</h5>
                                <p class="card-text h4">MWK <?= $total_balance_due?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                

                <div class="container-fluid">
    

    <!-- Row for Bar and Pie Charts -->
    <div class="row p-4">
        <!-- Bar Chart Column -->
        <div class="col-md-6">
            <canvas id="paymentsChart_bar"></canvas>
        </div>

        <!-- Pie Chart Column -->
        <div class="col-md-6">
            <canvas id="paymentsChart_pie"></canvas>
        </div>
    </div>
</div>

<script>
    // Data for the bar chart
    const barData = {
        labels: <?= json_encode($all_periods); ?>,
        datasets: [{
            label: 'Approved Payments',
            data: <?= json_encode($approved_payments_bar); ?>,
            backgroundColor: '#36a2eb',
            hoverBackgroundColor: '#36a2eb'
        }]
    };

    // Configuration for the bar chart
    const barConfig = {
        type: 'bar',
        data: barData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw || 0;
                            return 'Approved Payments: ' + value;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Period',
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Approved Payments'
                    }
                }
            }
        }
    };

    // Render the bar chart
    const paymentsChart_bar = new Chart(
        document.getElementById('paymentsChart_bar'),
        barConfig
    );

    // Data for the pie chart
    const pieData = {
        labels: ['Approved Payments', 'Other Payments'],
        datasets: [{
            data: [<?= $approved_payments; ?>, <?= $total_payments - $approved_payments; ?>],
            backgroundColor: ['#36a2eb', '#ff6384'],
            hoverBackgroundColor: ['#36a2eb', '#ff6384']
        }]
    };

    // Configuration for the pie chart
    const pieConfig = {
        type: 'pie',
        data: pieData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.chart._metasets[0].total;
                            const percentage = (value / total * 100).toFixed(2) + '%';
                            return label + ': ' + value + ' (' + percentage + ')';
                        }
                    }
                }
            }
        }
    };

    // Render the pie chart
    const paymentsChart_pie = new Chart(
        document.getElementById('paymentsChart_pie'),
        pieConfig
    );
</script>
 
            


                  




            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
   
</body>
<?php
   include('assets/templates/footer.php'); 
   ?>
</html>
