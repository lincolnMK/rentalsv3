<?php

include_once __DIR__ . '/../auth_check.php';

// --- Get User ID ---
$id = $_GET['User_id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    die('Invalid User ID.');
}

// --- Fetch user details ---
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $id); // "i" means integer
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die('User not found.');
}

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and prepare form data
    $first_name = $conn->real_escape_string($_POST['First_name']);
    $last_name = $conn->real_escape_string($_POST['Last_name']);
    $username = $conn->real_escape_string($_POST['Username']);
    $email = $conn->real_escape_string($_POST['Email']);
    $phone = $conn->real_escape_string($_POST['Phone']);
    $designation = $conn->real_escape_string($_POST['Designation']);
    $department = $conn->real_escape_string($_POST['Department']);

    // Use prepared statement for the update query
    $update_sql = "
        UPDATE users SET
            First_name = ?,
            Last_name = ?,
            Username = ?,
            Email = ?,
            Phone = ?,
            Designation = ?,
            Department = ?
        WHERE user_id = ?
    ";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $username, $email, $phone, $designation, $department, $id); // "sssssssi" means 7 strings and 1 integer

    if ($stmt->execute()) {
        // Redirect to avoid resubmission of the form upon refresh
        echo '<script type="text/javascript">
            window.location.href = "index.php?page=user_details&User_id=' . $id . '&updated=1";
          </script>';
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}
?>


<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Edit User Details</h4>
    </div>
</div>

<div class="row p-4">
<div class="col-md-12">

<div class="card">
<div class="card-body">

<h5 class="card-title">User: <?= htmlspecialchars($user['First_name'] . ' ' . $user['Last_name']) ?></h5>
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="user-details-tab" data-bs-toggle="tab" href="#user-details" role="tab" aria-controls="user-details" aria-selected="true">General</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false">Authorizations</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3">


        <!-- User Details Tab -->
        <div class="tab-pane fade show active" id="user-details" role="tabpanel" aria-labelledby="user-details-tab">
           
        <div class="card mb-4">
                <div class="card-body">

            <form method="post">
                <div class="mb-3 d-flex align-items-center">
                    <label for="First_name" class="form-label me-3" style="min-width: 150px;">First Name</label>
                    <input type="text" class="form-control w-auto" id="First_name" name="First_name" value="<?= htmlspecialchars($user['First_name']) ?>" required>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Last_name" class="form-label me-3" style="min-width: 150px;">Last Name</label>
                    <input type="text" class="form-control w-auto" id="Last_name" name="Last_name" value="<?= htmlspecialchars($user['Last_name']) ?>" required>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Username" class="form-label me-3" style="min-width: 150px;">Username</label>
                    <input type="text" class="form-control w-auto" id="Username" name="Username" value="<?= htmlspecialchars($user['Username']) ?>" required>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Email" class="form-label me-3" style="min-width: 150px;">Email</label>
                    <input type="email" class="form-control w-auto" id="Email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Phone" class="form-label me-3" style="min-width: 150px;">Phone</label>
                    <input type="text" class="form-control w-auto" id="Phone" name="Phone" value="<?= htmlspecialchars($user['Phone']) ?>">
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Designation" class="form-label me-3" style="min-width: 150px;">Designation</label>
                    <input type="text" class="form-control w-auto" id="Designation" name="Designation" value="<?= htmlspecialchars($user['Designation']) ?>">
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <label for="Department" class="form-label me-3" style="min-width: 150px;">Department</label>
                    <input type="text" class="form-control w-auto" id="Department" name="Department" value="<?= htmlspecialchars($user['Department']) ?>">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <a href="index.php?page=user_details&User_id=<?= $id ?>" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
            </div>
            </div>
        </div>

        <!-- Authorizations Tab -->
        <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
           
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">
                        <strong>User Type:</strong> <?= htmlspecialchars($user['user_type_ID'] ?? 'N/A') ?><br>
                        <strong>Assigned Permissions:</strong> <?= htmlspecialchars($user['Permissions'] ?? 'None') ?><br>
                    </p>
                    <a href="index.php?page=edit_permissions&User_id=<?= $id ?>" class="btn btn-primary">Edit Permissions</a>
                </div>
            </div>
        </div>
    </div>

    </div>
            </div>
        </div>
    </div>