<?php
include_once 'config.php';
include('session_check.php'); 
//include_once 'auth_check.php';
include_once 'db_connection.php';
define('ALLOW_PAGE_ACCESS', true);

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

// Get page from URL, default to 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';


// Determine the correct content file and title
switch ($page) {
    case 'users':
        $page_title = 'User Management';
        $page_file = 'view_users_copy.php';
        break;

    case 'view_users':
        $page_title = 'View Users';
        $page_file = 'view_users_copy.php';
        break;

    case 'add_user':
        $page_title = 'Add User';
        $page_file = 'add_user.php';
        break;

    case 'user_details':
        $page_title = 'User Details';
        $page_file = 'user_details.php';
        break;
    
    case 'edit_user':
        $page_title = 'Edit User';
        $page_file = 'edit_user.php';
        break;

    case 'home':
    default:
        $page_title = 'Homepage';
        $page_file = 'homepage_copy.php';
        break;

    case 'property':
        $page_title = 'View Properties';
        $page_file = 'view_properties.php';
        break;
    case 'add_property':
        $page_title = 'Add Properties';
        $page_file = 'add_property.php';
        break;
    
    case 'property_details':
        $page_title = 'Property Details';
        $page_file ='property_details.php';
        break;

    case 'edit_property':
        $page_title = 'Edit Property';
        $page_file = 'edit_property.php';
        break;
    
    case 'view_landlords':
        $page_title = 'View Landlords';
        $page_file = 'view_landlords.php';
        break;
    
    case 'landlord_details':
        $page_title = 'Landlord Details';
        $page_file = 'landlord_details.php';
        break;

    case 'edit_landlord':
        $page_title = 'Edit Landlord';
        $page_file = 'edit_landlord.php';
        break;
        
    case 'add_landlord':
        $page_title = 'Add Landlord';
        $page_file = 'add_landlord.php';
        break;
    
    case 'occupants':
        $page_title = 'View_occupants';
        $page_file = 'view_occupants.php';
        break;

    case 'occupant_details':
        $page_title = 'Occupant Details';
        $page_file = 'occupant_details.php';
        break;

    case 'add_occupant':
        $page_title = 'Add Occupant';
        $page_file = 'add_occupant.php';
        break;
    
    case 'edit_occupant':
        $page_title = 'Edit Occupant';
        $page_file = 'edit_occupant.php';
        break;

    case 'leases':
        $page_title = 'View Leases';
        $page_file = 'view_leases.php';
        break;

    case 'add_lease':
        $page_title = 'Add Lease';
        $page_file = 'add_lease.php';
        break;
    
    case 'tenancy':
        $page_title = 'View Tenancy';
        $page_file = 'view_tenancy.php';
        break;
        
    case 'tenancy_details':
        $page_title = 'Tenancy Details';
        $page_file = 'tenancy_details.php';
        break;
    
    case 'add_tenancy':
        $page_title = 'Add Tenancy';
        $page_file = 'add_tenancy.php';
        break;
    
    case 'reports':
        $page_title = 'Reports';
        $page_file = 'view_reports.php';
        break;
        
    case 'rental_analysis':
        $page_title = 'Rental Analysis';
        $page_file = 'rental_analysis.php';
        break;
    
        case 'payments':
        $page_title = 'View Payments';
        $page_file = 'view_payments.php';
        break;

    case 'add_payment':
        $page_title = 'Add Payment';
        $page_file = 'add_payment.php';
        break;
    
    case 'capture_payment':
        $page_title = 'Capture payment';
        $page_file = 'step3.php';
        break;

    case 'submit_payment':
        $page_title = 'Submit payment';
        $page_file = 'submit_payment.php';
        break;

    case 'payment_success':
        $page_title = 'payment Success';
        $page_file = 'payment_success.php';
        break;
    case 'login_log':
        $page_title = 'Login Audit Log';
        $page_file = 'audit_log.php';
        break;  

    case 'save_permissions':
        $page_title = 'Save Permissions';
        $page_file = 'save_permissions.php';
        break;

    case 'edit_permissions':
        $page_title = 'Edit Permissions';
        $page_file = 'edit_permissions.php';
        break;

    case 'maintenance':
        $page_title = 'maintenance';
        $page_file = 'view_maintenance.php';
        break;
    case 'maintenance_details':
        $page_title = 'maintenance Details';
        $page_file = 'maintenance_details.php';
        break;

    case 'add_maintenance':
        $page_title = 'Add Maintenance';
        $page_file = 'add_maintenance.php';
        break;

    case 'edit_maintenance':
        $page_title = 'Edit maintenance';
        $page_file = 'edit_maintenance.php';
        break;

    case 'view_maintenance':
        $page_title = 'View maintenance';
        $page_file = 'view_maintenance.php';
        break;

    case 'change_pass':
        $page_title = 'Change Password';
        $page_file = 'change_pass.php';
        break;
    

}


// Below your switch() statement
$page_permissions = [
    'users'            => ['module' => 'Users', 'action' => 'can_view'],
    'view_users'       => ['module' => 'Users', 'action' => 'can_view'],
    'add_user'         => ['module' => 'Users', 'action' => 'can_add'],
    'edit_user'        => ['module' => 'Users', 'action' => 'can_edit'],
    'user_details'     => ['module' => 'Users', 'action' => 'can_view'],
    'change_pass'      => ['module' => 'Users', 'action' => 'can_edit'],

    'property'         => ['module' => 'Properties', 'action' => 'can_view'],
    'add_property'     => ['module' => 'Properties', 'action' => 'can_add'],
    'edit_property'    => ['module' => 'Properties', 'action' => 'can_edit'],
    'property_details' => ['module' => 'Properties', 'action' => 'can_view'],

    'view_landlords'   => ['module' => 'Landlords', 'action' => 'can_view'],
    'add_landlord'     => ['module' => 'Landlords', 'action' => 'can_add'],
    'edit_landlord'    => ['module' => 'Landlords', 'action' => 'can_edit'],
    'landlord_details' => ['module' => 'Landlords', 'action' => 'can_view'],

    'occupants'        => ['module' => 'Occupants', 'action' => 'can_view'],
    'add_occupant'     => ['module' => 'Occupants', 'action' => 'can_add'],
    'edit_occupant'    => ['module' => 'Occupants', 'action' => 'can_edit'],
    'occupant_details' => ['module' => 'Occupants', 'action' => 'can_view'],

    'leases'           => ['module' => 'Leases', 'action' => 'can_view'],
    'add_lease'        => ['module' => 'Leases', 'action' => 'can_add'],

    'tenancy'          => ['module' => 'Tenancy', 'action' => 'can_view'],
    'add_tenancy'      => ['module' => 'Tenancy', 'action' => 'can_add'],
    'tenancy_details'  => ['module' => 'Tenancy', 'action' => 'can_view'],

    'payments'         => ['module' => 'Payments', 'action' => 'can_view'],
    'add_payment'      => ['module' => 'Payments', 'action' => 'can_add'],
    'capture_payment'  => ['module' => 'Payments', 'action' => 'can_add'],
    'submit_payment'   => ['module' => 'Payments', 'action' => 'can_add'],
    'payment_success'  => ['module' => 'Payments', 'action' => 'can_add'],

    'reports'          => ['module' => 'Reports', 'action' => 'can_view'],

    'login_log'        => ['module' => 'Audit Log', 'action' => 'can_view'],

    'save_permissions' => ['module' => 'Permissions', 'action' => 'can_edit'],
    'edit_permissions' => ['module' => 'Permissions', 'action' => 'can_edit'],

    'maintenance'       => ['module' => 'Maintenance', 'action' => 'can_view'],
    'maintenance_details' => ['module' => 'Maintenance', 'action' => 'can_view'],
    'add_maintenance' => ['module' => 'Maintenance', 'action' => 'can_add'],
    'edit_maintenance' => ['module' => 'Maintenance', 'action' => 'can_edit'],
    'view_maintenance' => ['module' => 'Maintenance', 'action' => 'can_view'],

];



?>

<?php include('assets/templates/header.php'); ?>

<div class="container-fluid main-container">
    <div class="row flex-nowrap">
        <?php include('assets/templates/sidebar.php'); ?>

        <main id="mainContent" class="col px-3">
            <?php 
               $path = "pages/$page_file";
if (file_exists($path)) {
    // Check permission
    if (isset($page_permissions[$page])) {
        $required = $page_permissions[$page];
       if (!has_permission($required['module'], $required['action'])) {
    echo "<div class='alert alert-danger'>You do not have permission to access this page.</div>";
    include('assets/templates/footer.php');
    exit;

    if (!isset($page_permissions[$page])) {
    echo "<div class='alert alert-danger'>Access not allowed.</div>";
    
    exit;
}

}

    }

    include($path); 
} else {
    echo "<p class='text-danger'>Page not found.</p>";
}

            ?>
        </main>
    </div>
</div>

<?php include('assets/templates/footer.php'); ?>
