
<?php
// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

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
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) {
    $start = 0;
}
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
       

                                    <!-- Search Form -->
                                    <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="hidden" name="page" value="tenancy">
        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, national idt, or phone" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=tenancy" class="btn btn-secondary">Clear</a>
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
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=tenancy&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=tenancy&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=tenancy&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


            </div>
        </div>
    </div>
</div>

       