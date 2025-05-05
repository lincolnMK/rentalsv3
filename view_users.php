<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the username from the session
$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture'] ?? 'assets/images/default_avatar.png';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count total number of users
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
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <!-- Load Bootstrap from CDN -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <!-- Top Bar -->
     <!-- Top Bar -->
     <div class="row bg-dark text-white py-2">
        <div class="col-md-6">
            <!-- Optional: Place your content here (e.g., logo, title, etc.) -->
        </div>
        <div class="col-md-6 text-end">
            <!-- User Dropdown -->
            <div class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($username); ?></span>
                    <?php
                        // Assuming $profile_picture contains the path to the uploaded image
                        if (!empty($profile_picture)) {
                            // If the user has uploaded a profile picture, display it
                            echo '<img class="img-profile rounded-circle img-fluid" src="' . htmlspecialchars($profile_picture) . '" alt="User Profile" style="width: 30px; height: 30px;">';
                        } else {
                            // If no profile picture, display a default image
                            echo '<img class="img-profile rounded-circle img-fluid" src="assets/images/default-avatar.png" alt="0" style="width: 30px; height: 30px;">';
                        }
                    ?>
                </a>
                <!-- Dropdown - User Information -->
                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Settings</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Activity Log</a></li>
                    <div class="dropdown-divider"></div>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Sidebar and Main Content Wrapper -->
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 bg-light vh-100 d-md-block sidebar">
            <div class="d-flex flex-column align-items-start py-3">
                <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                <h3 class="ms-3">Rental System</h3>
                <ul class="nav flex-column w-100 mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="homepage.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_user.php">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-10 bg-light">
            <!-- Button to toggle sidebar visibility -->
            <button id="sidebarToggle" class="btn btn-dark d-md-none">☰</button>

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
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
