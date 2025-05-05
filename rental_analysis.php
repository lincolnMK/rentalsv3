
<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

// Database connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture search, sort, and order parameters
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'Plot_number'; // Default sorting column
$order = $_GET['order'] ?? 'asc'; // Default sorting order

// Validate sorting column to prevent SQL injection
$valid_columns = [
    'Plot_number', 'Landlord', 'District', 'Location', 
    'Description', 'Used_for', 'RCA', 'Monthly_rent', 'Lease_start', 'rent_squaremeter'
];

if (!in_array($sort, $valid_columns)) {
    $sort = 'Plot_number'; // Default to Plot Number if invalid
}

// Validate order (asc/desc)
$order = ($order === 'desc') ? 'desc' : 'asc';

// Count query (no sorting needed)
$count_sql = "SELECT COUNT(*) AS total FROM property p
    LEFT JOIN landlord l ON p.Landlord_ID = l.Landlord_ID
    LEFT JOIN tenancy t ON p.Property_ID = t.Property_ID
    LEFT JOIN lease le ON le.Lease_ID = t.Lease_ID
    LEFT JOIN occupants o ON o.Occupant_ID = t.Occupant_ID
    WHERE 1";

if (!empty($search)) {
    $count_sql .= " AND (l.Name LIKE ? OR p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR p.Location LIKE ? OR t.Used_for LIKE ?)";
}

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

// Pagination setup
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Main query with sorting
$sql = "SELECT 
    p.Plot_number,
    l.Name AS Landlord,
    p.District,
    p.Location,
    p.Description,
    t.Used_for,
    RCA,
    t.Monthly_rent,
    le.Lease_start,
    p.Area / t.Monthly_rent AS rent_squaremeter,
    o.Name AS Occupant,
    t.Property_ID,
    p.Landlord_ID,
    o.Occupant_ID
FROM property p
LEFT JOIN landlord l ON p.Landlord_ID = l.Landlord_ID
LEFT JOIN tenancy t ON p.Property_ID = t.Property_ID
LEFT JOIN lease le ON le.Lease_ID = t.Lease_ID
LEFT JOIN occupants o ON o.Occupant_ID = t.Occupant_ID
WHERE 1";

if (!empty($search)) {
    $sql .= " AND (l.Name LIKE ? OR p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR p.Location LIKE ? OR t.Used_for LIKE ?)";
}

$sql .= " ORDER BY $sort $order LIMIT ?, ?"; // Sorting logic

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("ssssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

$total_pages = ceil($total_results / $limit);

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Properties</title>

     <!-- Load Bootstrap from CDN -->
     <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">



    
</head>
<body>
<div class="container-fluid">
   <!-- Top Bar -->
<?php
include ('assets/templates/topbar.php'); 
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
                        <a class="nav-link" href="view_reports.php">
                            <i class="fas fa-money-bill-wave"></i> Reports
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
                    <h4 class="text-primary">Rental Analysis</h4>
                </div>
            </div>

        <div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Properties</h5>

                                    <!-- Search Form -->
                                    <form method="GET" action="" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, Plot Number, Occupant, District, or Location" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="rental_analysis.php" class="btn btn-secondary">Clear</a>
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
    Showing <strong><?= $total_results; ?></strong> Properties in this list

</p>


<table class="table table-bordered">
<thead class="table-dark">
    <tr>
        <?php
        $columns = [
            'Plot_number' => 'PLot#',
            'Landlord' => 'Landlord',
            'District' => 'District',
            'Location' => 'Location',
            'Description' => 'Description',
            'Used_for' => 'Used_for',
            'RCA' => 'RCA',
            'Monthly_rent' => 'Rent/Month',
            'Lease_start' => 'Lease Start',
            'rent_squaremeter' => 'Rent/SQM'
        ];

        $sort = $_GET['sort'] ?? ''; // Current sorting column
        $order = $_GET['order'] ?? ''; // Current sorting order
        $search = $_GET['search'] ?? ''; // Preserve search query

        foreach ($columns as $col => $label) {
            $new_order = ($sort === $col && $order === 'asc') ? 'desc' : 'asc';

            // Bootstrap icon for sorting direction
            $icon = '';
            if ($sort === $col) {
                $icon = $order === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            } else {
                $icon = ''; // Default icon (unsorted state)
            }

            // Preserve search query in the sorting links
            $queryParams = http_build_query([
                'search' => $search,
                'sort' => $col,
                'order' => $new_order
            ]);

            echo "<th>
                    <a href='?$queryParams' class='text-white text-decoration-none'>
                        $label <i class='$icon'></i>
                    </a>
                  </th>";
        }
        ?>
    </tr>
</thead>


    <tbody>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><?= htmlspecialchars($property['Plot_number'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Landlord'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['District'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Location'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Description'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Used_for'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['RCA'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Monthly_rent'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['Lease_start'] ?? ''); ?></td>
                <td><?= htmlspecialchars($property['rent_squaremeter'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


                <!-- Pagination -->
                <!-- Pagination Links -->
<nav>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1; ?>&sort=<?= urlencode($sort); ?>&order=<?= urlencode($order); ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>&sort=<?= urlencode($sort); ?>&order=<?= urlencode($order); ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1; ?>&sort=<?= urlencode($sort); ?>&order=<?= urlencode($order); ?>">Next</a>
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
<?php
include ('assets/templates/footer.php'); 
?>
</html>
