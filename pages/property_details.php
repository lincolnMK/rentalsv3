<?php
// Assume you already have your DB connection in $conn
include_once __DIR__ . '/../auth_check.php';

// Get Property ID from URL


if (isset($_GET['Property_ID'])) {
    $property_id = intval($_GET['Property_ID']);

    // Fetch property details
    $sql = "SELECT 
        p.Landlord_ID,  
        l.Name AS Landlord_Name, 
        p.Property_ID,
        p.Plot_number, 
        o.Name AS Occupant,
        t.Used_for,
        p.District, 
        p.Location, 
        p.Area,
        p.Description,
        o.Occupant_ID
    FROM Property p
    LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN Tenancy t ON p.Property_ID = t.Property_id
    LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
    WHERE p.Property_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();

    if ($property) {
?>
<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Property Details</h4>
    </div>
</div>

<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Property at: <?= htmlspecialchars($property['Plot_number'] ?? 'N/A')?> in <?= htmlspecialchars($property['District'] ?? 'N/A') ?> District </h5>

                <ul class="nav nav-tabs" id="propertyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="property-details-tab" data-bs-toggle="tab" href="#property-details" role="tab" aria-controls="property-details" aria-selected="true">General</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="landlord-details-tab" data-bs-toggle="tab" href="#landlord-details" role="tab" aria-controls="landlord-details" aria-selected="false">Landlord</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tenancy-details-tab" data-bs-toggle="tab" href="#tenancy-details" role="tab" aria-controls="tenancy-details" aria-selected="false">Tenancy </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="maintenance-tab" data-bs-toggle="tab" href="#maintenance" role="tab" aria-controls="maintenance" aria-selected="false">Maintenance</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">

                    <!-- Property Details Tab -->
                    <div class="tab-pane fade show active" id="property-details" role="tabpanel" aria-labelledby="property-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">Landlord</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Landlord_Name'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Plot Number</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Plot_number'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Occupant</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Occupant'] ?? 'Vacant') ?></dd>

                                    <dt class="col-sm-3">Used For</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Used_for'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">District</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['District'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Location</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Location'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Area (in m²)</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Area'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Description</dt>
                                    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($property['Description'] ?? 'No description available')) ?></dd>
                                </dl>
                                
                            </div>
                            
                        </div>
                        <a href="index.php?page=edit_property&Property_ID=<?= $property_id ?>" class="btn btn-primary">Edit Property</a>
                    </div>

                    <!-- Landlord Details Tab -->
                    <div class="tab-pane fade" id="landlord-details" role="tabpanel" aria-labelledby="landlord-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">Landlord Name</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Landlord_Name'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Landlord ID</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Landlord_ID'] ?? 'N/A') ?></dd>
                                </dl>
                            </div>
                        </div>
                        <a href="" class="btn btn-primary">View Landlord</a>
                    </div>

                    <!-- Tenancy Details Tab -->
                    <div class="tab-pane fade" id="tenancy-details" role="tabpanel" aria-labelledby="tenancy-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">Occupant Name</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Occupant'] ?? 'Vacant') ?></dd>

                                    <dt class="col-sm-3">Occupant ID</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Occupant_ID'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Used For</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($property['Used_for'] ?? 'N/A') ?></dd>
                                </dl>
                            </div>
                        </div>
                        <a href="" class="btn btn-primary">View Tenancy</a>
                    </div>
  <!-- Maintenance Tab -->
  <div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
    <div class="card mb-4">
        <div class="card-body">

            <?php if (!empty($maintenanceRecords)) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Maintenance Type</th>
                                <th>Issue Reported</th>
                                <th>Status</th>
                                <th>Date Reported</th>
                                <th>Date Resolved</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($maintenanceRecords as $record) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['Maintenance_Type'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($record['Issue_Reported'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($record['Maintenance_Status'] ?? 'Pending') ?></td>
                                    <td><?= htmlspecialchars($record['Date_Reported'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($record['Date_Resolved'] ?? 'N/A') ?></td>
                                    <td><?= nl2br(htmlspecialchars($record['Maintenance_Notes'] ?? 'No additional notes')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                 
                </div>
               
            <?php else : ?>
                <p class="text-muted">No maintenance records available for this property.</p>
            <?php endif; ?>

        </div>
        
    </div>
    <a href="" class="btn btn-primary">Add Record</a>
</div>

                </div> <!-- End Tab Content -->

            </div> <!-- End Card Body -->
        </div> <!-- End Card -->
    </div> <!-- End Col -->
</div> <!-- End Row -->


<?php
    } else {
        echo "<div class='alert alert-warning'>Property not found.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>No property selected.</div>";
}
?>
