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

// Fetch landlords from the database
$sql = "SELECT landlord_id, Name FROM landlord";
$result = $conn->query($sql);
$landlords = [];

while ($row = $result->fetch_assoc()) {
    $landlords[] = $row;
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

    $sql = "INSERT INTO property (
        sn_file,
        Plot_number,
        Landlord_id,
        district,
        location,
        geo_ref_coordinates,
        property_type_id,
        rca,
        
       
        area,
        Description
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss",
        $sn_file,
        $plot_number,
        $landlord_id,
      
        $district,
        $location,
        $geo_ref_coordinates,
        $property_type_id,
        $rca,
       
        $area,
        $description
    );

    if ($stmt->execute()) {
        echo "New property added successfully.";
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
                              
                              
      <!-- Landlord Selection Field with Modal Trigger -->
<div class="mb-3">
    <label for="landlord_id" class="form-label">Landlord</label>
    <div class="input-group">
        <input type="text" class="form-control" id="landlord_name" placeholder="Select a landlord" required readonly>
        <input type="hidden" id="landlord_id" name="landlord_id">
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#landlordModal">
            Find
        </button>
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

<!-- Bootstrap Modal for Landlord Selection -->
<!-- Bootstrap Modal for Landlord Selection -->
<div class="modal fade" id="landlordModal" tabindex="-1" aria-labelledby="landlordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="landlordModalLabel">Select Landlord</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Bar -->
                <input type="text" id="searchLandlord" class="form-control mb-3" placeholder="Search landlord...">

                <!-- Table with Pagination -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="landlordTable">
                        <?php foreach ($landlords as $landlord): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($landlord['landlord_id']); ?></td>
                            <td><?php echo htmlspecialchars($landlord['Name']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm select-landlord" 
                                        data-id="<?php echo htmlspecialchars($landlord['landlord_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($landlord['Name']); ?>">
                                    Select
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination Controls -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <button class="page-link" id="prevPage">Previous</button>
                        </li>
                        <li class="page-item">
                            <span class="page-link" id="pageNumber">1</span>
                        </li>
                        <li class="page-item">
                            <button class="page-link" id="nextPage">Next</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Selection, Search, and Pagination -->


<script>
$(document).ready(function() {
    let landlords = [];
    let rows = $("#landlordTable tr");
    
    // Convert rows to array for pagination
    rows.each(function() {
        landlords.push($(this));
    });

    let currentPage = 1;
    let recordsPerPage = 10;

    function displayPage(page) {
        let start = (page - 1) * recordsPerPage;
        let end = start + recordsPerPage;

        rows.hide();
        landlords.slice(start, end).show();
        
        $("#pageNumber").text(page);
        $("#prevPage").prop("disabled", page === 1);
        $("#nextPage").prop("disabled", end >= landlords.length);
    }

    $("#prevPage").click(function() {
        if (currentPage > 1) {
            currentPage--;
            displayPage(currentPage);
        }
    });

    $("#nextPage").click(function() {
        if ((currentPage * recordsPerPage) < landlords.length) {
            currentPage++;
            displayPage(currentPage);
        }
    });

    // Initial page load
    displayPage(1);

    // Search functionality
    $("#searchLandlord").on("keyup", function() {
        let value = $(this).val().toLowerCase();
        landlords.forEach(row => {
            let name = row.find("td:nth-child(2)").text().toLowerCase();
            row.toggle(name.includes(value));
        });
    });

    // Selecting a landlord
    $(document).on("click", ".select-landlord", function() {
        let landlordId = $(this).data("id");
        let landlordName = $(this).data("name");

        $("#landlord_id").val(landlordId);
        $("#landlord_name").val(landlordName);
        $("#landlordModal").modal("hide");
    });
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
