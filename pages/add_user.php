<?php

// Check if user is logged in
include_once __DIR__ . '/../auth_check.php';

// Fetch the username from the session
$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $firstname = $_POST['first_name'] ?? '';
    $lastname = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $department = $_POST['department'] ?? '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $user_type = $_POST['user_type_ID'] ?? '';
    $profile_picture = NULL; // Default to NULL if no picture is uploaded

    // Validate if the email already exists in the database
    $email_check_query = "SELECT * FROM users WHERE Email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: The email address is already in use.";
        exit;
    }

    // Validate if the username already exists in the database
    $username_check_query = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($username_check_query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: The username is already in use.";
        exit;
    }

    // Handle file upload if present
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0 && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/profiles/";
        $original_filename = basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            // Generate unique filename
            $unique_filename = uniqid('profile_', true) . '.' . $imageFileType;
            $target_file = $target_dir . $unique_filename;

            // Move uploaded file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "Error: File upload failed.";
                exit;
            }
        } else {
            echo "Error: Invalid file type.";
            exit;
        }
    }

    // Validate required fields
    if (empty($username) || empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($user_type)) {
        echo "Error: Required fields cannot be empty.";
        exit;
    }

    // Insert user into the database
    $query = "INSERT INTO users (`Username`, `First_name`, `Last_name`, `Email`, `Phone`, `Designation`, `Department`, `Password`, `User_type_ID`, `Profile_picture`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Error: " . $conn->error;
        exit;
    }

    $stmt->bind_param('ssssssssss', $username, $firstname, $lastname, $email, $phone, $designation, $department, $password, $user_type, $profile_picture);

    if ($stmt->execute()) {
        echo "New user added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close resources
    $stmt->close();
    $conn->close();
}
?>


<!-- Topbar (Inside Main Content) -->
<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Add User</h4>
    </div>
</div>

<!-- Content Goes Here -->
<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">User Information</h5>
                <!-- form starts here -->
                <form action="" method="post" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="col-md-6">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" required>
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" required>
                    </div>
                    <div class="col-md-6">
                        <label for="user_type_ID" class="form-label">User Type</label>
                        <select class="form-select" id="user_type_ID" name="user_type_ID" required>
                            <option value="2">User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                        <!-- form ends here -->
                </div>

                </div>
    </div>
</div>