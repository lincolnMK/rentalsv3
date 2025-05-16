<?php
// auth_check.php
session_start();
include_once 'config.php';
include_once 'db_connection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

// Load user permissions only once per session
if (!isset($_SESSION['permissions'])) {
    $_SESSION['permissions'] = [];

    $stmt = $conn->prepare("
        SELECT m.Module_Name, p.can_view, p.can_add, p.can_edit, p.can_delete
        FROM permissions p
        JOIN modules m ON p.module_id = m.Module_ID
        WHERE p.user_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $_SESSION['permissions'][$row['Module_Name']] = [
            'can_view' => (int)$row['can_view'],
            'can_add' => (int)$row['can_add'],
            'can_edit' => (int)$row['can_edit'],
            'can_delete' => (int)$row['can_delete'],
        ];
    }
}

// Reusable permission checker function
function has_permission($module, $action) {
    if (!isset($_SESSION['permissions'][$module])) {
        return false;
    }
    return !empty($_SESSION['permissions'][$module][$action]);
}


// Optional: access logged-in user's username and profile image
$username = $_SESSION['username'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? BASE_URL . '/assets/images/default_avatar.png';
?>
