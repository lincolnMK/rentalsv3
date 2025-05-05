<?php

// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

// Query for total number of properties
$paidquery1 = "SELECT COUNT(*) AS number_of_properties FROM property;";
$stmt = $conn->prepare($paidquery1);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$number_of_properties = $row['number_of_properties'];
$stmt->close();

// Total payments due
$total_balance_due = 0;

// Total approved paid payments
$total_approved_paid_payments_this_month = 40;

?>

<!-- Topbar (Inside Main Content) -->
<div class="row bg-white py-3 shadow-sm mb-3">
    <div class="col">
        <h4 class="text-primary">Dashboard</h4>
    </div>
</div>

<!-- Dashboard Content -->
<div class="container-fluid px-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-primary shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Properties</h5>
                    <p class="card-text display-6 fw-semibold"><?= $number_of_properties; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-success">Approved & Paid Rentals</h5>
                    <p class="card-text display-6 fw-semibold"><?= $total_approved_paid_payments_this_month; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-warning">Total Payments Due</h5>
                    <p class="card-text display-6 fw-semibold">MWK <?= $total_balance_due ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
