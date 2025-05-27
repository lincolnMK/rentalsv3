


<?php

session_start();


function log_login_attempt($conn, $username, $status) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $stmt = $conn->prepare("INSERT INTO login_audit (username, status, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $status, $ip, $agent);
    $stmt->execute();
    $stmt->close();
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php?page=home");
    exit;
}

include('db_connection.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['Username']);
    $password = trim($_POST['Password']);

    $query = "SELECT User_ID, User_role_ID, User_type_ID, Username, Password, Profile_picture, First_name, Last_name FROM Users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['user_role_id'] = $user['User_role_ID'];
        $_SESSION['user_type_id'] = $user['User_type_ID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['profile_picture'] = $user['Profile_picture'];
       $_SESSION['full_name'] = $user['First_name'] . ' ' . $user['Last_name'];


        log_login_attempt($conn, $username, 'success'); // ✅ Log successful login
        header("Location: index.php?page=home");
        exit;
    } else {
        log_login_attempt($conn, $username, 'failure'); // ❌ Wrong password
        $error = "Invalid credentials!";
    }
} else {
    log_login_attempt($conn, $username, 'failure'); // ❌ Username not found
    $error = "User not found!";
}
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <!-- Logo and System Title -->
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="System Logo" class="img-fluid mb-3" style="max-width: 100px;">
                            <h3 class="fw-bold">Rental Management System</h3>
                        </div>
                        
                        <!-- Error Message -->
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="Username" class="form-label">Username</label>
                                <input type="text" name="Username" id="Username" class="form-control" placeholder="Enter your username" required>
                            </div>
                            <div class="mb-3">
                                <label for="Password" class="form-label">Password</label>
                                <input type="Password" name="Password" id="Password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-3">
                    <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
