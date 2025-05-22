<?php


// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

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
$limit = 50;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) {
    $start = 0;
}

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
                       


                        <!-- Search Form -->
                        <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
    <input type="hidden" name="page" value="leases">
    <input type="text" name="search" class="form-control" placeholder="Search by lease type or lease id" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=leases" class="btn btn-secondary">Clear</a>
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
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=leases&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=leases&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=leases&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


                        </div>
                    </div>
                </div>
            </div>
        
    