
<?php

// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

$search = $_GET['search'] ?? '';

// number of leases 
//$leases= "SELECT COUNT(*) as No_of_lease FROM landlord l left join  property p on l.Landlord_ID = p.Landlord_ID left join tenancy t on p.Property_ID = t.Property_ID where l.Landlord_ID = Landlord_ID";

// Base SQL query without pagination for counting rows
$count_sql = "SELECT COUNT(*) as total FROM landlord WHERE 1"; // Ensures WHERE clause works dynamically

// Modify query if search is applied
if (!empty($search)) {
    $count_sql .= " AND (Phone_Number LIKE ? OR National_ID LIKE ? OR Vendor_code LIKE ? OR Name LIKE ? OR Address LIKE ?)";
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
$sql = "SELECT Landlord_ID, Name, Phone_Number, National_ID, Vendor_code, Address FROM landlord WHERE 1"; // Ensure valid SQL structure

if (!empty($search)) {
    $sql .= " AND (Phone_Number LIKE ? OR National_ID LIKE ? OR Vendor_code LIKE ? OR Name LIKE ? OR Address LIKE ?)";
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
                    <h4 class="text-primary">Landlord List</h4>
                </div>
            </div>

        <div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                

                                    <!-- Search Form -->
                                    <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
    <input type="hidden" name="page" value="view_landlords">
        <input 
        type="text" 
        name="search" 
        class="form-control" 
        placeholder="Search by Landlord, national idt, or phone" 
        value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=view_landlords" class="btn btn-secondary">Clear</a>
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
    Showing <strong><?= $total_results; ?></strong> Landlords in this list

</p>


                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name Of Landlord</th>
                            <th>Contact</th>
                            <th>National ID</th>
                            <th>Vendor Code</th>
                            <th>Address</th>
                            <th># of Property(s)</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td>
                                    <a href="index.php?page=landlord_details&Landlord_ID=<?= $property['Landlord_ID']; ?>">
                                        <?= htmlspecialchars($property['Landlord_ID']?? ''); ?>
                                    </a>
                                </td>
                               
                           
                                <td>
                                <a href="index.php?page=landlord_details&Landlord_ID=<?= $property['Landlord_ID']; ?>">
                                    <?= htmlspecialchars($property['Name']?? ''); ?>
                                 </a>
                                </td>
                                
                                <td><?= htmlspecialchars($property['Phone_Number']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['National_ID']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Vendor_code']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Address']?? ''); ?></td>
                                <td><?= htmlspecialchars($property['Lease']?? ''); ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
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

      
