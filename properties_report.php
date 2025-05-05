<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Excel export before any output
if (isset($_POST['export_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=property_report_" . date("Y-m-d") . ".xls");

    echo "<table border='1'>";
    echo "<tr><th>Landlord</th><th>Plot No</th><th>Occupant</th><th>Used For</th><th>District</th><th>Location</th><th>Area</th><th>Description</th></tr>";

    if (isset($_POST['marked_ids']) && is_array($_POST['marked_ids']) && count($_POST['marked_ids']) > 0) {
        // Export only marked records
        $marked_ids = $_POST['marked_ids'];
        $placeholders = implode(',', array_fill(0, count($marked_ids), '?'));
        $types = str_repeat('i', count($marked_ids));

        $sql = "SELECT 
                    l.Name, 
                    p.Plot_number, 
                    o.Name as Occupant,
                    t.Used_for,
                    p.District, 
                    p.Location, 
                    p.Area,
                    p.Description
                FROM Property p
                LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
                LEFT JOIN Tenancy t ON p.Property_ID = t.Property_id
                LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
                WHERE p.Property_ID IN ($placeholders)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$marked_ids);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Export all records
        $result = $conn->query("SELECT 
                    l.Name, 
                    p.Plot_number, 
                    o.Name as Occupant,
                    t.Used_for,
                    p.District, 
                    p.Location, 
                    p.Area,
                    p.Description
                FROM Property p
                LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
                LEFT JOIN Tenancy t ON p.Property_ID = t.Property_id
                LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID");
    }

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['Name']?? '') . "</td>
                <td>" . htmlspecialchars($row['Plot_number']?? '') . "</td>
                <td>" . htmlspecialchars($row['Occupant'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['Used_for']?? '') . "</td>
                <td>" . htmlspecialchars($row['District'])?? '' . "</td>
                <td>" . htmlspecialchars($row['Location']?? '') . "</td>
                <td>" . htmlspecialchars($row['Area']?? '') . "</td>
                <td>" . htmlspecialchars($row['Description']?? '') . "</td>
              </tr>";
    }

    echo "</table>";
    exit;
}

// Main page logic
$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

$search = $_GET['search'] ?? '';

$count_sql = "SELECT COUNT(*) AS total FROM Property p
    LEFT JOIN Landlord l ON p.Landlord_ID = l.Landlord_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN Tenancy t ON p.Property_ID = t.Property_ID
    LEFT JOIN Occupants o ON t.Occupant_ID = o.Occupant_ID
    WHERE 1";

if (!empty($search)) {
    $count_sql .= " AND (l.Name LIKE ? OR p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR p.Location LIKE ?)";
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

$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

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
    p.Description
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
$stmt->close();

$total_pages = ceil($total_results / $limit);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Report</title>

 <!-- Load Bootstrap from CDN -->
 <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>



    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 8px 16px; margin-right: 10px; border: none; cursor: pointer; }
        .btn-print { background-color: #28a745; color: white; }
        .btn-export { background-color: #007bff; color: white; }
        .no-print { margin-bottom: 15px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Top Bar -->
<?php
include('assets/templates/topbar.php');
?>
<main class="col-md-12 bg-light">

<h2>Properties registered as at: <?= date('F j, Y') ?></h2>

<form method="post" id="exportForm">
    <div class="no-print">
        <button type="button" class="btn btn-print" onclick="window.print()">Print Report</button>
        <button type="submit" name="export_excel" class="btn btn-export">Export to Excel</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mark</th>
                <th>Landlord</th>
                <th>Plot No</th>
                <th>Occupant</th>
                <th>Used For</th>
                <th>District</th>
                <th>Location</th>
                <th>Area</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($properties) > 0): ?>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><input type="checkbox" name="marked_ids[]" value="<?= $property['Property_ID'] ?>"></td>
                        <td><?= htmlspecialchars($property['Name']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Plot_number']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Occupant']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Used_for']?? '') ?></td>
                        <td><?= htmlspecialchars($property['District']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Location']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Area']?? '') ?></td>
                        <td><?= htmlspecialchars($property['Description']?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9">No records found.</td></tr>
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




</form>
        </div>
        </div>

</body>
<?php
include('assets/templates/footer.php');
?>
</html>
