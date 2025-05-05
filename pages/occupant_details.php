<?php
// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

// Check if occupant ID is provided
if (!isset($_GET['Occupant_ID']) || !is_numeric($_GET['Occupant_ID'])) {
    echo '<p class="error">Invalid occupant ID provided.</p>';
    exit;
}

$occupant_id = (int) $_GET['Occupant_ID'];

// SQL query to get detailed occupant info
$sql = "SELECT 
    o.Occupant_ID,
    o.Name AS OccupantName,
    o.Contact,
    o.Email,
    m.MDA_name,
    t.Used_for,
    t.created_at,
    p.District,
    p.Plot_number,
    p.Location,
    t.Property_ID,
    l.Name AS LandlordName,
    l.Landlord_ID
    FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID = t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
    WHERE o.Occupant_ID = ?";

// Prepare statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<p class="error">Database prepare failed: ' . htmlspecialchars($conn->error) . '</p>';
    exit;
}

$stmt->bind_param("i", $occupant_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if occupant found
if ($result->num_rows === 0) {
    echo '<p class="error">Sorry, no occupant found with this ID.</p>';
    exit;
}

$occupant = $result->fetch_assoc();
$stmt->close();
?>

<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Occupant Details</h4>
    </div>
</div>

<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Occupant: <?= htmlspecialchars($occupant['OccupantName'] ?? 'N/A') ?></h5>

                <!-- Tabs navigation -->
                <ul class="nav nav-tabs" id="occupantTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="occupant-tab" data-bs-toggle="tab" data-bs-target="#occupant" type="button" role="tab">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tenancy-tab" data-bs-toggle="tab" data-bs-target="#tenancy" type="button" role="tab">Tenancy Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="property-tab" data-bs-toggle="tab" data-bs-target="#property" type="button" role="tab">Property Info</button>
                    
                </ul>

                <!-- Tabs content -->
                <div class="tab-content pt-3" id="occupantTabContent">
                    <!-- Occupant Info -->
                    <div class="tab-pane fade show active" id="occupant" role="tabpanel">
                    <div class="card mb-4">
                    <div class="card-body">    
                    <dl class="row">
                            <dt class="col-sm-4">Occupant Name</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['OccupantName']) ?></dd>

                            <dt class="col-sm-4">Contact</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Contact']) ?></dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Email']) ?></dd>

                            <dt class="col-sm-4">MDA</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['MDA_name']) ?></dd>
                        </dl>
                        </div>
</div>
<a href="index.php?page=edit_occupant&occupant_id=<?= $occupant_id ?>" class="btn btn-primary">Edit occupant</a>
                   
</div>

                    <!-- Tenancy Info -->
                    <div class="tab-pane fade" id="tenancy" role="tabpanel">
                    <div class="card mb-4">
                    <div class="card-body">       
                    <dl class="row">

                    <dt class="col-sm-4">Used For</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($occupant['Used_for']) ?></dd>
                            <dt class="col-sm-4">Used For</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Used_for']) ?></dd>

                            <dt class="col-sm-4">Tenancy Start</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['created_at']) ?></dd>

                            <dt class="col-sm-4">Tenancy End</dt>
                            <dd class="col-sm-8">
                                <?php
                                $startDate = new DateTime($occupant['created_at']);
                                $endDate = (clone $startDate)->modify('+1 year');
                                echo htmlspecialchars($endDate->format('Y-m-d'));
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

                    <!-- Property Info -->
                    <div class="tab-pane fade" id="property" role="tabpanel">
                    <div class="card mb-4">
                    <div class="card-body">    
                    <dl class="row">
                   
                             <dt class="col-sm-4">Landlord Name</dt>
                           <dd class="col-sm-8">
                            
                           <a href="index.php?page=landlord_details&Landlord_ID=<?= $occupant['Landlord_ID']?>"> 
                           <?= htmlspecialchars($occupant['LandlordName']) ?>
                            </a>
                        </dd> 

                            <dt class="col-sm-4">Property ID</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Property_ID']?? '') ?></dd>
                            
                            <dt class="col-sm-4">District</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['District']) ?></dd>

                            <dt class="col-sm-4">Plot Number</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Plot_number']) ?></dd>

                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Location']) ?></dd>

                            <dt class="col-sm-4">Property Type</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($occupant['Used_for']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

                    <!-- Landlord Info -->
                    
                </div>

                
            </div>
        </div>
    </div>
</div>
