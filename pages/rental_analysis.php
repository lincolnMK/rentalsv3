
<?php
include_once __DIR__ . '/../auth_check.php';

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


// Pagination Setup
$limit = 50;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) {
    $start = 0;
}

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
        <input type="hidden" name="page" value="rental_analysis">

        <input type="text" name="search" class="form-control" placeholder="Search by Landlord, Plot Number, Occupant, District, or Location" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=rental_analysis" class="btn btn-secondary">Clear</a>
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
         <th><input type="checkbox" id="selectAll"></th> <!-- Master checkbox -->
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
                    <a href='index.php?page=rental_analysis&?$queryParams' class='text-white text-decoration-none'>
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
                <td>
                        <input type="checkbox" class="row-checkbox" value="<?= htmlspecialchars(json_encode($property)); ?>">
                </td>
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
                 <nav>
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=rental_analysis&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=rental_analysis&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=rental_analysis&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>




            </div>
        </div>
    </div>
</div>

   <script>
function getSelectedData() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    const data = [];

    selected.forEach(checkbox => {
        data.push(JSON.parse(checkbox.value));
    });

    return data;
}

function exportToExcel() {
    const selectedData = getSelectedData();
    if (selectedData.length === 0) {
        alert("Please select at least one row to export.");
        return;
    }

    // Generate CSV
    let csv = '';
    const headers = Object.keys(selectedData[0]);
    csv += headers.join(',') + '\n';

    selectedData.forEach(row => {
        csv += headers.map(h => `"${(row[h] ?? '').toString().replace(/"/g, '""')}"`).join(',') + '\n';
    });

    // Download as CSV file
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "selected_properties.csv";
    link.click();
}

function exportToPDF() {
    const selectedData = getSelectedData();
    if (selectedData.length === 0) {
        alert("Please select at least one row to export.");
        return;
    }

    const doc = new jsPDF();
    const headers = Object.keys(selectedData[0]);

    const rows = selectedData.map(row => headers.map(h => row[h] ?? ''));

    doc.autoTable({
        head: [headers],
        body: rows
    });

    doc.save("selected_properties.pdf");
}

// Select/Deselect all checkboxes
document.getElementById('selectAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
