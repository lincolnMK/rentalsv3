<?php
include_once __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid access.');
}

$user_id = (int)($_POST['user_id'] ?? 0);
$submitted_permissions = $_POST['permissions'] ?? [];

// Basic validation
if ($user_id <= 0) {
    die('Invalid user ID.');
}

// Use a prepared statement for the DELETE to avoid SQL injection
$stmt = $conn->prepare("DELETE FROM permissions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Insert updated permissions using prepared statements
$insert_stmt = $conn->prepare("
    INSERT INTO permissions (user_id, module_id, can_view, can_add, can_edit, can_delete)
    VALUES (?, ?, ?, ?, ?, ?)
");

foreach ($submitted_permissions as $module_id => $actions) {
    $module_id = (int)$module_id;
    $can_view = isset($actions['can_view']) ? 1 : 0;
    $can_add = isset($actions['can_add']) ? 1 : 0;
    $can_edit = isset($actions['can_edit']) ? 1 : 0;
    $can_delete = isset($actions['can_delete']) ? 1 : 0;

    $insert_stmt->bind_param("iiiiii", $user_id, $module_id, $can_view, $can_add, $can_edit, $can_delete);
    $insert_stmt->execute();
}

// Clear session cache of permissions for this user
if ($_SESSION['user_id'] == $user_id) {
    unset($_SESSION['permissions']);
}

// Redirect back with success flag
echo '<script>
    window.location.href = "index.php?page=user_details&User_id=' . $user_id . '&updated=1";
</script>';
exit;
?>
