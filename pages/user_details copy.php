<?php
// Database connection
$host = 'localhost';
$database = 'rentalmanagement';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get user ID
$id = $_GET['User_id'] ?? $_POST['User_id'] ?? 1;
$id = (int)$id;

// If form is submitted to update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $firstname = $conn->real_escape_string($_POST['First_name']);
    $lastname = $conn->real_escape_string($_POST['Last_name']);
    $email = $conn->real_escape_string($_POST['Email']);
    $phone = $conn->real_escape_string($_POST['Phone']);
    $designation = $conn->real_escape_string($_POST['Designation']);
    $department = $conn->real_escape_string($_POST['Department']);
    $username = $conn->real_escape_string($_POST['Username']);

    $update_sql = "
        UPDATE users 
        SET First_name='$firstname', Last_name='$lastname', Email='$email', Phone='$phone', Designation='$designation', Department='$department', Username='$username'
        WHERE user_id=$id
    ";

    if ($conn->query($update_sql)) {
        $success_message = "User details updated successfully.";
    } else {
        $error_message = "Update failed: " . $conn->error;
    }
}

// Fetch user details again
$user_sql = "SELECT * FROM users WHERE user_id = $id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch permissions
$permissions_sql = "SELECT * FROM permissions WHERE User_Id = $id";
$permissions_result = $conn->query($permissions_sql);

$permissions = [];
while ($row = $permissions_result->fetch_assoc()) {
    $permissions[$row['module_name']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function enableEditMode() {
            const inputs = document.querySelectorAll('.editable');
            inputs.forEach(input => input.removeAttribute('readonly'));
            document.getElementById('saveButton').classList.remove('d-none');
            document.getElementById('editButton').classList.add('d-none');
        }
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">User Details</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <!-- User Details Form -->
    <form method="POST" action="">
        <input type="hidden" name="User_id" value="<?= $id ?>">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <input type="text" name="First_name" value="<?= htmlspecialchars($user['First_name']) ?>" class="form-control editable mb-2" readonly>
                    <input type="text" name="Last_name" value="<?= htmlspecialchars($user['Last_name']) ?>" class="form-control editable mb-2" readonly>
                </h5>
                <p class="card-text">
                    <label><strong>Username:</strong></label>
                    <input type="text" name="Username" value="<?= htmlspecialchars($user['Username']?? '') ?>" class="form-control editable mb-2" readonly>

                    <label><strong>Email:</strong></label>
                    <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']?? '') ?>" class="form-control editable mb-2" readonly>

                    <label><strong>Phone:</strong></label>
                    <input type="text" name="Phone" value="<?= htmlspecialchars($user['Phone']?? '') ?>" class="form-control editable mb-2" readonly>

                    <label><strong>Designation:</strong></label>
                    <input type="text" name="Designation" value="<?= htmlspecialchars($user['Designation']?? '') ?>" class="form-control editable mb-2" readonly>

                    <label><strong>Department:</strong></label>
                    <input type="text" name="Department" value="<?= htmlspecialchars($user['Department']?? '') ?>" class="form-control editable mb-2" readonly>

                    <label><strong>User Type:</strong></label>
                    <input type="text" value="<?= htmlspecialchars($user['user_type_ID']?? '') ?>" class="form-control mb-2" readonly disabled>
                </p>

                <button type="button" id="editButton" onclick="enableEditMode()" class="btn btn-primary">Edit User Details</button>
                <button type="submit" name="update_user" id="saveButton" class="btn btn-success d-none">Save Changes</button>
                <a href="edit_credentials.php?id=<?= $id ?>" class="btn btn-warning ms-2">Edit Credentials</a>
            </div>
        </div>
    </form>

    <!-- Permissions Table -->
    <h4>Access Permissions</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Module</th>
                    <th>Create</th>
                    <th>Read</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissions as $module => $perm): ?>
                    <tr>
                        <td><?= htmlspecialchars(ucfirst($module)) ?></td>
                        <td><?= $perm['can_create'] ? '✅' : '❌' ?></td>
                        <td><?= $perm['can_read'] ? '✅' : '❌' ?></td>
                        <td><?= $perm['can_update'] ? '✅' : '❌' ?></td>
                        <td><?= $perm['can_delete'] ? '✅' : '❌' ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($permissions)): ?>
                    <tr><td colspan="5" class="text-center">No permissions set for this user.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
