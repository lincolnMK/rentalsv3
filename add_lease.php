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

// Database connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for Lease
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $lease_type = mysqli_real_escape_string($conn, $_POST['lease_type']);
    $lease_duration = mysqli_real_escape_string($conn, $_POST['lease_duration']);
    $lease_start = mysqli_real_escape_string($conn, $_POST['lease_start']);
    $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
    $lease_status = mysqli_real_escape_string($conn, $_POST['lease_status']);
    // Get session user for created_by
    $created_by = isset($_SESSION['username']) ? mysqli_real_escape_string($conn, $_SESSION['username']) : 'system';

    // Get current timestamp
    $created_at = date('Y-m-d H:i:s');

    // Validate property existence
    $checkProperty = mysqli_query($conn, "SELECT * FROM property WHERE Property_ID = '$property_id'");
    if (mysqli_num_rows($checkProperty) === 0) {
        echo "<script>
                alert('Error: Property ID does not exist. Please choose another property.');
                window.history.back();
              </script>";
        exit;
    }

    // Check if the property is already leased
    $checkPropertyLeased = mysqli_query($conn, "SELECT * FROM lease WHERE Property_ID = '$property_id' AND Lease_status = 'active'");
    if (mysqli_num_rows($checkPropertyLeased) > 0) {
        // Property is already leased
        echo "<script>
                alert('Error: This property is already leased. Please select a different property.');
                window.history.back();
              </script>";
        exit;
    }

    // Prepare the SQL statement to insert lease data
    $sql = "INSERT INTO lease (
                Lease_type, Lease_duration, Lease_start, Property_ID, Lease_status, created_at, created_by
            ) VALUES (?, ?, ?, ?, ?,?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error: " . $conn->error;
        exit;
    }

    // Bind parameters
    $stmt->bind_param("sssssss", $lease_type, $lease_duration, $lease_start, $property_id, $lease_status, $created_at, $created_by);

    // Execute the query
    if ($stmt->execute()) {
        echo "Lease record added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    mysqli_close($conn);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lease</title>

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
                        <a class="nav-link" href="view_leases.php">
                            <i class="fas fa-building"></i> Manage Leases
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
                    <h4 class="text-primary">Add Lease</h4>
                </div>
            </div>

            
    <!-- Content Goes Here -->
    <div class="row p-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Lease Information</h5>
<form action="add_lease.php" method="post">
    <div class="mb-3">
        <label for="lease_type" class="form-label">Lease Type</label>
        <input type="text" class="form-control" id="lease_type" name="lease_type" required>
    </div>

    <div class="mb-3">
        <label for="lease_duration" class="form-label">Lease Duration (in months)</label>
        <input type="number" class="form-control" id="lease_duration" name="lease_duration" required>
    </div>

    <div class="mb-3">
        <label for="lease_start" class="form-label">Lease Start Date</label>
        <input type="date" class="form-control" id="lease_start" name="lease_start" required>
    </div>

    <div class="mb-3">
        <label for="property_id" class="form-label">Property ID</label>
        <input type="text" class="form-control" id="property_id" name="property_id" required>
    </div>

    <div class="mb-3">
    <label for="lease_status" class="form-label">Lease Status</label>
    <select class="form-control" id="lease_status" name="lease_status" required>
        <option value="" disabled selected>Select Lease Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="pending">Pending</option>
        <option value="terminated">Terminated</option>
    </select>
</div>

    <button type="submit" class="btn btn-primary">Submit Lease</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

</div>
</body>
<?php
    include('assets/templates/footer.php'); 
?>
</html>
