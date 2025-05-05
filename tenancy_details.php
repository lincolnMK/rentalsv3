<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username from the session
$username = $_SESSION['username'];

// Get tenancy_id from query parameters
$tenancy_id = $_GET['tenancy_id'];

// Fetch tenancy details from the database
$query = "SELECT * FROM TENANCY WHERE Tenancy_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $tenancy_id);
$stmt->execute();
$result = $stmt->get_result();
$tenancy = $result->fetch_assoc();

if (!$tenancy) {
    echo "Tenancy not found!";
    exit;
}

// Handle form submission for updating tenancy details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $occupant_id = $_POST['occupant_id'];
    $rca = $_POST['rca'];
    $monthly_rent = $_POST['monthly_rent'];
    $termination_status = $_POST['termination_status'];

    $query = "UPDATE TENANCY SET Property_id = ?, Occupant_id = ?, RCA = ?, Monthly_rent = ?, Termination_status = ? WHERE Tenancy_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iisisi', $property_id, $occupant_id, $rca, $monthly_rent, $termination_status, $tenancy_id);

    if ($stmt->execute()) {
        echo "Tenancy details updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Details</title>
    <!-- Load Bootstrap from CDN -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function enableEditing() {
            var formElements = document.querySelectorAll('.form-control');
            formElements.forEach(function(element) {
                element.disabled = false;
            });
            document.getElementById('editButton').style.display = 'none';
            document.getElementById('updateButton').style.display = 'block';
        }
    </script>
</head>
<body>
<div class="container-fluid">
    <!-- Top Bar -->
    <div class="row bg-dark text-white py-2">
        <div class="col-md-6">
            <h4 class="ms-4">Logged in as: <?= htmlspecialchars($username); ?></h4>
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
                <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                <h3 class="ms-3">Rental System</h3>
                <ul class="nav flex-column w-100 mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="homepage.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_tenancy.php">
                            <i class="fas fa-list"></i> View Tenancies
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-10 bg-light">
            <!-- Button to toggle sidebar visibility -->
            <button id="sidebarToggle" class="btn btn-dark d-md-none">☰</button>

            <!-- Topbar ( fInside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Tenancy Details</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tenancy Information</h5>
                            
                            <form action="tenancy_details.php?tenancy_id=<?= $tenancy_id ?>" method="post">
                                <div class="mb-3">
                                    <label for="property_id" class="form-label">Property ID</label>
                                    <input type="text" class="form-control" id="property_id" name="property_id" value="<?= htmlspecialchars($tenancy['Property_id']); ?>" disabled required>
                                </div>
                                <div class="mb-3">
                                    <label for="occupant_id" class="form-label">Occupant ID</label>
                                    <input type="text" class="form-control" id="occupant_id" name="occupant_id" value="<?= htmlspecialchars($tenancy['Occupant_id']); ?>" disabled required>
                                </div>
                                <div class="mb-3">
                                    <label for="rca" class="form-label">RCA</label>
                                    <input type="text" class="form-control" id="rca" name="rca" value="<?= htmlspecialchars($tenancy['RCA']); ?>" disabled required>
                                </div>
                                <div class="mb-3">
                                    <label for="monthly_rent" class="form-label">Monthly Rent</label>
                                    <input type="text" class="form-control" id="monthly_rent" name="monthly_rent" value="<?= htmlspecialchars($tenancy['Monthly_rent']); ?>" disabled required>
                                </div>
                                <div class="mb-3">
                                    <label for="termination_status" class="form-label">Termination Status</label>
                                    <input type="text" class="form-control" id="termination_status" name="termination_status" value="<?= htmlspecialchars($tenancy['Termination_status']); ?>" disabled required>
                                </div>
                                <button type="button" id="editButton" class="btn btn-secondary" onclick="enableEditing()">Edit Details</button>
                                <button type="submit" id="updateButton" class="btn btn-primary" style="display:none;">Update Details</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
