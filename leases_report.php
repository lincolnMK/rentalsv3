


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
    Lease_id, 
    Lease_type, 
    Lease_duration, 
    Lease_start, 
    Property_ID, 
    Lease_status 
FROM LEASE 
WHERE 1";

$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (
        Lease_id LIKE ? OR 
        Lease_type LIKE ? OR 
        Lease_duration LIKE ? OR 
        Lease_start LIKE ? OR 
        Property_ID LIKE ? OR 
        Lease_status LIKE ?
    )";
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
    header("Content-Disposition: attachment; filename=Lease_Report_" . date('Ymd_His') . ".csv");

    $output = fopen("php://output", "w");
    fputcsv($output, [
        'Lease ID', 'Lease Type', 'Lease Duration', 'Lease Start', 'Property ID', 'Lease Status'
    ]);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['Lease_id'] ?? '',
            $row['Lease_type'] ?? '',
            $row['Lease_duration'] ?? '',
            $row['Lease_start'] ?? '',
            $row['Property_ID'] ?? '',
            $row['Lease_status'] ?? ''
        ]);
    }

    fclose($output);
    exit; // 
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Leases Report</title>
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
        <form method="get" action="leases_report.php" target="_blank">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="export" value="excel">
            <button type="submit">Export to Excel</button>
        </form>
        <button onclick="window.print()">Print Report</button>
    </div>

    <h2>Lease Report</h2>

    <table>
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
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Lease_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Lease_type'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Lease_duration'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Lease_start'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Property_ID'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Lease_status'] ?? '') ?></td>
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
