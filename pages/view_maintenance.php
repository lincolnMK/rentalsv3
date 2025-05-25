<?php

include_once __DIR__ . '/../auth_check.php';

$search = $_GET['search'] ?? '';

// Base SQL to count total records
$count_sql = "SELECT count(*) AS total FROM maintenance m
LEFT JOIN property p ON p.Property_ID = m.Property_ID
LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
LEFT JOIN tenancy t ON t.Tenancy_ID = m.Tenancy_ID
LEFT JOIN occupants o ON o.Occupant_ID = t.Occupant_ID
LEFT JOIN users u_req ON u_req.User_ID = m.Requested_By
LEFT JOIN users u_cr ON u_cr.User_ID = m.Created_By
LEFT JOIN users u_ap ON u_ap.User_ID = m.Approved_By
LEFT JOIN users u_as ON u_as.User_ID = m.Assigned_To
WHERE 1";

if (!empty($search)) {
    $count_sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR l.Name LIKE ? OR m.Status LIKE ?)";
}

$stmt = $conn->prepare($count_sql);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_results = $row['total'];
$stmt->close();

// Pagination
$limit = 50;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) $start = 0;

// Main data query
$sql = "SELECT 
    m.Maintenance_ID,
    p.Plot_number,
    l.Name AS Landlord_Name,
    o.Name AS Occupant_Name,
    m.Request_Title,
    m.Request_Date,
    m.Status,
    CONCAT(u_req.First_Name, ' ', u_req.Last_Name) AS Requested_By_Name,
    CONCAT(u_cr.First_Name, ' ', u_cr.Last_Name) AS Created_By_Name,
    CONCAT(u_ap.First_Name, ' ', u_ap.Last_Name) AS Approved_By_Name,
    CONCAT(u_as.First_Name, ' ', u_as.Last_Name) AS Assigned_To_Name,
    m.Estimated_Cost,
    m.Updated_At
FROM maintenance m
LEFT JOIN property p ON p.Property_ID = m.Property_ID
LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
LEFT JOIN tenancy t ON t.Tenancy_ID = m.Tenancy_ID
LEFT JOIN occupants o ON o.Occupant_ID = t.Occupant_ID
LEFT JOIN users u_req ON u_req.User_ID = m.Requested_By
LEFT JOIN users u_cr ON u_cr.User_ID = m.Created_By
LEFT JOIN users u_ap ON u_ap.User_ID = m.Approved_By
LEFT JOIN users u_as ON u_as.User_ID = m.Assigned_To
WHERE 1";

if (!empty($search)) {
    $sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR l.Name LIKE ? OR m.Status LIKE ?)";
}

$sql .= " ORDER BY m.Request_Date DESC LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

$maintenance_records = [];
while ($row = $result->fetch_assoc()) {
    $maintenance_records[] = $row;
}
$total_pages = ceil($total_results / $limit);

?>


<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Maintenance Records</h4>
    </div>
</div>
<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
              
                
<!-- Search Form -->

<form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
                <div class="input-group me-3">
                    <input type="hidden" name="page" value="maintenance">
                    <input type="text" class="form-control" name="search" placeholder="Search by Plot Number, Occupant Name, Landlord Name, or Status" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <a href="index.php?page=maintenance" class="btn btn-secondary">Clear</a>
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

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Maintenance ID</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date Reported</th>
                            <th>Occupant Name</th>
                            <th>Plot Number</th>
                            <th>District</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($maintenance_records)): ?>
                            <tr>
                                            <td colspan="10" class="text-center">No Maintenance records found.</td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($maintenance_records as $maintenance_record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($maintenance_record['Maintenance_ID']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['Request_Title']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['Status']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['Request_Date']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['Occupant_Name']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['Plot_number']) ?></td>
                                    <td><?= htmlspecialchars($maintenance_record['District']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                   

                 <nav>
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=maintenance&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=maintenance&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=maintenance&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

            </div>
        </div>
    </div>
</div>

<?php $stmt->close();?>

<script>
    function exportToExcel() {
        window.location.href = 'export_excel.php?search=' + encodeURIComponent('<?= $search ?>');
    }

    function exportToPDF() {
        window.location.href = 'export_pdf.php?search=' + encodeURIComponent('<?= $search ?>');
    }