<?php
include_once __DIR__ . '/../auth_check.php';


// Validate property selection
if (!isset($_POST['property_id'])) {
    echo "Missing property selection.";
    exit;
}

$property_id = $_POST['property_id'];
$_SESSION['property_id'] = $property_id;

// Fetch tenancy details
$stmt = $conn->prepare("
    SELECT t.Tenancy_ID, 
    t.occupation_date as Start_Date, 
    DATE_ADD(t.occupation_date, INTERVAL 1 YEAR) AS End_Date,
    t.monthly_rent as Rent_Amount,     
    t.termination_status as Status, 
    p.Plot_number, 
    l.name AS LandlordName
    FROM tenancy t
    INNER JOIN property p ON t.Property_ID = p.Property_ID
    INNER JOIN landlord l ON p.Landlord_ID = l.Landlord_ID
    WHERE t.Property_ID = ?
    ORDER BY t.Tenancy_ID DESC
    LIMIT 1
");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$tenancy = $result->fetch_assoc();
$stmt->close();
?>

<h2>Step 3: Confirm Tenancy & Record Payment</h2>

<?php if ($tenancy): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Tenancy Information</strong>
        </div>
        <div class="card-body">
            <p><strong>Tenancy_ID:</strong> <?= htmlspecialchars($tenancy['Tenancy_ID']?? '') ?></p>
            <p><strong>Landlord:</strong> <?= htmlspecialchars($tenancy['LandlordName']?? '') ?></p>
            <p><strong>Plot Number:</strong> <?= htmlspecialchars($tenancy['Plot_number']?? '') ?></p>
            <p><strong>Tenancy Period:</strong> <?= htmlspecialchars($tenancy['Start_Date']?? '') ?> to <?= htmlspecialchars($tenancy['End_Date']?? '') ?></p>
            <p><strong>Rent Amount:</strong> <?= htmlspecialchars($tenancy['Rent_Amount']?? '') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($tenancy['Status']?? '') ?></p>
        </div>
    </div>

    <h4>Record Payment</h4>
    <form method="POST" action="index.php?page=submit_payment" class="row g-3">
        <input type="hidden" name="tenancy_id" value="<?= $tenancy['Tenancy_ID'] ?>">
        <div class="col-md-6">
            <label for="amount" class="form-label">Payment Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="receipt" class="form-label">Receipt Number</label>
            <input type="text" name="receipt" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Approval Status</label>
            <select name="status" class="form-select" required>
                <option value="Approved">Approved</option>
                <option value="Pending">Pending</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Submit Payment</button>
        </div>
    </form>
<?php else: ?>
    <div class="alert alert-warning">No tenancy found for the selected property.</div>
<?php endif; ?>
