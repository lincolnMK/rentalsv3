<?php
include_once __DIR__ . '/../auth_check.php';



if (isset($_GET['User_id']) && ctype_digit($_GET['User_id']) && (int)$_GET['User_id'] > 0) {
    $user_id = (int)$_GET['User_id'];
    $id= $user_id;
}

if (!isset($id)) {
    $id = $_POST['user_id'] ?? null;
    if (!$id) {
        echo "<div class='alert alert-danger'>Invalid user ID.</div>";
        exit;
    }
}
// --- Show success message ---
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    echo "<div class='alert alert-success'>User details updated successfully!</div>";
    
}

if (isset($_GET['password_changed']) && $_GET['password_changed'] == 1) {
    echo "<div class='alert alert-success'>Password updated successfully.</div>";
    $changePassword = false;
}

// --- Fetch user details securely ---
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// --- Fetch modules ---
$modules_result = mysqli_query($conn, "SELECT * FROM modules");

// --- Fetch user permissions ---
$permissions_result = mysqli_query($conn, "SELECT * FROM permissions WHERE user_id = $user_id");
$permissions = [];
while ($row = mysqli_fetch_assoc($permissions_result)) {
    $permissions[$row['module_id']] = $row;
}

// Determine whether password fields should be enabled
$changePassword = isset($_GET['change_password']) && $_GET['change_password'] == '1';
$activeTab = isset($_GET['change_password']) ? 'security' : 'user-details';




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
        <a class="nav-link <?= $activeTab === 'user-details' ? 'active' : '' ?>" 
           id="user-details-tab" 
           data-bs-toggle="tab" 
           href="#user-details" 
           role="tab" 
           aria-controls="user-details" 
           aria-selected="<?= $activeTab === 'user-details' ? 'true' : 'false' ?>">
           General
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $activeTab === 'permissions' ? 'active' : '' ?>" 
           id="permissions-tab" 
           data-bs-toggle="tab" 
           href="#permissions" 
           role="tab" 
           aria-controls="permissions" 
           aria-selected="<?= $activeTab === 'permissions' ? 'true' : 'false' ?>">
           Authorizations
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $activeTab === 'security' ? 'active' : '' ?>" 
           id="security-tab" 
           data-bs-toggle="tab" 
           href="#security" 
           role="tab" 
           aria-controls="security" 
           aria-selected="<?= $activeTab === 'security' ? 'true' : 'false' ?>">
           Security
        </a>
    </li>
</ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <!-- User Details Tab -->
        <div class="tab-pane fade <?= $activeTab === 'user-details' ? 'show active' : '' ?>" 
             id="user-details" 
             role="tabpanel" 
             aria-labelledby="user-details-tab">

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
                   
        </div>

        <!-- Permissions Tab -->
        <div class="tab-pane fade <?= $activeTab === 'permissions' ? 'show active' : '' ?>" 
         id="permissions" 
         role="tabpanel" 
         aria-labelledby="permissions-tab">

  <div class="card mb-4">
    <div class="card-body">
      <?php
      $query = "
        SELECT 
            u.User_ID, u.Username, m.Module_Name, 
            p.can_view, p.can_add, p.can_edit, p.can_delete
        FROM permissions p
        JOIN users u ON p.user_id = u.User_ID
        JOIN modules m ON p.module_id = m.Module_ID
        WHERE u.User_ID = $id
        ORDER BY m.Module_Name
      ";

      $result = mysqli_query($conn, $query);
      ?>

      <div class="table-responsive">
        <?php if (mysqli_num_rows($result) > 0): ?>
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
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= htmlspecialchars($row['Module_Name']) ?></td>
                  <td class="text-center"><?= $row['can_view'] ? '✔️' : '❌' ?></td>
                  <td class="text-center"><?= $row['can_add'] ? '✔️' : '❌' ?></td>
                  <td class="text-center"><?= $row['can_edit'] ? '✔️' : '❌' ?></td>
                  <td class="text-center"><?= $row['can_delete'] ? '✔️' : '❌' ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-warning m-3">
            No permissions set for this user.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <a href="index.php?page=edit_user&User_id=<?= $id ?>" class="btn btn-primary">Modify Permissions</a>
</div>


        <!-- Security Tab -->
 <div class="tab-pane fade <?= $activeTab === 'security' ? 'show active' : '' ?>" 
         id="security" 
         role="tabpanel" 
         aria-labelledby="security-tab">

  <div class="card mb-4">
    <div class="card-body">
      <h4 class="mb-3">Security Settings</h4>

      <!-- Toggle button -->
      <a href="?page=user_details&User_id=<?= $id ?><?= $changePassword ? '' : '&change_password=1' ?>" 
   class="btn btn-outline-secondary mb-3">
   <?= $changePassword ? 'Cancel Password Change' : 'Change Password' ?>
</a>


      <!-- Security form -->
      <form method="post" action="index.php?page=change_pass<?= $changePassword ? '&change_password=1' : '' ?>">
        <input type="hidden" name="user_id" value="<?= $id ?>">

        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input 
            type="password" 
            class="form-control" 
            id="new_password" 
            name="new_password" 
            placeholder="Enter new password" 
            <?= $changePassword ? '' : 'disabled' ?>
          > 

          <input 
            type="password" 
            class="form-control mt-2" 
            id="confirm_password" 
            name="confirm_password" 
            placeholder="Confirm new password" 
            <?= $changePassword ? '' : 'disabled' ?>
          >

          <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>





        </div> <!-- tab-content -->
    </div> <!-- card-body -->
</div> <!-- card -->
</div> <!-- col -->
</div> <!-- row -->


