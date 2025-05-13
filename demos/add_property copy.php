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


// Fetch the username from the session (adjust this depending on how user data is stored)
$username = $_SESSION['username'];  // Adjust as necessary

// Database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sn_file = $_POST['sn_file'];
    $plot_number = $_POST['plot_number'];
    $landlord_id = $_POST['landlord_id'];
    $district = $_POST['district'];
    $location = $_POST['location'];
    $geo_ref_coordinates = $_POST['geo_ref_coordinates'];
    $property_type_id = $_POST['property_type_id'];
    $rca = $_POST['rca'];
    $area = $_POST['area_m2'];
    $description = $_POST['description'];

    // Check if a new landlord needs to be added
    if (empty($landlord_id)) {
        $stmt = $conn->prepare("INSERT INTO landlord (name, phone_no) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['new_landlord_name'], $_POST['new_landlord_phone']);
        $stmt->execute();
        $landlord_id = $stmt->insert_id; // Get newly added landlord's ID
    }

    // Insert property
    $stmt = $conn->prepare("INSERT INTO property (sn_file, plot_number, landlord_id, district, location, geo_ref_coordinates, property_type_id, rca, area, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $sn_file, $plot_number, $landlord_id, $district, $location, $geo_ref_coordinates, $property_type_id, $rca, $area, $description);
    
    if ($stmt->execute()) {
        echo "New property added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>

    <!-- Load Bootstrap from CDN -->
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for search & pagination -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <!-- Top Bar -->
  <?php include('assets/templates/topbar.php');?>
  
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
                        <a class="nav-link" href="view_properties.php">
                            <i class="fas fa-building"></i> Manage Properties
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
                    <h4 class="text-primary">Add Property</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Information</h5>
                            <form action="add_property.php" method="post">
                               
                                <div class="mb-3">
                                    <label for="sn_file" class="form-label">SN File</label>
                                    <input type="text" class="form-control" id="sn_file" name="sn_file" required>
                                </div>
                                <div class="mb-3">
                                    <label for="plot_number" class="form-label">Plot Number</label>
                                    <input type="text" class="form-control" id="plot_number" name="plot_number" required>
                                </div>
                                <div class="mb-3">
    <label for="landlord_search" class="form-label">Search Landlord</label>
    <input type="text" class="form-control" id="landlord_search" placeholder="Enter phone, ID, or name">
    <div id="landlord_results" class="list-group"></div>
    
    <!-- Hidden fields to store selected landlord ID -->
    <input type="hidden" id="landlord_id" name="landlord_id">
</div>

<!-- Add New Landlord Section -->
<div id="new_landlord_section" style="display: none;">
    <h5>Add New Landlord</h5>
    <div class="mb-3">
        <label for="new_landlord_name" class="form-label">Landlord Name</label>
        <input type="text" class="form-control" id="new_landlord_name" name="new_landlord_name">
    </div>
    <div class="mb-3">
        <label for="new_landlord_phone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="new_landlord_phone" name="new_landlord_phone">
    </div>
</div>
 
       





                              
                                <div class="mb-3">
                                    <label for="district" class="form-label">District</label>
                                    <input type="text" class="form-control" id="district" name="district" required>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                </div>
                                <div class="mb-3">
                                    <label for="geo_ref_coordinates" class="form-label">Geo Reference Coordinates</label>
                                    <input type="text" class="form-control" id="geo_ref_coordinates" name="geo_ref_coordinates" required>
                                </div>
                                <div class="mb-3">
                                    <label for="property_type_id" class="form-label">Property Type</label>
                                    <input type="text" class="form-control" id="property_type_id" name="property_type_id" required>
                                </div>
                                <div class="mb-3">
                                    <label for="rca" class="form-label">RCA</label>
                                    <input type="text" class="form-control" id="rca" name="rca" required>
                                </div>
                               
                               
                                <div class="mb-3">
                                    <label for="area_m2" class="form-label">Area (m2)</label>
                                    <input type="text" class="form-control" id="area_m2" name="area_m2" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>


<script>
$(document).ready(function() {
    $("#landlord_search").on("input", function() {
        let query = $(this).val();
        if (query.length < 2) return;

        $.get("search_landlord.php?q=" + query, function(data) {
            $("#landlord_results").html(data);
        });
    });
});

function selectLandlord(id, name) {
    $("#landlord_id").val(id);
    $("#landlord_search").val(name);
    $("#landlord_results").html("");
    $("#new_landlord_section").hide();
}

// Show new landlord section if not found
$("#landlord_search").on("blur", function() {
    setTimeout(function() {
        if (!$("#landlord_id").val()) {
            $("#new_landlord_section").show();
        }
    }, 300);
});


</script>

                            </div>
                    </div>
                </div>
            </div>

       </main>
    </div>
</div>

<!-- Bootstrap JS -->
</body>
<?php include('assets/templates/footer.php');?>
</html>
