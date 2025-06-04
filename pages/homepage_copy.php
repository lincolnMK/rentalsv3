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
 <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Rental Management Dashboard</h4>
                </div>
            </div>
  <div class="container-fluid p-4">
    
    <!-- Stats Row -->
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary">
          <div class="card-body">
            <h5 class="card-title">Total Properties</h5>
            <h3>24</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success">
          <div class="card-body">
            <h5 class="card-title">Occupied Units</h5>
            <h3>18</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning">
          <div class="card-body">
            <h5 class="card-title">Vacant Units</h5>
            <h3>6</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-danger">
          <div class="card-body">
            <h5 class="card-title">Maintenance Requests</h5>
            <h3>3</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Occupancy Distribution</div>
          <div class="card-body">
            <canvas id="occupancyChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Monthly Rent Paid</div>
          <div class="card-body">
            <canvas id="rentChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Reminders and Quick Actions -->
    <div class="row g-4 mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Reminders</div>
          <div class="card-body">
            <ul>
              <li>Leases expiring this month</li>
              <li>Properties needing maintenance</li>
              <li>Payments not approved</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Quick Actions</div>
          <div class="card-body d-grid gap-2">
            <button class="btn btn-outline-primary">Add New Property</button>
            <button class="btn btn-outline-success">Register New Tenant</button>
            <button class="btn btn-outline-warning">Log Payment</button>
            <button class="btn btn-outline-danger">Add Maintenance Request</button>
          </div>
        </div>
      </div>
    </div>



  <script>
    // Occupancy Chart
    const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
    new Chart(occupancyCtx, {
      type: 'pie',
      data: {
        labels: ['Occupied', 'Vacant'],
        datasets: [{
          data: [18, 6],
          backgroundColor: ['#198754', '#ffc107']
        }]
      }
    });

    // Rent Chart
    const rentCtx = document.getElementById('rentChart').getContext('2d');
    new Chart(rentCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Rent Collected (MWK)',
          data: [1500000, 1650000, 1800000, 1750000, 1900000, 2100000],
          fill: false,
          borderColor: '#0d6efd',
          tension: 0.1
        }]
      }
    });
  </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>