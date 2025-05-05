<?php
session_start();
include('db_connection.php');

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query to fetch results with pagination
$sql = "SELECT 
    p.Landlord_ID,  
    l.Name, 
    p.Property_ID,
    p.Plot_number, 
    o.Name as Occupant,
    t.Usage,
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

// Search handling (if needed)
$search = $_GET['search'] ?? '';

// SQL Query and Pagination logic (same as previous code)


// Export to Excel
if (isset($_GET['export_excel'])) {
    require 'vendor/autoload.php'; // Ensure you have PhpSpreadsheet installed
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header row
    $sheet->setCellValue('A1', 'Landlord ID');
    $sheet->setCellValue('B1', 'Landlord Name');
    $sheet->setCellValue('C1', 'Property ID');
    $sheet->setCellValue('D1', 'Plot Number');
    $sheet->setCellValue('E1', 'Occupant');
    $sheet->setCellValue('F1', 'Usage');
    $sheet->setCellValue('G1', 'District');
    $sheet->setCellValue('H1', 'Location');
    $sheet->setCellValue('I1', 'Area');
    $sheet->setCellValue('J1', 'Description');

    $rowNum = 2; // Row index starts from 2
    foreach ($properties as $property) {
        $sheet->setCellValue('A' . $rowNum, $property['Landlord_ID']);
        $sheet->setCellValue('B' . $rowNum, $property['Name']);
        $sheet->setCellValue('C' . $rowNum, $property['Property_ID']);
        $sheet->setCellValue('D' . $rowNum, $property['Plot_number']);
        $sheet->setCellValue('E' . $rowNum, $property['Occupant']);
        $sheet->setCellValue('F' . $rowNum, $property['Usage']);
        $sheet->setCellValue('G' . $rowNum, $property['District']);
        $sheet->setCellValue('H' . $rowNum, $property['Location']);
        $sheet->setCellValue('I' . $rowNum, $property['Area']);
        $sheet->setCellValue('J' . $rowNum, $property['Description']);
        $rowNum++;
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = "properties_data.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}

// Export to PDF
if (isset($_GET['export_pdf'])) {
    require 'vendor/autoload.php'; // Ensure you have TCPDF installed
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Add header
    $pdf->Cell(40, 10, 'Landlord ID');
    $pdf->Cell(40, 10, 'Landlord Name');
    $pdf->Cell(40, 10, 'Property ID');
    $pdf->Cell(40, 10, 'Plot Number');
    $pdf->Cell(40, 10, 'Occupant');
    $pdf->Cell(40, 10, 'Usage');
    $pdf->Cell(40, 10, 'District');
    $pdf->Cell(40, 10, 'Location');
    $pdf->Cell(40, 10, 'Area');
    $pdf->Cell(40, 10, 'Description');
    $pdf->Ln();

    // Add data
    foreach ($properties as $property) {
        $pdf->Cell(40, 10, $property['Landlord_ID']);
        $pdf->Cell(40, 10, $property['Name']);
        $pdf->Cell(40, 10, $property['Property_ID']);
        $pdf->Cell(40, 10, $property['Plot_number']);
        $pdf->Cell(40, 10, $property['Occupant']);
        $pdf->Cell(40, 10, $property['Usage']);
        $pdf->Cell(40, 10, $property['District']);
        $pdf->Cell(40, 10, $property['Location']);
        $pdf->Cell(40, 10, $property['Area']);
        $pdf->Cell(40, 10, $property['Description']);
        $pdf->Ln();
    }

    $pdf->Output('properties_data.pdf', 'D');
    exit;
}
?>

<!-- Search Form -->
<form method="GET" action="" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="your_page.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- Display Results -->
<p>Showing <strong><?= $total_results; ?></strong> results across all pages.</p>

<!-- Export Buttons -->
<div class="mb-3">
    <a href="?export_excel=true" class="btn btn-success">Export to Excel</a>
    <a href="?export_pdf=true" class="btn btn-danger">Export to PDF</a>
</div>

<!-- Table to Display Data (same table logic as before) -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Landlord Name</th>
            <th>Property ID</th>
            <th>Plot Number</th>
            <th>Occupant</th>
            <th>Usage</th>
            <th>District</th>
            <th>Location</th>
            <th>Area</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><?= htmlspecialchars($property['Name']); ?></td>
                <td><?= htmlspecialchars($property['Property_ID']); ?></td>
                <td><?= htmlspecialchars($property['Plot_number']); ?></td>
                <td><?= htmlspecialchars($property['Occupant']); ?></td>
                <td><?= htmlspecialchars($property['Usage']); ?></td>
                <td><?= htmlspecialchars($property['District']); ?></td>
                <td><?= htmlspecialchars($property['Location']); ?></td>
                <td><?= htmlspecialchars($property['Area']); ?></td>
                <td><?= htmlspecialchars($property['Description']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
