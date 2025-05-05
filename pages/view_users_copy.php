<?php

// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count total number of users
$total_users = 0;
$users_count_query = "SELECT COUNT(*) AS total_users FROM users";
$stmt = $conn->prepare($users_count_query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

$stmt->close();


// Fetch users data for pagination
$users = array();
$limit = 20; // Number of entries to show per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;


$sql = "SELECT * FROM users LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

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
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                    </li>
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
