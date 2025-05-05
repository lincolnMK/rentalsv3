



<?php
$host = "localhost"; // Change if necessary
$user = "root"; // Change if necessary
$password = ""; // Change if necessary
$database = "rentalmanagement"; // Change to your database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT landlord_id, Name FROM landlord";
$result = $conn->query($sql);

$landlords = [];

while ($row = $result->fetch_assoc()) {
    $landlords[] = $row;
}

echo json_encode($landlords);

$conn->close();
?>
