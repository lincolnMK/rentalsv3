<?php
include_once __DIR__ . '/../auth_check.php';

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$step = max(1, min(5, $step)); // clamp step to [1,5]

// Step 1: Search Landlord
if ($step == 1 && isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    $like = "%$keyword%";
    $stmt = $conn->prepare("
        SELECT
            l.name AS name,
            l.landlord_id AS id,
            l.Phone_Number,
            p.Property_ID,
            p.Plot_number,
            p.District
        FROM landlord l
        LEFT JOIN property p ON l.landlord_id = p.landlord_id
        WHERE l.name LIKE ? OR p.plot_number LIKE ?
    ");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $landlords = $result->fetch_all(MYSQLI_ASSOC);
}

// Step 1b: User selects landlord
if ($step == 1 && isset($_POST['select_landlord'])) {
    $_SESSION['landlord_id'] = $_POST['landlord_id'];

    $stmt = $conn->prepare("SELECT * FROM property WHERE landlord_id = ?");
    $stmt->bind_param("i", $_SESSION['landlord_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);

    $step = 2;
}

// Step 2: Select Property
if ($step == 2 && isset($_POST['property_id'])) {
    $_SESSION['property_id'] = $_POST['property_id'];

    $stmt = $conn->prepare("SELECT * FROM tenancy WHERE property_id = ?");
    $stmt->bind_param("i", $_POST['property_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $tenancy = $result->fetch_assoc();

    if ($tenancy) {
        $_SESSION['tenancy'] = $tenancy;
        $step = 3; // move to next step
    } else {
        echo "<div class='alert alert-warning'>❌ No tenancy record found for the selected property. Please ensure the property has an active tenant before proceeding.</div>";
        exit;
    }
}

// Re-fetch properties if user is on Step 2 and $properties is not set
if ($step == 2 && !isset($properties) && isset($_SESSION['landlord_id'])) {
    $stmt = $conn->prepare("SELECT * FROM property WHERE landlord_id = ?");
    $stmt->bind_param("i", $_SESSION['landlord_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);
}

// Step 3: Capture Payment Info
if ($step == 3 && isset($_POST['amount'])) {
    $_SESSION['payment'] = [
        'amount' => floatval($_POST['amount']),
        'from_month' => $_POST['from_month'],
        'to_month' => $_POST['to_month'],
        'status' => $_POST['status'],
        'receipt' => $_POST['receipt'] ?? 'N/A',
        'vat' => floatval($_POST['amount']) * (DEFAULT_VAT_PERCENT / 100)
    ];
    $step = 4;
}

// Step 4: Final Submission
if ($step == 5 && isset($_POST['confirm']) && isset($_SESSION['tenancy'], $_SESSION['payment'])) {
    if (!isset($_SESSION['tenancy']['id'])) {
        echo "<div class='alert alert-danger'>❌ Tenancy ID missing. Please start again.</div>";
        exit;
    }

    $tenancy_id = $_SESSION['tenancy']['id'];
    $payment_date = date('Y-m-d');
    $receipt_landlord = $_SESSION['payment']['receipt'];
    $vat = $_SESSION['payment']['vat'];
    $amount = $_SESSION['payment']['amount'];

    $stmt = $conn->prepare("INSERT INTO payments (Tenancy_ID, Payment_date, Receipt_Landlord, VAT, amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $tenancy_id, $payment_date, $receipt_landlord, $vat, $amount);
    $stmt->execute();

    session_destroy();
    echo "<h3>✅ Payment has been successfully recorded.</h3>";
    exit;
} elseif ($step == 5) {
    echo "<div class='alert alert-danger'>❌ Session data is missing or step skipped. Please restart the process.</div>";
}
?>

<h2>Tenancy Payment Form</h2>

<form method="POST" action="">
    <input type="hidden" name="step" value="<?= $step + 1 ?>">

    <?php if ($step == 1): ?>
        <div class="container mt-4">
            <h3 class="mb-4">Step 1: Search for Landlord</h3>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="keyword" class="form-control" placeholder="Enter landlord name or plot number" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                </div>
            </div>

            <?php if (!empty($landlords)): ?>
                <h5 class="mb-3">Search Results:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($landlords as $l): ?>
                            <tr>
                                <td><?= htmlspecialchars($l['name']) ?></td>
                                <td><?= htmlspecialchars($l['Phone_Number']) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="step" value="1">
                                        <input type="hidden" name="select_landlord" value="1">
                                        <input type="hidden" name="landlord_id" value="<?= $l['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Select</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($step == 2): ?>
        <h3>Step 2: Select Property</h3>
        <form method="POST">
            <input type="hidden" name="step" value="2">
            <?php foreach ($properties as $p): ?>
                <label>
                    <input type="radio" name="property_id" value="<?= $p['Property_ID'] ?>" required>
                    <?= htmlspecialchars($p['Plot_number']) ?> (District: <?= $p['District'] ?>)
                </label><br>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">Next</button>
        </form>

    <?php elseif ($step == 3): ?>
        <h3>Step 3: Payment Information</h3>
        <?php if ($_SESSION['tenancy']): ?>
            <p><strong>Tenant:</strong> <?= $_SESSION['tenancy']['Occupant'] ?></p>
            <p><strong>Status:</strong> <?= $_SESSION['tenancy']['Status'] ?> | Lease: <?= $_SESSION['tenancy']['Lease_status'] ?></p>
            <p><strong>Rent per Month:</strong> <?= $_SESSION['tenancy']['Rent_per_month'] ?></p>

            <label>Amount: <input type="number" name="amount" step="0.01" required></label><br>
            <label>From Month: <input type="month" name="from_month" required></label><br>
            <label>To Month: <input type="month" name="to_month" required></label><br>
            <label>Status:
                <select name="status" required>
                    <option value="">--Select--</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                </select>
            </label><br>
            <label>Receipt / Ref: <input type="text" name="receipt"></label><br>
            <button type="submit" class="btn btn-success mt-3">Next</button>
        <?php else: ?>
            <p>No tenancy data found.</p>
        <?php endif; ?>

    <?php elseif ($step == 4): ?>
        <h3>Step 4: Confirm Payment</h3>
        <p><strong>Tenant:</strong> <?= $_SESSION['tenancy']['Occupant'] ?></p>
        <p><strong>Amount:</strong> <?= number_format($_SESSION['payment']['amount'], 2) ?></p>
        <p><strong>VAT (<?= DEFAULT_VAT_PERCENT ?>%):</strong> <?= number_format($_SESSION['payment']['vat'], 2) ?></p>
        <p><strong>Period:</strong> <?= $_SESSION['payment']['from_month'] ?> → <?= $_SESSION['payment']['to_month'] ?></p>
        <p><strong>Status:</strong> <?= $_SESSION['payment']['status'] ?></p>
        <p><strong>Receipt:</strong> <?= $_SESSION['payment']['receipt'] ?></p>

        <form method="POST">
            <input type="hidden" name="step" value="5">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-primary">Confirm & Submit</button>
        </form>
    <?php endif; ?>
</form>
