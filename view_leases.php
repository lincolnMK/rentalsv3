<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username and profile picture from the session
$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';

// Base SQL query without pagination for counting rows
$lease_count_query = "SELECT COUNT(*) AS total_leases FROM LEASE WHERE 1";

// Modify query if search is applied
if (!empty($search)) {
    $lease_count_query .= " AND (Lease_ID LIKE ? OR Lease_type LIKE ?)";
}

// Prepare and execute total count query
$stmt = $conn->prepare($lease_count_query);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("is", $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_leases = $row['total_leases'];

$stmt->close();

// Pagination Setup

$limit = 50; // Number of entries to show per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


$start = ($page - 1) * $limit;

// Ensure that $start and $limit are integers
$start = (int) $start;
$limit = (int) $limit;

// Query to fetch results with pagination
$sql = "SELECT 
Lease_id, 
Lease_type, 
Lease_duration, 
Lease_start, 
Property_ID, 
Lease_status 
FROM LEASE WHERE 1";

if (!empty($search)) {
    $sql .= " AND (Lease_id LIKE ? OR Lease_type LIKE ?)";
}
// Add pagination
$sql .= " LIMIT ?, ?";

// Prepare and execute the lease query
$stmt = $conn->prepare($sql);
if (!empty($search)) {
    // Bind parameters with appropriate types (ssii: two strings for search and two integers for pagination)
    $stmt->bind_param("isii", $search_param, $search_param, $start, $limit);
} else {
    // Bind parameters for pagination only
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

$leases = [];
// Check if any leases were found

 while ($row = $result->fetch_assoc()) {
        $leases[] = $row;
    }
 

// Prevent the page number from exceeding the total number of pages
$total_pages = ceil($total_leases / $limit);
//$page = min($page, $total_pages);


$stmt->close();
$conn->close();

// Handling empty results in the view
if (empty($leases)) {
    $message = "No leases found matching your search criteria.";
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Leases</title>
    <!-- Load Bootstrap from CDN -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid">

    <!-- Top Bar -->
    <?php
include('assets/templates/topbar.php');
?>

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
                        <a class="nav-link" href="add_lease.php">
                            <i class="fas fa-home"></i> Add Lease
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-10 bg-light">
          

            <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Leases List</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title">Leases</h5>


                        <!-- Search Form -->
                        <form method="GET" action="" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="text" name="search" class="form-control" placeholder="Search by lease type or lease id" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="view_leases.php" class="btn btn-secondary">Clear</a>
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
<?php if (isset($message)) : ?>
    <div class="alert alert-warning" role="alert">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Display Search Result Count -->
<p class="text-muted">
    Showing <strong><?= $total_leases; ?></strong> Leases in this list

</p>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Lease ID</th>
            <th>Lease Type</th>
            <th>Lease Duration</th>
            <th>Lease Start</th>
            <th>Property ID</th>
            <th>Lease Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($leases as $lease): ?>
            <tr>
                <td><?= htmlspecialchars($lease['Lease_id']); ?></td>
                <td><?= htmlspecialchars($lease['Lease_type']); ?></td>
                <td><?= htmlspecialchars($lease['Lease_duration']); ?></td>
                <td><?= htmlspecialchars($lease['Lease_start']); ?></td>
                <td><?= htmlspecialchars($lease['Property_ID']); ?></td>
                <td><?= htmlspecialchars($lease['Lease_status']?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


                            <!-- Pagination -->
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
<?php
include('assets/templates/footer.php');
?>
</html>
