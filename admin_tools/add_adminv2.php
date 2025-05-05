<?php
include('db_connection.php');

// Admin credentials
$username = 'klmkumbwa';
$password = password_hash('admin', PASSWORD_DEFAULT); // Hash the password
$user_type = 'admin';
$first_name = 'Admin';
$last_name = 'User';
$email = 'admin@example.com';
$designation = 'Administrator';

// Check if admin user already exists
$query = "SELECT * FROM USERS WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert admin user into the database
    $query = "INSERT INTO USERS (username, password, user_type, first_name, last_name, Email, Designation) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssss', $username, $password, $user_type, $first_name, $last_name, $email, $designation);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Admin user already exists.";
}
?>
3