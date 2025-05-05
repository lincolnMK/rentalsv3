<?php
// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';
include_once __DIR__ . '/../db_connection.php'; // assuming you have this

// Get landlord ID from query string
if (isset($_GET['Landlord_ID']) && is_numeric($_GET['Landlord_ID'])) {
    $landlord_id = intval($_GET['Landlord_ID']);

    // Query to fetch landlord details
    $sql = "SELECT 
                Landlord_ID, 
                Name, 
                Phone_Number, 
                National_ID, 
                Vendor_code, 
                Address,
                Bank_name,
                Bank_account_holder,
                Bank_account_number
            FROM landlord 
            WHERE Landlord_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $landlord_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $landlord = $result->fetch_assoc();

    $landlordName = $landlord['Name'];
    $propertyRecords = [];
$stmt = $conn->prepare("
    SELECT 
        p.Landlord_ID,  
        l.Name, 
        p.Property_ID,
        p.Plot_number, 
        o.Name as Occupant,
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
    WHERE l.Name = ?
");
$stmt->bind_param("s", $landlordName);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $propertyRecords[] = $row;
}




    if ($landlord) {
        ?>


<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Landlord Details</h4>
    </div>
</div>

<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Landlord: <?= htmlspecialchars($landlord['Name'] ?? 'N/A') ?></h5>

                <ul class="nav nav-tabs" id="landlordTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="landlord-details-tab" data-bs-toggle="tab" href="#landlord-details" role="tab" aria-controls="landlord-details" aria-selected="true">General</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="bank-details-tab" data-bs-toggle="tab" href="#bank-details" role="tab" aria-controls="bank-details" aria-selected="false">Bank Details</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="properties-tab" data-bs-toggle="tab" href="#properties" role="tab" aria-controls="properties" aria-selected="false">Properties</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="payment-details-tab" data-bs-toggle="tab" href="#payment-details" role="tab" aria-controls="payment-details" aria-selected="false">Payments</a>
    </li>
</ul>

                <div class="tab-content mt-3">
                    <!-- Landlord Details Tab -->
                    <div class="tab-pane fade show active" id="landlord-details" role="tabpanel" aria-labelledby="landlord-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <dl class="row">

                                <dt class="col-sm-3">Landlord ID</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($landlord['Landlord_ID'] ?? 'N/A') ?></dd>
                                    <dt class="col-sm-3">Name</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Name'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Phone Number</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Phone_Number'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">National ID</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['National_ID'] ?? 'N/A') ?></dd>

                                    <dt class="col-sm-3">Vendor Code</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Vendor_code'] ?? 'N/A') ?></dd>
                                    
                                    <dt class="col-sm-3">Address</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Address'] ?? 'N/A') ?></dd>
                                
                                
                                </dl>
                            </div>
                        </div>
                        <a href="index.php?page=edit_landlord&Landlord_ID=<?= $landlord['Landlord_ID'] ?>" class="btn btn-primary">Edit Landlord</a>
                    </div>


 <!-- Bank Details Tab -->
 <div class="tab-pane fade" id="bank-details" role="tabpanel" aria-labelledby="bank-details-tab">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Bank Name</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Bank_name'] ?? 'N/A') ?></dd>

                   

                    <dt class="col-sm-3">Account Name</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Bank_account_holder'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-3">Account Number</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($landlord['Bank_account_number'] ?? 'N/A') ?></dd>

                   
                </dl>
            </div>
        </div>
        <a href="index.php?page=edit_bank_details&Landlord_ID=<?= $landlord['Landlord_ID'] ?>" class="btn btn-primary">Edit Bank Details</a>
    </div>

                    <!-- Properties Tab -->
                    <div class="tab-pane fade" id="properties" role="tabpanel" aria-labelledby="properties-tab">
    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($propertyRecords)) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Property ID</th>
                                <th>Plot Number</th>
                                <th>Occupant</th>
                                <th>Used For</th>
                                <th>District</th>
                                <th>Location</th>
                                <th>Area</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($propertyRecords as $property) : ?>
                                <tr>
                                    <td>
                                    <a href="index.php?page=property_details&Property_ID=<?= $property['Property_ID']; ?>">
                                        <?= htmlspecialchars($property['Property_ID']?? '') ?>
                            </a>
                                    </td>
                                    <td><?= htmlspecialchars($property['Plot_number']?? '') ?></td>
                                    
                                    <td>
                                    <a href="index.php?page=occupant_details&Occupant_ID=<?= $property['Occupant_ID']; ?>">
                                        <?= htmlspecialchars($property['Occupant']?? '') ?>
                            </a>
                                    </td>
                                    
                                    <td><?= htmlspecialchars($property['Used_for']?? '') ?></td>
                                    <td><?= htmlspecialchars($property['District']?? '') ?></td>
                                    <td><?= htmlspecialchars($property['Location']?? '') ?></td>
                                    <td><?= htmlspecialchars($property['Area']?? '') ?></td>
                                    <td><?= nl2br(htmlspecialchars($property['Description']?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-muted">No properties found for this landlord.</p>
            <?php endif; ?>
        </div>
    </div>

                        <a href="index.php?page=add_property&Landlord_ID=<?= $landlord['Landlord_ID'] ?>" class="btn btn-primary">Add Property</a>
                    </div>


                   

                     <!-- Payment Details Tab -->
                     <div class="tab-pane fade" id="payment-details" role="tabpanel" aria-labelledby="payment-details-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php if (!empty($paymentRecords)) : ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Payment Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Method</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($paymentRecords as $payment) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($payment['Payment_Date'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($payment['Amount'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($payment['Status'] ?? 'Pending') ?></td>
                                                        <td><?= htmlspecialchars($payment['Method'] ?? 'N/A') ?></td>
                                                        <td><?= nl2br(htmlspecialchars($payment['Notes'] ?? 'No notes')) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <p class="text-muted">No payment records available for this landlord.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="" class="btn btn-primary">Add Payment</a>
                    </div>
                </div> <!-- End Tab Content -->

            </div> <!-- End Card Body -->
        </div> <!-- End Card -->
    </div> <!-- End Col -->
</div> <!-- End Row -->

        <?php
    } else {
        echo "<div class='alert alert-warning'>Landlord not found.</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No Landlord selected.</div>";
}
?>
