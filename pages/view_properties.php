
<?php
// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';
$search = $_GET['search'] ?? '';

// Base SQL query without pagination for counting rows
$count_sql = "SELECT COUNT(*) AS total FROM Property p
    LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN Tenancy t ON p.Property_ID = t.Property_ID
    LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
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
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) {
    $start = 0;
}

// Query to fetch results with pagination
$sql = "SELECT 
    p.Landlord_ID,  
    l.Name, 
    p.Property_ID,
    p.Plot_number, 
    o.Name as Occupant,
    t.Used_for,
    p.District, 
    p.Location, 
    p.Area,
    p.Description,
    o.Occupant_ID
    FROM Property p
    LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN Tenancy t ON p.Property_ID = t.Property_id
    LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
    WHERE 1";

if (!empty($search)) {
    $sql .= " AND (l.Name LIKE ? OR p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR p.Location LIKE ?)";
}

$sql .= " LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("ssssssi", $search_param, $search_param, $search_param, $search_param, $search_param, $start, $limit);
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





   
                          <!-- Main Content Area -->
    
                        <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Property List</h4>
                </div>
            </div>

        <div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
               

                                    <!-- Search Form -->
                                    <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">

    <div class="input-group me-3">
        <input type="hidden" name="page" value="property">
        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Search by Landlord, Plot Number, Occupant, District, or Location" 
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=view_properties" class="btn btn-secondary">Clear</a>
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


                <table class="table table-bordered table-striped  ">
                    <thead class="table-dark">
                        <tr>
                            <th>Property#</th>
                            <th>Landlord Name</th>
                            <th>Occupant</th>
                            <th>Type/Used as</th>
                            <th>Plot Number</th>
                            <th>District</th>
                            <th>Location</th>
                            <th>Area(sq.m)</th>
                            <th>Description</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td>
                                    <a href="index.php?page=property_details&Property_ID=<?= $property['Property_ID']; ?>">
                                        <?= htmlspecialchars($property['Property_ID']?? ''); ?>
                                    </a>
                                </td>
                                <td>
                                <a href="index.php?page=landlord_details&Landlord_ID=<?= $property['Landlord_ID']; ?>">
                                <?= htmlspecialchars($property['Name'] ?? 'Unknown'); ?>
                                </a>
                            
                            </td>
                            <td>
                                <a href="index.php?page=occupant_details&Occupant_ID=<?= $property['Occupant_ID']; ?>">
                                    <?= htmlspecialchars($property['Occupant']?? 'unkown'); ?>
                                </a>
                            </td>
                                <td><?= htmlspecialchars($property['Used_for']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Plot_number']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['District']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Location']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Area']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Description']?? ''); ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                 <nav>
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=property&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=property&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=property&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


            </div>
        </div>
    </div>
</div>

  
       