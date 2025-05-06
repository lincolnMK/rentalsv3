<?php
// --- Connect to database and check authentication ---
include_once __DIR__ . '/../auth_check.php';


// --- Get property ID ---
if (!isset($_GET['Property_ID'])) {
    die('Property ID not specified.');
}
$propertyId = (int) $_GET['Property_ID'];

// --- Fetch property details ---
$sql = "SELECT * FROM Property WHERE Property_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    die('Property not found.');
}

// --- Generate CSRF token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- Fetch landlords list (for dropdown) ---
$landlordsResult = $conn->query("SELECT Landlord_ID, Name FROM Landlord");
$landlords = $landlordsResult ? $landlordsResult->fetch_all(MYSQLI_ASSOC) : [];

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }

    $plotNumber = $_POST['Plot_number'] ?? '';
    $district = $_POST['District'] ?? '';
    $location = $_POST['Location'] ?? '';
    $area = (float) ($_POST['Area'] ?? 0);
    $description = $_POST['Description'] ?? '';
    $landlordId = (int) ($_POST['Landlord_ID'] ?? 0);

    $updateSql = "
        UPDATE Property 
        SET Plot_number = ?, District = ?, Location = ?, Area = ?, Description = ?, Landlord_ID = ?
        WHERE Property_ID = ?
    ";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param(
        "sssdsii", // s=string, d=double, i=integer
        $plotNumber, 
        $district, 
        $location, 
        $area, 
        $description, 
        $landlordId,
        $propertyId
    );

    if ($updateStmt->execute()) {
        echo "<script type='text/javascript'>
            window.location.href = \"index.php?page=property_details&Property_ID=$propertyId\";
        </script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to update property: " . htmlspecialchars($conn->error) . "</div>";
    }
    
}
?>

<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Edit Property Details</h4>
    </div>
</div>

<div class="row p-4">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">

                <h5 class="card-title">
                    Property: <?= htmlspecialchars($property['Plot_number'] ?? 'N/A') ?> in <?= htmlspecialchars($property['District'] ?? 'N/A') ?> District
                </h5>

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="propertyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="property-details-tab" data-bs-toggle="tab" href="#property-details" role="tab" aria-controls="property-details" aria-selected="true">General</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="landlord-details-tab" data-bs-toggle="tab" href="#landlord-details" role="tab" aria-controls="landlord-details" aria-selected="false">Landlord</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3">

                    <!-- Property Details Tab -->
                    <div class="tab-pane fade show active" id="property-details" role="tabpanel" aria-labelledby="property-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">

                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="Plot_number" class="form-label me-3" style="min-width: 150px;">Plot Number</label>
                                        <input type="text" class="form-control w-auto" id="Plot_number" name="Plot_number" value="<?= htmlspecialchars($property['Plot_number'] ?? '') ?>" required>
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="District" class="form-label me-3" style="min-width: 150px;">District</label>
                                        <input type="text" class="form-control w-auto" id="District" name="District" value="<?= htmlspecialchars($property['District'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="Location" class="form-label me-3" style="min-width: 150px;">Location</label>
                                        <input type="text" class="form-control w-auto" id="Location" name="Location" value="<?= htmlspecialchars($property['Location'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="Area" class="form-label me-3" style="min-width: 150px;">Area (m²)</label>
                                        <input type="number" step="0.01" class="form-control w-auto" id="Area" name="Area" value="<?= htmlspecialchars($property['Area'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="Description" class="form-label me-3" style="min-width: 150px;">Description</label>
                                        <textarea class="form-control" id="Description" name="Description" rows="3" style="max-width:600px;"><?= htmlspecialchars($property['Description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="Landlord_ID" class="form-label me-3" style="min-width: 150px;">Landlord</label>
                                        <select class="form-select w-auto" id="Landlord_ID" name="Landlord_ID">
                                            <option value="0">-- Select Landlord --</option>
                                            <?php foreach ($landlords as $landlord): ?>
                                                <option value="<?= $landlord['Landlord_ID'] ?>" <?= $landlord['Landlord_ID'] == $property['Landlord_ID'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($landlord['Name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                        <a href="index.php?page=property_details&Property_ID=<?= $propertyId ?>" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <!-- Landlord Details Tab -->
                    <div class="tab-pane fade" id="landlord-details" role="tabpanel" aria-labelledby="landlord-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <p>Under development</p>
                            </div>
                        </div>
                    </div>

                </div> <!-- end tab-content -->

            </div>
        </div>

    </div>
</div>
