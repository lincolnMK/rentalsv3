
<?php


// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

$search = $_GET['search'] ?? '';

// Base SQL query without pagination for counting rows
$count_sql = "SELECT count(*) AS total FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID =t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
    WHERE 1"; // Ensures WHERE clause works dynamically

// Modify query if search is applied
if (!empty($search)) {
    $count_sql .= " AND (l.Name LIKE ? OR p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR p.Location LIKE ?)";
}


// Prepare and execute total count query
$stmt = $conn->prepare($count_sql);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_results = $row['total'];

$stmt->close();

// Pagination Setup
$limit = 50;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query to fetch results with pagination
$sql = "SELECT 
    o.Occupant_ID,
    o.Name,
    o.contact,
    o.email,
    m.MDA_name,
    t.Used_for,
    p.District,
    p.plot_number
    FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID =t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
    WHERE 1";

// Add search condition if search is applied
if (!empty($search)) {
    $sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR o.Occupant_ID LIKE ? OR o.Contact LIKE ? OR o.email LIKE ? OR m.MDA_name LIKE ?)";
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

    // Bind 7 parameters for the search and 2 for pagination
    $stmt->bind_param("ssssssssi", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    // Bind only 2 parameters for pagination
    $stmt->bind_param("ii", $start, $limit);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$occupants = [];
while ($row = $result->fetch_assoc()) {
    $occupants[] = $row;
}

$total_pages = ceil($total_results / $limit);




?>





                        <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Occupants List</h4>
                </div>
            </div>

        <div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
               

                                    <!-- Search Form -->
                                    <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="hidden" name="page" value="occupants">
        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, national idt, or phone" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=occupants" class="btn btn-secondary">Clear</a>
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
    Showing <strong><?= $total_results; ?></strong> Occupants in this list

</p>

<!-- Table -->
<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>MDA Name</th>
            <th>Property USE</th>
            <th>District</th>
            <th>Plot Number</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($occupants) > 0): ?>
            <?php foreach ($occupants as $occupant): ?>
                <tr>
                    <td>
                    <a href="index.php?page=occupant_details&Occupant_ID=<?= $occupant['Occupant_ID']; ?>">
                        <?= htmlspecialchars($occupant["Occupant_ID"]?? '') ?>
            </a>
                    </td>
                    <td>
                    <a href="index.php?page=occupant_details&Occupant_ID=<?= $occupant['Occupant_ID']; ?>">    
                    <?= htmlspecialchars($occupant["Name"]?? '') ?>
            </a>
                </td>
                    <td><?= htmlspecialchars($occupant["contact"]?? '') ?></td>
                    <td><?= htmlspecialchars($occupant["email"]?? '') ?></td>
                    <td><?= htmlspecialchars($occupant["MDA_name"]?? '') ?></td>
                    <td><?= htmlspecialchars($occupant["Used_for"]?? '') ?></td>
                    <td><?= htmlspecialchars($occupant["District"]?? '') ?></td>
                    <td><?= htmlspecialchars($occupant["plot_number"]?? '') ?></td>
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

       