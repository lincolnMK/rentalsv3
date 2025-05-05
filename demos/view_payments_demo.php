<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username from the session (adjust this depending on how user data is stored)
$username = $_SESSION['user_id'];  // Adjust as necessary

// Dummy data for demonstration. Replace this with actual database queries
$payments = [
    ['Payment ID' => 1, 'Tenancy ID' => 101, 'Payment Date' => '2025-01-01', 'Rent Per Month' => 500, 'VAT' => 50, 'Total Payment' => 550, 'Annual Gross Rent' => 6000, 'Period' => 'Jan 2025', 'Approval Status' => 'Approved', 'Approval Date' => '2025-01-02', 'Payment Received by Landlord' => 'Yes', 'Balance Due' => 0, 'Comments' => 'N/A'],
    ['Payment ID' => 2, 'Tenancy ID' => 102, 'Payment Date' => '2025-01-15', 'Rent Per Month' => 600, 'VAT' => 60, 'Total Payment' => 660, 'Annual Gross Rent' => 7200, 'Period' => 'Jan 2025', 'Approval Status' => 'Pending', 'Approval Date' => '', 'Payment Received by Landlord' => 'No', 'Balance Due' => 660, 'Comments' => 'Late payment'],
    // Add more dummy data as needed
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>

    <!-- Load Bootstrap from CDN -->
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
                        <a class="nav-link" href="manage_payments.php">
                            <i class="fas fa-money-bill-wave"></i> Manage Payments
                        </a>
                    </li>
                    <!-- Add other nav items as needed -->
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
                    <h4 class="text-primary">View Payments</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Payments</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Tenancy ID</th>
                                        <th>Payment Date</th>
                                        <th>Rent Per Month</th>
                                        <th>VAT</th>
                                        <th>Total Payment</th>
                                        <th>Annual Gross Rent</th>
                                        <th>Period</th>
                                        <th>Approval Status</th>
                                        <th>Approval Date</th>
                                        <th>Payment Received by Landlord</th>
                                        <th>Balance Due</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['Payment ID']); ?></td>
                                            <td><?= htmlspecialchars($payment['Tenancy ID']); ?></td>
                                            <td><?= htmlspecialchars($payment['Payment Date']); ?></td>
                                            <td><?= htmlspecialchars($payment['Rent Per Month']); ?></td>
                                            <td><?= htmlspecialchars($payment['VAT']); ?></td>
                                            <td><?= htmlspecialchars($payment['Total Payment']); ?></td>
                                            <td><?= htmlspecialchars($payment['Annual Gross Rent']); ?></td>
                                            <td><?= htmlspecialchars($payment['Period']); ?></td>
                                            <td><?= htmlspecialchars($payment['Approval Status']); ?></td>
                                            <td><?= htmlspecialchars($payment['Approval Date']); ?></td>
                                            <td><?= htmlspecialchars($payment['Payment Received by Landlord']); ?></td>
                                            <td><?= htmlspecialchars($payment['Balance Due']); ?></td>
                                            <td><?= htmlspecialchars($payment['Comments']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
