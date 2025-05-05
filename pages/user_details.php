<?php


include_once __DIR__ . '/../auth_check.php';


// Check for success flag in the query string
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    echo "<div class='alert alert-success'>User details updated successfully!</div>";
}




// --- Get the user ID ---
$id = $_GET['User_id'] ?? 1;
$id = (int)$id; // Always cast to int for safety

// --- Fetch user details ---
$user_sql = "SELECT * FROM users WHERE user_id = $id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// --- Fetch user permissions ---
$permissions_sql = "SELECT * FROM permissions WHERE User_Id = $id";
$permissions_result = $conn->query($permissions_sql);

$permissions = [];
while ($row = $permissions_result->fetch_assoc()) {
    $permissions[$row['module_name']] = $row;
}
?>

<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">User Details</h4>
    </div>
</div>

<div class="row p-4">
<div class="col-md-12">

<div class="card">
<div class="card-body">
<h5 class="card-title">User: <?= htmlspecialchars($user['First_name'] . ' ' . $user['Last_name']) ?></h5>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="user-details-tab" data-bs-toggle="tab" href="#user-details" role="tab" aria-controls="user-details" aria-selected="true">General</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false">Authorizations</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <!-- User Details Tab -->
        <div class="tab-pane fade show active" id="user-details" role="tabpanel" aria-labelledby="user-details-tab">
            <!-- User Details Card -->
            <div class="card mb-4">
                <div class="card-body">
                   
                    <p class="card-text">
                        <strong>Username:</strong> <?= htmlspecialchars($user['Username']?? '') ?><br>
                        <strong>Surname:</strong> <?= htmlspecialchars($user['Last_name']?? '') ?><br>
                        <strong>Fisrt name:</strong> <?= htmlspecialchars($user['First_name']?? '') ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($user['Email']?? '') ?><br>
                        <strong>Phone:</strong> <?= htmlspecialchars($user['Phone']?? '') ?><br>
                        <strong>Designation:</strong> <?= htmlspecialchars($user['Designation']?? '') ?><br>
                        <strong>Department:</strong> <?= htmlspecialchars($user['Department']?? '') ?><br>
                        <strong>EMployMent #:</strong> <?= htmlspecialchars($user['Emp_No']?? '') ?><br><strong>User Type:</strong> <?= htmlspecialchars($user['user_type_ID']?? '') ?><br>
                    </p>
                    
                    
                </div>
            </div>
            <a href="index.php?page=edit_user&User_id=<?= $id ?>" class="btn btn-primary">Edit User Details</a>
                    <a href=" " class="btn btn-primary">reset password</a>
        </div>

        <!-- Permissions Tab -->
        <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
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
                <a href="edit_credentials.php?User_id=<?= $id ?>" class="btn btn-warning ms-2">Modify Permissions</a>
            </div>
        </div>
    </div>

                        </div>
 </div>
 </div>
 </div>
