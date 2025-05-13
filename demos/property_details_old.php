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

// Get property_id from query parameters
$property_id = $_GET['Property_ID'] ?? 0;

// Fetch property details from the database
$query = "SELECT 
            p.Property_ID, 
            p.Plot_Number, 
            p.District, 
            p.Location, 
            p.Area, 
            p.Description, 
            l.Name AS Landlord_Name, 
            o.Name AS Occupant_Name, 
            t.Used_for AS Usage_Type, 
            p.Description
          FROM Property p 
          LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID 
          LEFT JOIN Tenancy t ON p.Property_ID = t.Property_id
          LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
          WHERE p.Property_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();





?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>




    <script>
    // JavaScript function to enable editing
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
    <div class="row bg-dark text-white py-2">
        <div class="col-md-6">
            <h4 class="ms-4">Logged in as: <?= htmlspecialchars($username); ?></h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="logout.php" class="btn btn-light me-4">Logout</a>
        </div>
    </div>

    <div class="row">
        <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
            <div class="d-flex flex-column align-items-start py-3">
                <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                <h3 class="ms-3">Rental System</h3>
                <ul class="nav flex-column w-100 mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="homepage.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_properties.php">View Properties</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 bg-light">
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Property Details</h4>
                </div>
            </div>

            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Information</h5>
                           
                            
                            <form action="property_details.php?Property_ID=<?= $property_id ?>" method="post">
    <div class="mb-3">
        <label for="Property_ID" class="form-label">Property#</label>
        <input type="text" class="form-control" id="Property_ID" name="Property_ID" value="<?= htmlspecialchars($property['Property_ID'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="Landlord_Name" class="form-label">Landlord Name</label>
        <input type="text" class="form-control" id="Landlord_Name" name="Landlord_Name" value="<?= htmlspecialchars($property['Landlord_Name'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="Occupant_Name" class="form-label">Occupant</label>
        <input type="text" class="form-control" id="Occupant_Name" name="Occupant_Name" value="<?= htmlspecialchars($property['Occupant_Name'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="usage" class="form-label">Usage</label>
        <input type="text" class="form-control" id="Usage_Type" name="Usage_Type" value="<?= htmlspecialchars($property['Usage_Type'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="plot_number" class="form-label">Plot Number</label>
        <input type="text" class="form-control" id="plot_number" name="plot_number" value="<?= htmlspecialchars($property['Plot_Number'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="district" class="form-label">District</label>
        <input type="text" class="form-control" id="district" name="district" value="<?= htmlspecialchars($property['District'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($property['Location'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="area_m2" class="form-label">Area (sq.m)</label>
        <input type="number" class="form-control" id="area_m2" name="area_m2" value="<?= htmlspecialchars($property['Area'] ?? '') ?>" disabled required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" disabled required><?= htmlspecialchars($property['Description'] ?? '') ?></textarea>
    </div>

    <!-- Buttons to edit and update -->
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

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
