<?php
include('db_connection.php');

$limit = 10; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$query = isset($_GET['query']) ? $_GET['query'] : '';

$sql = "SELECT landlord_id, name, Phone_Number FROM landlord WHERE name LIKE ? OR Phone_Number LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search = "%$query%";
$stmt->bind_param("ssii", $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$output = '<ul class="list-group">';
while ($row = $result->fetch_assoc()) {
    $output .= '<li class="list-group-item d-flex justify-content-between align-items-center">';
    $output .= '<span>' . htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['Phone_Number']) . ')</span>';
    
    // ✅ Fixed this line to use correct attribute name
    $output .= '<button class="btn btn-success btn-sm select-landlord" data-landlord_id="' . $row['landlord_id'] . '" data-name="' . htmlspecialchars($row['name']) . '">Select</button>';
    
    $output .= '</li>';
}
$output .= '</ul>';

// Count total landlords for pagination
$count_sql = "SELECT COUNT(*) AS total FROM landlord WHERE name LIKE ? OR Phone_Number LIKE ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("ss", $search, $search);
$count_stmt->execute();
$count_result = $count_stmt->get_result()->fetch_assoc();
$total = $count_result['total'];
$total_pages = ceil($total / $limit);

// Pagination UI
$pagination = '<ul class="pagination">';

// Previous Button
if ($page > 1) {
    $pagination .= "<li class='page-item'><a class='page-link' href='#' onclick='fetchLandlords(" . ($page - 1) . ")'>&laquo; Prev</a></li>";
}

// Show first page and dots if needed
if ($page > 3) {
    $pagination .= "<li class='page-item'><a class='page-link' href='#' onclick='fetchLandlords(1)'>1</a></li>";
    if ($page > 4) {
        $pagination .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
    }
}

// Show page numbers around the current page
for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
    $active = ($i == $page) ? 'active' : '';
    $pagination .= "<li class='page-item $active'><a class='page-link' href='#' onclick='fetchLandlords($i)'>$i</a></li>";
}

// Show last page and dots if needed
if ($page < $total_pages - 2) {
    if ($page < $total_pages - 3) {
        $pagination .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
    }
    $pagination .= "<li class='page-item'><a class='page-link' href='#' onclick='fetchLandlords($total_pages)'>$total_pages</a></li>";
}

// Next Button
if ($page < $total_pages) {
    $pagination .= "<li class='page-item'><a class='page-link' href='#' onclick='fetchLandlords(" . ($page + 1) . ")'>Next &raquo;</a></li>";
}

$pagination .= '</ul>';

echo json_encode(['results' => $output, 'pagination' => $pagination]);
?>
