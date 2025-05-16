<?php
include_once __DIR__ . '/../auth_check.php';

// --- Get User ID ---
$user_id = isset($_GET['User_id']) ? (int)$_GET['User_id'] : 0;
$id = $user_id;
if ($user_id <= 0) {
    die('Invalid User ID.');
}

// --- Fetch user details securely ---
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die('User not found.');
}

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['First_name']);
    $last_name = $conn->real_escape_string($_POST['Last_name']);
    $username = $conn->real_escape_string($_POST['Username']);
    $email = $conn->real_escape_string($_POST['Email']);
    $phone = $conn->real_escape_string($_POST['Phone']);
    $designation = $conn->real_escape_string($_POST['Designation']);
    $department = $conn->real_escape_string($_POST['Department']);

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
    $stmt->bind_param("sssssssi", $first_name, $last_name, $username, $email, $phone, $designation, $department, $user_id);

    if ($stmt->execute()) {
        echo '<script type="text/javascript">
            window.location.href = "index.php?page=user_details&User_id=' . $user_id . '&updated=1";
        </script>';
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}

// --- Fetch all modules ---
$modules_result = mysqli_query($conn, "SELECT * FROM modules");

// --- Fetch user permissions with module names ---
$permissions_result = mysqli_query($conn, "
    SELECT p.*, m.Module_Name 
    FROM permissions p 
    JOIN modules m ON p.module_id = m.module_id 
    WHERE p.user_id = $user_id
");

$permissions = [];
while ($row = mysqli_fetch_assoc($permissions_result)) {
    $permissions[$row['module_id']] = $row;
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
                                    <?php
                                    $fields = [
                                        'First_name' => 'First Name',
                                        'Last_name' => 'Last Name',
                                        'Username' => 'Username',
                                        'Email' => 'Email',
                                        'Phone' => 'Phone',
                                        'Designation' => 'Designation',
                                        'Department' => 'Department'
                                    ];
                                    foreach ($fields as $name => $label): ?>
                                        <div class="mb-3 d-flex align-items-center">
                                            <label for="<?= $name ?>" class="form-label me-3" style="min-width: 150px;"><?= $label ?></label>
                                            <input type="<?= $name === 'Email' ? 'email' : 'text' ?>" class="form-control w-auto" id="<?= $name ?>" name="<?= $name ?>" value="<?= htmlspecialchars($user[$name]) ?>" <?= in_array($name, ['First_name', 'Last_name', 'Username', 'Email']) ? 'required' : '' ?>>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                        <a href="index.php?page=user_details&User_id=<?= $id ?>" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Tab -->
                    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3">Access Permissions</h4>
                                <form method="post" action="index.php?page=save_permissions">
                                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Module</th>
                                                    <th class="text-center">View</th>
                                                    <th class="text-center">Add</th>
                                                    <th class="text-center">Edit</th>
                                                    <th class="text-center">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($module = mysqli_fetch_assoc($modules_result)):
                                                    $perm = $permissions[$module['Module_ID']] ?? ['can_view'=>0, 'can_add'=>0, 'can_edit'=>0, 'can_delete'=>0];
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($module['Module_Name']) ?></td>
                                                        <?php foreach (['can_view', 'can_add', 'can_edit', 'can_delete'] as $action): ?>
                                                            <td class="text-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input"
                                                                        type="checkbox"
                                                                        name="permissions[<?= $module['Module_ID'] ?>][<?= $action ?>]"
                                                                        value="1"
                                                                        <?= $perm[$action] ? 'checked' : '' ?>>
                                                                </div>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-success">Save Permissions</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> <!-- tab-content -->

            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- col -->
</div> <!-- row -->
