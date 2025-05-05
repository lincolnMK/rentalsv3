<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=occupants_report_" . date("Y-m-d") . ".xls");

    echo "Name\tContact\tEmail\tMDA\tUsed For\tDistrict\tPlot Number\n";

    $sql = "SELECT o.Name, o.contact, o.email, m.MDA_name, t.Used_for, p.District, p.plot_number
            FROM occupants o
            LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
            LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
            LEFT JOIN Property p ON p.Property_ID = t.Property_ID";

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Name']}\t{$row['contact']}\t{$row['email']}\t{$row['MDA_name']}\t{$row['Used_for']}\t{$row['District']}\t{$row['plot_number']}\n";
    }
    exit;
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';

$count_sql = "SELECT count(*) AS total FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID =t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
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

$sql = "SELECT o.Occupant_ID, o.Name, o.contact, o.email, m.MDA_name, t.Used_for, p.District, p.plot_number
    FROM occupants o
    LEFT JOIN MDA m ON m.MDA_ID = o.MDA_ID
    LEFT JOIN Tenancy t ON o.Occupant_ID = t.Occupant_ID
    LEFT JOIN Property p ON p.Property_ID = t.Property_ID
    LEFT JOIN Property_types pt ON p.Property_type_ID = pt.Property_type_ID
    LEFT JOIN landlord l ON l.Landlord_ID = p.Landlord_ID
    WHERE 1";

if (!empty($search)) {
    $sql .= " AND (p.Plot_number LIKE ? OR o.Name LIKE ? OR p.District LIKE ? OR o.Occupant_ID LIKE ? OR o.Contact LIKE ? OR o.Email LIKE ? OR m.MDA_name LIKE ?)";
}
$sql .= " LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bind_param("ssssssssi", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();

$occupants = [];
while ($row = $result->fetch_assoc()) {
    $occupants[] = $row;
}
$total_pages = ceil($total_results / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Occupants Report</title>
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
    <h2>Occupants registered as at: <?= date("F d, Y") ?></h2>

    <div class="btn-group">
        <a href="?export=excel">Export to Excel</a>
        <button onclick="window.print()">Print</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>MDA</th>
                <th>Used For</th>
                <th>District</th>
                <th>Plot Number</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($occupants) > 0): ?>
                <?php $i = $start + 1; ?>
                <?php foreach ($occupants as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['Name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['contact'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['MDA_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Used_for'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['District'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['plot_number'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No occupants found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
