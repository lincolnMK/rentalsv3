
<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';

// Base SQL query without pagination for counting rows
$count_sql = "SELECT count(*) AS total FROM TENANCY t
    LEFT JOIN Property p ON p.Property_ID = t.Property_ID
    LEFT JOIN occupants o ON t.occupant_id = o.occupant_id
    LEFT JOIN Lease l ON l.Lease_ID = t.Lease_ID
    WHERE 1"; // Ensures WHERE clause works dynamically

// Modify query if search is applied
if (!empty($search)) {
    $count_sql .= " AND ( p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR t.Lease_ID LIKE ? OR t.used_for LIKE ? OR p.location LIKE ?)";
}

// Prepare and execute total count query
$stmt = $conn->prepare($count_sql);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("ssssss", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_results = $row['total'];

$stmt->close();

// Pagination Setup
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query to fetch results with pagination
$sql = "SELECT 
    t.tenancy_ID,
    t.used_for,
    o.Name,
    t.Lease_ID,
    t.rent_first_occupation,
    t.Monthly_rent,
    t.occupation_date,
    t.termination_status,
    p.plot_number,
    p.District,
    p.location
    FROM TENANCY t
    LEFT JOIN Property p ON p.Property_ID = t.Property_ID
    LEFT JOIN occupants o ON t.occupant_id = o.occupant_id
   
    
    WHERE 1";

// Add search condition if search is applied
if (!empty($search)) {
    $sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR t.Lease_ID LIKE ? OR t.used_for LIKE ? OR p.location LIKE ?)";
}

$sql .= " LIMIT ?, ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}

// Bind parameters based on whether search is applied
if (!empty($search)) {
    // Set the search parameter with wildcard characters
    $search_param = "%{$search}%";

    // Bind **6 search parameters** and **2 pagination parameters** correctly
    $stmt->bind_param("ssssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    // Bind only 2 parameters for pagination
    $stmt->bind_param("ii", $start, $limit);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$tenancies = [];
while ($row = $result->fetch_assoc()) {
    $tenancies[] = $row;
}

$total_pages = ceil($total_results / $limit);
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tenancy</title>

     <!-- Load Bootstrap from CDN -->
     <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    
</head>
<body>
<div class="container-fluid">
   <!-- Top Bar -->
<?php include('assets/templates/topbar.php'); ?>
    <!-- Sidebar and Main Content Wrapper -->
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
            <div class="d-flex flex-column align-items-start py-3">
            <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                
            <h3 class="ms-3">Rental System</h3>
                <ul class="nav flex-column w-100 mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="homepage.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_tenancy.php">
                            <i class="fas fa-money-bill-wave"></i>Create Tenancy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="del_tenancy.php">
                            <i class="fas fa-money-bill-wave"></i> Terminate Tenancy
                        </a>
                    </li>
                    <!-- Add other nav items as needed -->
                </ul>
            </div>
        </nav>


                          <!-- Main Content Area -->
        <main class="col-md-10 bg-light">
                        <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Tenancy List</h4>
                </div>
            </div>

        <div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tenancies</h5>

                                    <!-- Search Form -->
                                    <form method="GET" action="" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, national idt, or phone" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="view_occupants.php" class="btn btn-secondary">Clear</a>
    </div>

    <div class="dropdown ms-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
            <li><a class="dropdown-item" href="#" onclick="exportToExcel()">Export to Excel</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportToPDF()">Export to PDF</a></li>
        </ul>
    </div>
</form>


<!-- Display Search Result Count -->
<p class="text-muted">
    Showing <strong><?= $total_results; ?></strong> Tenancies in this list

</p>

<!-- Table -->
<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
           
            <th>Tenancy ID</th>
            <th>Occupant Name</th>
            <th>Used_for</th>   
            <th>Lease ID</th>
            <th>Rent First Occupation</th>
            <th>Monthly Rent(MWK)</th>
            <th>Occupation Date</th>
            <th>Termination Status</th>
            <th>Plot Number</th>
            <th>District</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($tenancies) > 0): ?>
            <?php foreach ($tenancies as $row): ?>
                <tr>
                <td><?= htmlspecialchars($row['tenancy_ID']) ?></td>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['used_for']?? '') ?></td>
               
                <td><?= htmlspecialchars($row['Lease_ID']) ?></td>
                <td><?= htmlspecialchars($row['rent_first_occupation']) ?></td>
                <td><?= htmlspecialchars($row['Monthly_rent']) ?></td>
                <td><?= htmlspecialchars($row['occupation_date']) ?></td>
                <td><?= htmlspecialchars($row['termination_status']) ?></td>
                <td><?= htmlspecialchars($row['plot_number']) ?></td>
                <td><?= htmlspecialchars($row['District']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>



                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">No records found</td></tr>
        <?php endif; ?>
    </tbody>
</table>



              

                <!-- Pagination -->
                <nav>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


            </div>
        </div>
    </div>
</div>

        </main>

                </div>


</body>
<?php include('assets/templates/footer.php'); ?>
</html>
