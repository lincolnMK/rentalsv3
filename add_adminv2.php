<?php
include('db_connection.php'); // Ensure this file contains your database connection ($conn)

// Admin credentials
$username = 'klmkumbwa';
$password = password_hash('admin', PASSWORD_DEFAULT); // Securely hash the password
$first_name = 'Admin';
$last_name = 'User';
$email = 'admin@example.com';
$phone = '123456789';
$designation = 'Administrator';
$department = 'IT';
$profile_picture = NULL; // Set NULL or provide a path to an image

// Default admin role and type IDs (Assuming 1 is for Admin)
$admin_role_id = 1;
$admin_type_id = 1;

// Check if admin user already exists
$query = "SELECT * FROM Users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert the admin user
    $query = "INSERT INTO Users (username, password, first_name, last_name, email, phone, designation, department, profile_picture, user_role_id, user_type_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssssssssi', $username, $password, $first_name, $last_name, $email, $phone, $designation, $department, $profile_picture, $admin_role_id, $admin_type_id);

    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Admin user already exists.";
}

// Close the connection
$stmt->close();
$conn->close();
?>
