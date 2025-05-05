<?php
include('db_connection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the request data
$data = json_decode(file_get_contents("php://input"), true);

// Retrieve the search and pagination parameters
$page = isset($data['page']) ? $data['page'] : 1; // Current page
$itemsPerPage = isset($data['itemsPerPage']) ? $data['itemsPerPage'] : 20; // Number of records per page
$search = isset($data['search']) ? $data['search'] : ''; // Search term
$searchColumn = isset($data['searchColumn']) ? $data['searchColumn'] : ''; // Column to search from
$sortColumn = isset($data['sortColumn']) ? $data['sortColumn'] : 'Property_ID'; // Column to sort by
$sortDirection = isset($data['sortDirection']) ? $data['sortDirection'] : 'asc'; // Sort direction

// Define the default columns to retrieve
$defaultColumns = [
    
];

// Determine the selected columns
$selectedColumns = isset($data['selectedColumns']) ? $data['selectedColumns'] : $defaultColumns;
$columns = implode(", ", $selectedColumns);

// Calculate the offset for pagination
$offset = ($page - 1) * $itemsPerPage;

// Build the SQL query
if ($searchColumn && in_array($searchColumn, $defaultColumns)) {
    // Search in a specific column
    $sql = "SELECT $columns FROM PROPERTy WHERE $searchColumn LIKE ? ORDER BY $sortColumn $sortDirection LIMIT ?, ?";
} else {
    // Search across all columns
    $sql = "SELECT $columns FROM PROPERTy WHERE CONCAT_WS('', $columns) LIKE ? ORDER BY $sortColumn $sortDirection LIMIT ?, ?";
}

// Prepare the statement
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("sii", $searchTerm, $offset, $itemsPerPage); // Bind parameters
$stmt->execute();
$result = $stmt->get_result();

$dataRows = [];
while ($row = $result->fetch_assoc()) {
    $dataRows[] = $row;
}

// Get the total number of records for pagination
$totalSql = "SELECT COUNT(*) AS total FROM PROPERTy WHERE CONCAT_WS('', $columns) LIKE ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("s", $searchTerm);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];

// Calculate the total number of pages
$totalPages = ceil($totalItems / $itemsPerPage);

// Prepare the response data
$response = [
    'data' => $dataRows,
    'totalItems' => $totalItems,
    'itemsPerPage' => $itemsPerPage,
    'totalPages' => $totalPages,
    'currentPage' => $page
];

// Send the response as JSON
header("Content-Type: application/json");
echo json_encode($response);

$conn->close();
?>
