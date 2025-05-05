<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=landlords_report_" . date("Y-m-d") . ".xls");

    echo "Name\tPhone\tNational ID\tVendor Code\tAddress\n";

    $sql = "SELECT Name, Phone_Number, National_ID, Vendor_code, Address FROM landlord";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "{$row['Name']}\t{$row['Phone_Number']}\t{$row['National_ID']}\t{$row['Vendor_code']}\t{$row['Address']}\n";
    }
    exit;
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';

$count_sql = "SELECT COUNT(*) as total FROM landlord WHERE 1";
if (!empty($search)) {
    $count_sql .= " AND (Phone_Number LIKE ? OR National_ID LIKE ? OR Vendor_code LIKE ? OR Name LIKE ? OR Address LIKE ?)";
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

$sql = "SELECT Landlord_ID, Name, Phone_Number, National_ID, Vendor_code, Address FROM landlord WHERE 1";
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

$landlords = [];
while ($row = $result->fetch_assoc()) {
    $landlords[] = $row;
}
$total_pages = ceil($total_results / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Landlords Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; }
        h2 { margin-bottom: 10px; }
        .btn-group { margin-bottom: 15px; }
        .btn-group button, .btn-group a {
            padding: 6px 12px;
            font-size: 13px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            margin-right: 5px;
            cursor: pointer;
        }
        .btn-group a { display: inline-block; }
        .btn-group button:hover, .btn-group a:hover { background-color: #0056b3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #cccccc;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        @media print {
            .btn-group { display: none; }
        }
    </style>
</head>
<body>
    <h2>Landlords registered as at: <?= date("F d, Y") ?></h2>

    <div class="btn-group">
        <a href="?export=excel">Export to Excel</a>
        <button onclick="window.print()">Print</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>National ID</th>
                <th>Vendor Code</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($landlords) > 0): ?>
                <?php $i = $start + 1; ?>
                <?php foreach ($landlords as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['Name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Phone_Number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['National_ID'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Vendor_code'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Address'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No landlords found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
