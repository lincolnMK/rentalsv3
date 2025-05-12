<!-- Sidebar -->

<nav id="sidebar" class="col-auto col-md-2 bg-light vh-100 border-end d-flex flex-column px-2 px-md-3" style="min-width: 100px">
    <div class="d-flex flex-column py-4 px-3 ">
        <!-- System Name -->
        <h5 class="fw-semibold text-primary mb-4 mt-3">Rental_System</h5>
        
        <!-- Navigation Links -->
        <ul class="nav flex-column gap-2">
        <li class="nav-item">
    <a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?> d-flex align-items-center gap-2" href="index.php?page=home">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>

<?php
$users_pages = ['users', 'view_users', 'add_user'];
$property_pages = ['property', 'add_property'];
$landlord_pages = ['view_landlords', 'add_landlord'];
$occupant_pages = ['occupants', 'add_occupant'];
$payments_pages = ['payments', 'add_payment'];
$tenancy_pages = ['tenancy', 'add_tenancy'];
$leases_pages = ['leases', 'add_lease'];
$reports_pages = ['reports', 'export_reports'];
?>

<li class="nav-item">
    <div class="d-flex align-items-center justify-content-between">
        <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=users">
            <i class="fas fa-users"></i> Users
        </a>
        <button class="btn btn-sm dropdown-arrow-btn" type="button"
            data-bs-toggle="collapse" 
            data-bs-target="#usersDropdown" 
            aria-expanded="<?php echo in_array($page, $users_pages) ? 'true' : 'false'; ?>" 
            aria-controls="usersDropdown">
            <i class="fas fa-chevron-right dropdown-arrow"></i>
        </button>
    </div>

    <ul class="collapse <?php echo in_array($page, $users_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="usersDropdown">
        <li><a class="nav-link <?php echo ($page == 'view_users') ? 'active' : ''; ?>" href="index.php?page=view_users">View Users</a></li>
        <li><a class="nav-link <?php echo ($page == 'add_user') ? 'active' : ''; ?>" href="index.php?page=add_user">Add User</a></li>
    </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=property">
      <i class="fas fa-home"></i> Property
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#propertyDropdown" aria-expanded="<?php echo in_array($page, $property_pages) ? 'true' : 'false'; ?>" aria-controls="propertyDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $property_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="propertyDropdown">
    <li><a class="nav-link <?php echo ($page == 'property') ? 'active' : ''; ?>" href="index.php?page=property">View Properties</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_property') ? 'active' : ''; ?>" href="index.php?page=add_property">Add Property</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=view_landlords">
      <i class="fas fa-user-tie"></i> Landlords
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#landlordDropdown" aria-expanded="<?php echo in_array($page, $landlord_pages) ? 'true' : 'false'; ?>" aria-controls="landlordDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $landlord_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="landlordDropdown">
    <li><a class="nav-link <?php echo ($page == 'view_landlords') ? 'active' : ''; ?>" href="index.php?page=view_landlords">View Landlords</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_landlord') ? 'active' : ''; ?>" href="index.php?page=add_landlord">Add Landlord</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=occupants">
      <i class="fas fa-user-friends"></i> Occupants
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#occupantsDropdown" aria-expanded="<?php echo in_array($page, $occupant_pages) ? 'true' : 'false'; ?>" aria-controls="occupantsDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $occupant_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="occupantsDropdown">
    <li><a class="nav-link <?php echo ($page == 'occupants') ? 'active' : ''; ?>" href="index.php?page=occupants">View Occupants</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_occupant') ? 'active' : ''; ?>" href="index.php?page=add_occupant">Add Occupant</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=payments">
      <i class="fas fa-money-check-alt"></i> Payments
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#paymentsDropdown" aria-expanded="<?php echo in_array($page, $payments_pages) ? 'true' : 'false'; ?>" aria-controls="paymentsDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $payments_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="paymentsDropdown">
    <li><a class="nav-link <?php echo ($page == 'payments') ? 'active' : ''; ?>" href="index.php?page=payments">View Payments</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_payment') ? 'active' : ''; ?>" href="index.php?page=add_payment">Record Payment</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=tenancy">
      <i class="fas fa-file-signature"></i> Tenancy
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#tenancyDropdown" aria-expanded="<?php echo in_array($page, $tenancy_pages) ? 'true' : 'false'; ?>" aria-controls="tenancyDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $tenancy_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="tenancyDropdown">
    <li><a class="nav-link <?php echo ($page == 'tenancy') ? 'active' : ''; ?>" href="index.php?page=tenancy">View Tenancy</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_tenancy') ? 'active' : ''; ?>" href="index.php?page=add_tenancy">Create Tenancy</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=leases">
      <i class="fas fa-file-contract"></i> Leases
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#leasesDropdown" aria-expanded="<?php echo in_array($page, $leases_pages) ? 'true' : 'false'; ?>" aria-controls="leasesDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $leases_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="leasesDropdown">
    <li><a class="nav-link <?php echo ($page == 'leases') ? 'active' : ''; ?>" href="index.php?page=leases">View Leases</a></li>
    <li><a class="nav-link <?php echo ($page == 'add_lease') ? 'active' : ''; ?>" href="index.php?page=add_lease">Add Lease</a></li>
  </ul>
</li>

<li class="nav-item">
  <div class="d-flex align-items-center justify-content-between">
    <a class="nav-link d-flex align-items-center gap-2" href="index.php?page=reports">
      <i class="fas fa-chart-bar"></i> Reports & Analytics
    </a>
    <button class="btn btn-sm dropdown-arrow-btn" data-bs-toggle="collapse" data-bs-target="#reportsDropdown" aria-expanded="<?php echo in_array($page, $reports_pages) ? 'true' : 'false'; ?>" aria-controls="reportsDropdown">
      <i class="fas fa-chevron-right dropdown-arrow"></i>
    </button>
  </div>
  <ul class="collapse <?php echo in_array($page, $reports_pages) ? 'show' : ''; ?> list-unstyled ps-4" id="reportsDropdown">
    <li><a class="nav-link <?php echo ($page == 'reports') ? 'active' : ''; ?>" href="index.php?page=reports">View Reports</a></li>
    <li><a class="nav-link <?php echo ($page == 'export_reports') ? 'active' : ''; ?>" href="export_reports.php">Export Reports</a></li>
  </ul>
</li>
        </ul>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const arrowButtons = document.querySelectorAll('.dropdown-arrow-btn');

    arrowButtons.forEach(button => {
      const targetId = button.getAttribute('data-bs-target');
      const targetCollapse = document.querySelector(targetId);
      const arrowIcon = button.querySelector('.dropdown-arrow');

      if (targetCollapse.classList.contains('show')) {
        button.setAttribute('aria-expanded', 'true');
        arrowIcon.classList.add('rotate');
      }

      targetCollapse.addEventListener('show.bs.collapse', () => {
        button.setAttribute('aria-expanded', 'true');
      });

      targetCollapse.addEventListener('hide.bs.collapse', () => {
        button.setAttribute('aria-expanded', 'false');
      });
    });
  });
</script>

    </div>
</nav>
