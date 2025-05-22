<?php 
include_once __DIR__ . '/../auth_check.php';

// Initialize search input
$search = $_GET['search'] ?? '';

// Total count query
$count_sql = "SELECT COUNT(*) AS total 
FROM payments p
LEFT JOIN tenancy t ON p.Tenancy_ID = t.Tenancy_ID
LEFT JOIN property k ON t.Property_ID = k.Property_ID
LEFT JOIN landlord l ON k.Landlord_ID = l.Landlord_ID
LEFT JOIN occupants o ON t.OCCUPANT_ID = o.OCCUPANT_ID
WHERE 1";

if (!empty($search)) {
    $count_sql .= " AND (l.Name LIKE ? OR k.Plot_number LIKE ? OR o.Name LIKE ? OR k.District LIKE ? OR k.Location LIKE ?)";
}

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

/// Pagination Setup
$limit = 50;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
// Ensure start is not negative
if ($start < 0) {
    $start = 0;
}

// Query with pagination
$sql = "SELECT 
    p.Payment_id,
    p.Tenancy_id,
    l.Name AS landlord,
    o.Name AS occupant,
    k.Plot_number AS plot,
    k.District,
    k.Location,
    p.Payment_date,
    t.Monthly_rent AS rate,
    (t.Monthly_rent * 12) AS gross,
    p.Receipt_landlord,
    p.VAT,
    p.amount
FROM payments p
LEFT JOIN tenancy t ON p.Tenancy_ID = t.Tenancy_ID
LEFT JOIN property k ON t.Property_ID = k.Property_ID
LEFT JOIN landlord l ON k.Landlord_ID = l.Landlord_ID
LEFT JOIN occupants o ON t.OCCUPANT_ID = o.OCCUPANT_ID
WHERE 1";

if (!empty($search)) {
    $sql .= " AND (l.Name LIKE ? OR k.Plot_number LIKE ? OR o.Name LIKE ? OR k.District LIKE ? OR k.Location LIKE ?)";
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

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

$total_pages = ceil($total_results / $limit);
?>


                <!-- Topbar (Inside Main Content) -->
                <div class="row bg-white py-3 shadow-sm">
                    <div class="col">
                        <h4 class="text-primary">Payments</h4>
                        
                    </div>
                </div>
  <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                       
                       
                       <!-- Search Form -->
     <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">
    <div class="input-group me-3">
        <input type="hidden" name="page" value="payments">
        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, occupant, or plot number" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=payments" class="btn btn-secondary">Clear</a>
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

<p class="text-muted">
    Showing <strong><?= $total_results; ?></strong> Payments in this list

</p>

                        
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Tenancy ID</th>
                                        <th>Landlord</th>
                                        <th>Occupant</th>
                                        <th>Plot Number</th>
                                        <th>Payment Date</th>
                                        <th>Monthly Rent</th>
                                        <th>Gross (Year)</th>
                                        <th>Receipt Landlord</th>
                                        <th>VAT</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No payments found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($payment['Payment_id']) ?></td>
                                                <td> <?= htmlspecialchars($payment['Tenancy_id']) ?></td>
                                                <td><?= htmlspecialchars($payment['landlord']) ?></td>
                                                <td><?= htmlspecialchars($payment['occupant']) ?></td>
                                                <td><?= htmlspecialchars($payment['plot']) ?></td>
                                                <td><?= htmlspecialchars($payment['Payment_date']) ?></td>
                                                <td><?= number_format($payment['rate'], 2) ?></td>
                                                <td><?= number_format($payment['gross'], 2) ?></td>
                                                <td><?= htmlspecialchars($payment['Receipt_landlord']) ?></td>
                                                <td><?= number_format($payment['VAT'], 2) ?></td>
                                                <td><?= number_format($payment['amount'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        
                
                <!-- Pagination -->
                  <nav>
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=payments&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=payments&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=payments&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

                        </div>
                    </div>
                </div>
            </div>
        
    