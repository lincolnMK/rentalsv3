<?php
// Database connection settings
$host = 'localhost';        // Hostname (use '127.0.0.1' or 'localhost')
$db_name = 'rental_db';     // Name of your database
$username = 'root';         // Database username
$password = '';             // Database password (leave blank if none)

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
