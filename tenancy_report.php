<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$search = $_GET['search'] ?? '';
$export = $_GET['export'] ?? '';

// SQL base
$sql = "SELECT 
    t.tenancy_ID,
    t.used_for,
    o.Name AS occupant_name,
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

$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR t.Lease_ID LIKE ? OR t.used_for LIKE ? OR p.location LIKE ?)";
    $search_param = "%{$search}%";
    $params = array_fill(0, 6, $search_param);
    $types = str_repeat('s', 6);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($export === 'excel') {
    // Export as CSV
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=Tenancy_Report_" . date('Ymd_His') . ".csv");

    $output = fopen("php://output", "w");
    fputcsv($output, [
        'Tenancy ID', 'Occupant', 'Lease ID', 'Used For', 'Monthly Rent', 'First Rent', 
        'Occupation Date', 'Termination Status', 'Plot Number', 'District', 'Location'
    ]);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['tenancy_ID'],
            $row['occupant_name'],
            $row['Lease_ID'],
            $row['used_for'],
            $row['Monthly_rent'],
            $row['rent_first_occupation'],
            $row['occupation_date'],
            $row['termination_status'],
            $row['plot_number'],
            $row['District'],
            $row['location']
        ]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tenancy Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .header div { font-size: 14px; }
        .buttons { display: flex; justify-content: space-between; margin-bottom: 20px; }
        button { padding: 8px 16px; }
    </style>
</head>
<body>
    <div class="header">
        <div><strong>Prepared by:</strong> <?= htmlspecialchars($username) ?></div>
        <div><strong>Date:</strong> <?= date('Y-m-d H:i:s') ?></div>
    </div>

    <div class="buttons">
        <form method="get" action="tenancy_report.php" target="_blank">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="export" value="excel">
            <button type="submit">Export to Excel</button>
        </form>
        <button onclick="window.print()">Print Report</button>
    </div>

    <h2>Tenancy Report</h2>

    <table>
        <thead>
            <tr>
                <th>Tenancy ID</th>
                <th>Occupant</th>
                <th>Lease ID</th>
                <th>Used For</th>
                <th>Monthly Rent</th>
                <th>First Rent</th>
                <th>Occupation Date</th>
                <th>Termination Status</th>
                <th>Plot Number</th>
                <th>District</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['tenancy_ID']?? '') ?></td>
                <td><?= htmlspecialchars($row['occupant_name']?? '') ?></td>
                <td><?= htmlspecialchars($row['Lease_ID']?? '') ?></td>
                <td><?= htmlspecialchars($row['used_for']?? '') ?></td>
                <td><?= htmlspecialchars($row['Monthly_rent']?? '') ?></td>
                <td><?= htmlspecialchars($row['rent_first_occupation']?? '') ?></td>
                <td><?= htmlspecialchars($row['occupation_date']?? '') ?></td>
                <td><?= htmlspecialchars($row['termination_status']?? '') ?></td>
                <td><?= htmlspecialchars($row['plot_number']?? '') ?></td>
                <td><?= htmlspecialchars($row['District']?? '') ?></td>
                <td><?= htmlspecialchars($row['location']?? '') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
