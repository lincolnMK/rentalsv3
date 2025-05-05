<?php

include_once __DIR__ . '/../auth_check.php';


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

  
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Insert property
    $stmt = $conn->prepare("INSERT INTO property (sn_file, plot_number, landlord_id, district, location, geo_ref_coordinates, property_type_id, rca, area, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssssss", $sn_file, $plot_number, $landlord_id, $district, $location, $geo_ref_coordinates, $property_type_id, $rca, $area, $description);
    
    if ($stmt->execute()) {
        echo "New property added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}


?>
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
                            <div class="container" style="max-width: 600px;">
  <form action="add_property.php" method="post">
    <div class="row">
      <div class="col-md-6 mb-2">
        <label for="sn_file" class="form-label">SN File</label>
        <input type="text" class="form-control form-control-sm" id="sn_file" name="sn_file" required>
      </div>
      <div class="col-md-6 mb-2">
        <label for="plot_number" class="form-label">Plot Number</label>
        <input type="text" class="form-control form-control-sm" id="plot_number" name="plot_number" required>
      </div>

      <div class="mb-3">
    <label for="landlord_id" class="form-label">Landlord</label>
    <div class="input-group">
        <input type="text" class="form-control" id="landlord_name" placeholder="Select a landlord" required readonly>
        <input type="hidden" id="landlord_id" name="landlord_id" required>

        <!-- Search Button -->
      

        <!-- Clear Selection Button -->
        <button type="button" class="btn btn-secondary" id="clearLandlord">
            Search
        </button>
    </div>
</div>

      <div class="col-md-6 mb-2">
        <label for="district" class="form-label">District</label>
        <input type="text" class="form-control form-control-sm" id="district" name="district" required>
      </div>
      <div class="col-md-6 mb-2">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control form-control-sm" id="location" name="location" required>
      </div>

      <div class="col-md-6 mb-2">
        <label for="geo_ref_coordinates" class="form-label">Geo Reference Coordinates</label>
        <input type="text" class="form-control form-control-sm" id="geo_ref_coordinates" name="geo_ref_coordinates" required>
      </div>
      <div class="col-md-6 mb-2">
        <label for="property_type_id" class="form-label">Property Type</label>
        <input type="text" class="form-control form-control-sm" id="property_type_id" name="property_type_id" required>
      </div>

      <div class="col-md-6 mb-2">
        <label for="rca" class="form-label">RCA</label>
        <input type="text" class="form-control form-control-sm" id="rca" name="rca" required>
      </div>
      <div class="col-md-6 mb-2">
        <label for="area_m2" class="form-label">Area (m²)</label>
        <input type="text" class="form-control form-control-sm" id="area_m2" name="area_m2" required>
      </div>

      <div class="col-12 mb-2">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control form-control-sm" id="description" name="description" rows="2" required></textarea>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
  </form>
</div>


<!-- Landlord Search Modal -->
<div class="modal fade" id="landlordModal" tabindex="-1" aria-labelledby="landlordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="landlordModalLabel">Search Landlord</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Bar -->
                <input type="text" class="form-control mb-3" id="searchLandlord" placeholder="Search by name..." onkeyup="fetchLandlords(1)">

                <!-- Search Results -->
                <div id="landlordResults"></div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
            <div class="modal-footer">
                <a href="add_landlord.php" class="btn btn-primary">Create New Landlord</a>
            </div>
        </div>
    </div>
</div>

<script>
function fetchLandlords(page = 1) {
    let query = $("#searchLandlord").val();

    $.ajax({
        url: "fetch_landlords.php",
        type: "GET",
        data: { query: query, page: page },
        success: function(response) {
            let data = JSON.parse(response);
            $("#landlordResults").html(data.results);
            $("#pagination").html(data.pagination);
        }
    });
}

// Ensure dynamic pagination works
$(document).on('click', '#pagination .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    fetchLandlords(page);
});

// Handle landlord selection
$(document).ready(function () {
    $('#landlordModal').on('shown.bs.modal', function () {
        // Ensure previous backdrops don't interfere
        $('body').removeClass('modal-open');
    });

    $(document).on('click', '.select-landlord', function () {
        let landlordId = $(this).data('landlord_id');
        let landlordName = $(this).data('name');

        $('#landlord_id').val(landlordId);
        $('#landlord_name').val(landlordName);

        $('#landlordModal').modal('hide');
    });

    $('#landlordModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open'); // Let Bootstrap handle backdrops
    });

    // Improve search efficiency with debounce
    let searchTimeout;
    $('#searchLandlord').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchLandlords(1);
        }, 300); // Debounce search
    });

    // Clear selection and reopen search modal
    $('#clearLandlord').on('click', function () {
        //$('#landlord_id').val('');
        $('#landlord_name').val('');
        $('#landlordModal').modal('show'); // Open modal for re-selection
    });
});



</script>







                            </div>
                    </div>
                </div>
            </div>

