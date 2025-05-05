<?php
include('db_connection.php');

// Admin credentials
$username = 'admin';
$password = password_hash('admin', PASSWORD_DEFAULT); // Hash the password
$user_type = 'admin';

// Check if admin user already exists
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert admin user into the database
    $query = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $username, $password, $user_type);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Admin user already exists.";
}
?>
