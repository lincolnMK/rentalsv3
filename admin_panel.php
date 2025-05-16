<?php

include('db_connection.php'); // your DB connection file

// For security, only allow admins (adjust according to your auth system)


// Fetch users (you can filter to admins only)
$users = $conn->query("SELECT user_id, username FROM users ORDER BY username");

// Fetch modules
$modules = $conn->query("SELECT Module_ID, Module_Name FROM modules ORDER BY Module_Name");

// Handle form submission to update permissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);

    // First, delete existing permissions for this user (optional: or do upsert)
    $conn->query("DELETE FROM permissions WHERE user_id = $user_id");

    // Insert new permissions from form data
    foreach ($modules as $module) {
        $mod_id = $module['Module_ID'];
        $can_view = isset($_POST["can_view_$mod_id"]) ? 1 : 0;
        $can_add = isset($_POST["can_add_$mod_id"]) ? 1 : 0;
        $can_edit = isset($_POST["can_edit_$mod_id"]) ? 1 : 0;
        $can_delete = isset($_POST["can_delete_$mod_id"]) ? 1 : 0;

        // Only insert if any permission is granted
        if ($can_view || $can_add || $can_edit || $can_delete) {
            $stmt = $conn->prepare("INSERT INTO permissions (user_id, module_id, can_view, can_add, can_edit, can_delete) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiii", $user_id, $mod_id, $can_view, $can_add, $can_edit, $can_delete);
            $stmt->execute();
            $stmt->close();
        }
    }

    $message = "Permissions updated for user ID $user_id.";
}

// If a user is selected, load their permissions
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_permissions = [];

if ($selected_user_id > 0) {
    $result = $conn->query("SELECT * FROM permissions WHERE user_id = $selected_user_id");
    while ($row = $result->fetch_assoc()) {
        $user_permissions[$row['module_id']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Panel - Manage Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h1>Admin Panel - Manage Permissions</h1>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="get" class="mb-3">
        <label for="user_id" class="form-label">Select User:</label>
        <select name="user_id" id="user_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Select a User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $selected_user_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selected_user_id > 0): ?>
    <form method="post">
        <input type="hidden" name="user_id" value="<?= $selected_user_id ?>" />

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>View</th>
                    <th>Add</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modules as $module): 
                    $perm = $user_permissions[$module['Module_ID']] ?? [];
                ?>
                <tr>
                    <td><?= htmlspecialchars($module['Module_Name']) ?></td>
                    <td><input type="checkbox" name="can_view_<?= $module['Module_ID'] ?>" <?= (!empty($perm['can_view']) ? 'checked' : '') ?>></td>
                    <td><input type="checkbox" name="can_add_<?= $module['Module_ID'] ?>" <?= (!empty($perm['can_add']) ? 'checked' : '') ?>></td>
                    <td><input type="checkbox" name="can_edit_<?= $module['Module_ID'] ?>" <?= (!empty($perm['can_edit']) ? 'checked' : '') ?>></td>
                    <td><input type="checkbox" name="can_delete_<?= $module['Module_ID'] ?>" <?= (!empty($perm['can_delete']) ? 'checked' : '') ?>></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Save Permissions</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
