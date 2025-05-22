<?php

// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';


// Handle search input
$search = $_GET['search'] ?? '';

// Count total users
$users_count_query = "SELECT COUNT(*) AS total_users FROM users";
if (!empty($search)) {
    $users_count_query = "SELECT COUNT(*) AS total_users FROM users 
        WHERE Username LIKE ? OR First_name LIKE ? OR Last_name LIKE ? OR Email LIKE ?";
    $stmt = $conn->prepare($users_count_query);
    $search_param = "%{$search}%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare($users_count_query);
}

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_users = $row['total_users'];
$stmt->close();

// Pagination Setup
$limit = 50;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) && $_GET['pages'] > 0 ? (int)$_GET['pages'] : 1;
$start = ($pages - 1) * $limit;
if ($start < 0) $start = 0;

// Fetch users
$users = [];
if (!empty($search)) {
    $sql = "SELECT * FROM users 
        WHERE Username LIKE ? OR First_name LIKE ? OR Last_name LIKE ? OR Email LIKE ? 
        LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $start, $limit);
} else {
    $sql = "SELECT * FROM users LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

$total_pages = ceil($total_users / $limit);
?>


         
            <!-- Topbar (Inside Main Content) -->
            <div class="row bg-white py-3 shadow-sm">
                <div class="col">
                    <h4 class="text-primary">Users</h4>
                </div>
            </div>

            <!-- Content Goes Here -->
            <div class="row p-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

<!-- Search Form -->
                                    <form method="GET" action="index.php" class="mb-3 d-flex align-items-center">

    <div class="input-group me-3">
        <input type="hidden" name="page" value="users">
        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Search by Username, First Name, Last Name, Email" 
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button class="btn btn-primary" type="submit">Search</button>
        <a href="index.php?page=users" class="btn btn-secondary">Clear</a>
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



                            <h5 class="card-title">Users</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                       
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>User Role</th>
                                       
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['User_ID']); ?></td>
                                            <td><a href="index.php?page=user_details&User_id=<?= htmlspecialchars($user['User_ID']); ?>"><?= htmlspecialchars($user['Username']); ?></a></td>
                                            
                                            <td><?= htmlspecialchars($user['First_name']); ?></td>
                                            <td><?= htmlspecialchars($user['Last_name']); ?></td>
                                            <td><?= htmlspecialchars($user['Email']); ?></td>
                                           
                                            <td><?= htmlspecialchars($user['Designation']?? ''); ?></td>
                                            <td><?= htmlspecialchars($user['user_type_id']?? ''); ?></td>
                                            <td><?= htmlspecialchars($user['Created_At']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                              <nav>
    <ul class="pagination">
        <?php if ($pages > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=users&pages=<?= $pages - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $pages) ? 'active' : '' ?>">
                <a class="page-link" href="index.php?page=users&pages=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pages < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=users&pages=<?= $pages + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
                        </div>
                    </div>
                </div>
            </div>
        
<!-- Bootstrap JS -->



<?php
$conn->close();
?>
