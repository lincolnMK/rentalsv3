<?php
// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $occupation_date = isset($_POST['occupation_date']) ? mysqli_real_escape_string($conn, $_POST['occupation_date']) : '';
    $rent_first_occupation = isset($_POST['rent_first_occupation']) ? mysqli_real_escape_string($conn, $_POST['rent_first_occupation']) : '';
    $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
    $occupant_id = mysqli_real_escape_string($conn, $_POST['occupant_id']);
    $monthly_rent = mysqli_real_escape_string($conn, $_POST['monthly_rent']);
    $termination_status = mysqli_real_escape_string($conn, $_POST['termination_status']);

    $created_by = isset($_SESSION['username']) ? mysqli_real_escape_string($conn, $_SESSION['username']) : 'system';
    $created_at = date('Y-m-d H:i:s');

    // Validate required fields
    if (empty($occupation_date) || empty($rent_first_occupation) || empty($property_id) || empty($occupant_id) ||
        empty($monthly_rent) || empty($termination_status)) {
        die("Error: All fields are required.");
    }

    // Check if property exists
    $checkProperty = mysqli_query($conn, "SELECT * FROM property WHERE Property_ID = '$property_id'");
    if (mysqli_num_rows($checkProperty) === 0) {
        echo "<script>
            alert('Error: Property ID does not exist. Please choose another property.');
            window.history.back();
        </script>";
        exit;
    }

    // Check if occupant exists
    $checkOccupant = mysqli_query($conn, "SELECT * FROM occupants WHERE Occupant_ID = '$occupant_id'");
    if (mysqli_num_rows($checkOccupant) === 0) {
        echo "<script>
            alert('Error: Occupant ID does not exist.');
            window.history.back();
        </script>";
        exit;
    }

    // Check lease status for the property
    $leaseCheckQuery = "SELECT lease_id, lease_status FROM lease WHERE property_id = '$property_id' ORDER BY lease_id DESC LIMIT 1";
    $leaseCheckResult = mysqli_query($conn, $leaseCheckQuery);

    if (mysqli_num_rows($leaseCheckResult) == 0) {
        echo "<script>
            if (confirm('Property not yet leased. Create lease?')) {
                window.location.href = 'add_lease.php?property_id=$property_id';
            } else {
                window.history.back();
            }
        </script>";
        exit;
    } else {
        $leaseData = mysqli_fetch_assoc($leaseCheckResult);
        $leaseStatus = $leaseData['lease_status'];
        $leaseId = $leaseData['lease_id'];

        if (strtolower($leaseStatus) !== 'active') {
            echo "<script>
                if (confirm('Property is \"$leaseStatus\". Choose another or edit lease?')) {
                    window.location.href = 'lease_details.php?lease_id=$leaseId';
                } else {
                    window.history.back();
                }
            </script>";
            exit;
        }
    }

    // Insert into tenancy including the lease_id
    $sql = "INSERT INTO tenancy (
                Lease_ID,
                Monthly_rent, 
                Property_ID, 
                Occupant_ID, 
                Occupation_date, 
                Rent_first_occupation, 
                Termination_status, 
                created_at,
                created_by
            ) VALUES (
                '$leaseId',
                '$monthly_rent',
                '$property_id',
                '$occupant_id',
                '$occupation_date',
                '$rent_first_occupation',
                '$termination_status',
                '$created_at',
                '$created_by'
            )";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Tenancy record added successfully!');
            window.location.href = 'view_tenancy.php';
        </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>



            <!-- Button to toggle sidebar visibility -->
            <button id="sidebarToggle" class="btn btn-dark d-md-none">☰</button>

            <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Add Tenancy</h4>
                </div>
            </div>

            <!-- Content Goes Here -->



    <div class="row p-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                

                <h5 class="card-title">Tenancy Information</h5>
<form action="add_tenancy.php" method="post">
    <div class="mb-3">
        <label for="property_id" class="form-label">Property ID</label>
        <input type="text" class="form-control" id="property_id" name="property_id" required>
    </div>

    <div class="mb-3">
        <label for="occupant_id" class="form-label">Occupant ID</label>
        <input type="text" class="form-control" id="occupant_id" name="occupant_id" required>
    </div>

  

    <div class="mb-3">
        <label for="occupation_date" class="form-label">Occupation Date</label>
        <input type="date" class="form-control" id="occupation_date" name="occupation_date" required>
    </div>

    <div class="mb-3">
        <label for="rent_first_occupation" class="form-label">First Month's Rent</label>
        <input type="number" step="0.01" class="form-control" id="rent_first_occupation" name="rent_first_occupation" required>
    </div>

    <div class="mb-3">
        <label for="monthly_rent" class="form-label">Monthly Rent</label>
        <input type="number" step="0.01" class="form-control" id="monthly_rent" name="monthly_rent" required>
    </div>

   

    <div class="mb-3">
        <label for="termination_status" class="form-label">Termination Status</label>
        <input type="text" class="form-control" id="termination_status" name="termination_status" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit Tenancy</button>
</form>



                </div>
            </div>
        </div>
    </div>
</div>
</div>
