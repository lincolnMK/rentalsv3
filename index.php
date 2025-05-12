<?php



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
    
        case 'payments':
        $page_title = 'View Payments';
        $page_file = 'view_payments.php';
        break;

    case 'add_payment':
        $page_title = 'Add Payment';
        $page_file = 'add_payment.php';
        break;
    
}
?>

<?php include('assets/templates/header.php'); ?>

<div class="container-fluid main-container">
    <div class="row flex-nowrap">
        <?php include('assets/templates/sidebar.php'); ?>

        <main id="mainContent" class="col px-3">
            <?php 
                $path = "pages/$page_file";
                if (file_exists($path)) {
                    include($path); 
                } else {
                    echo "<p class='text-danger'>Page not found.</p>";
                }
            ?>
        </main>
    </div>
</div>

<?php include('assets/templates/footer.php'); ?>
