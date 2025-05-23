<?php
include_once __DIR__ . '/../auth_check.php';

// Validate tenancy_id
if (!isset($_GET['Tenancy_ID']) || !is_numeric($_GET['Tenancy_ID'])) {
    echo "Invalid Tenancy ID.";
    exit;
}

$tenancy_id = (int) $_GET['Tenancy_ID'];

// Fetch tenancy details
$query = "SELECT 
    Rent_first_occupation, 
    t.Property_ID, 
    Monthly_rent AS Rate, 
    Termination_status AS Status, 
    Used_for AS Purpose, 
    l.Name AS Landlord, 
    o.Name AS Occupant
FROM TENANCY t
LEFT JOIN occupants o ON o.Occupant_ID = t.Occupant_ID
LEFT JOIN property p ON p.Property_ID = t.Property_ID
LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
WHERE t.Tenancy_ID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $tenancy_id);
$stmt->execute();
$result = $stmt->get_result();
$tenancy = $result->fetch_assoc();

if (!$tenancy) {
    echo "Tenancy not found!";
    exit;
}
$stmt->close();
?>

<!-- Topbar -->
<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Tenancy Details</h4>
    </div>
</div>

<!-- Show tenancy details -->
<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Occupant: <?= htmlspecialchars($tenancy['Occupant']) ?></h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Property ID:</strong> <?= htmlspecialchars($tenancy['Property_ID']) ?></li>
                    <li class="list-group-item"><strong>Rent First Occupation:</strong> <?= htmlspecialchars($tenancy['Rent_first_occupation']) ?></li>
                    <li class="list-group-item"><strong>Monthly Rent:</strong> <?= htmlspecialchars($tenancy['Rate']) ?></li>
                    <li class="list-group-item"><strong>Status:</strong> <?= htmlspecialchars($tenancy['Status']) ?></li>
                    <li class="list-group-item"><strong>Purpose:</strong> <?= htmlspecialchars($tenancy['Purpose']) ?></li>
                    <li class="list-group-item"><strong>Landlord:</strong> <?= htmlspecialchars($tenancy['Landlord']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
