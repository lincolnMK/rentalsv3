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

// Database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenancy_id = $_POST['tenancy_id'];
    $payment_date = $_POST['payment_date'];
    $rent_per_month = $_POST['rent_per_month'];
    $vat = $_POST['vat'];
    $total_payment = $_POST['total_payment'];
    $annual_gross_rent = $_POST['annual_gross_rent'];
    $period = $_POST['period'];
    $approval_status = $_POST['approval_status'];
    $approval_date = $_POST['approval_date'];
    $payment_received_by_landlord = $_POST['payment_received_by_landlord'];
    $balance_due = $_POST['balance_due'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO payments (
        tenancy_id,
        payment_date,
        rent_per_month,
        vat,
        total_payment,
        annual_gross_rent,
        period,
        approval_status,
        approval_date,
        payment_received_by_landlord,
        balance_due,
        comments
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isddddssssds",
        $tenancy_id,
        $payment_date,
        $rent_per_month,
        $vat,
        $total_payment,
        $annual_gross_rent,
        $period,
        $approval_status,
        $approval_date,
        $payment_received_by_landlord,
        $balance_due,
        $comments
    );

    if ($stmt->execute()) {
        echo "New payment added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment</title>

    <!-- Load Bootstrap from CDN -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <!-- Top Bar -->
    <?php 
    include('assets/templates/topbar.php');
    ?>

    <!-- Sidebar and Main Content Wrapper -->
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
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
                        <a class="nav-link" href="view_payments.php">
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
                    <h4 class="text-primary">Add Payment</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title">Payment Information</h5>
<form action="add_payment.php" method="post" id="paymentForm">
    <!-- Tenancy Lookup -->
    <div class="mb-3">
        <label for="tenancy_id" class="form-label">Tenancy ID</label>
        <input type="text" class="form-control" id="tenancy_id" name="tenancy_id" required>
    </div>

    <!-- Auto-Populated Fields -->
    <div class="mb-3">
        <label class="form-label">Landlord</label>
        <input type="text" class="form-control" id="landlord" name="landlord" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Plot #</label>
        <input type="text" class="form-control" id="plot_no" name="plot_no" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">District</label>
        <input type="text" class="form-control" id="district" name="district" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Property Type</label>
        <input type="text" class="form-control" id="property_type" name="property_type" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Occupant</label>
        <input type="text" class="form-control" id="occupant" name="occupant" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">MDA</label>
        <input type="text" class="form-control" id="mda" name="mda" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Rent / Month</label>
        <input type="text" class="form-control" id="rent_per_month" name="rent_per_month" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">VAT (16.5%)</label>
        <input type="text" class="form-control" id="vat" name="vat" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Annual Gross</label>
        <input type="text" class="form-control" id="annual_gross" name="annual_gross" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Amount Paid</label>
        <input type="number" class="form-control" id="amount_paid" name="amount_paid" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Balance</label>
        <input type="text" class="form-control" id="balance" name="balance" readonly>
    </div>

    <!-- Payment Details -->
    <div class="mb-3">
        <label class="form-label">Payment ID</label>
        <input type="text" class="form-control" id="payment_id" name="payment_id" readonly>
    </div>
    <div class="mb-3">
        <label for="payment_date" class="form-label">Payment Date</label>
        <input type="date" class="form-control" id="payment_date" name="payment_date" required>
    </div>
    <div class="mb-3">
        <label for="approval_date" class="form-label">Approval Date</label>
        <input type="date" class="form-control" id="approval_date" name="approval_date" required>
    </div>
    <div class="mb-3">
        <label for="approval_status" class="form-label">Approval Receipt Confirmation</label>
        <input type="text" class="form-control" id="approval_status" name="approval_status" required>
    </div>
    <div class="mb-3">
        <label for="payment_received_by_landlord" class="form-label">Payment Received by Landlord</label>
        <select class="form-control" id="payment_received_by_landlord" name="payment_received_by_landlord" required>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Submit Payment</button>
</form>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->

<?php

include('assets/templates/footer.php');
?>