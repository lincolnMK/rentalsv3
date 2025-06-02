<!-- Top Bar -->
 <?php if (!$full_name) {
    $full_name = "Guest";
} else {
    $full_name = htmlspecialchars($full_name);
}
?>
<div class="row bg-dark text-white py-2 align-items-center flex-nowrap" style="height: 60px;">
    <div class="col d-flex align-items-center" style="min-width: 200px;">
        <img src="assets/images/logo.png" alt="System Logo" class="img-fluid me-2" style="height: 45px;">
        <span class="fs-5 fw-bold text-truncate">Ministry of Lands</span>
    </div>
    <div class="col-auto text-end" style="min-width: 220px;">
        <!-- User Dropdown -->
        <div class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-none d-lg-inline text-white small text-truncate"><?= htmlspecialchars($full_name); ?></span>
                <?php
                    if (!empty($profile_picture)) {
                        echo '<img class="img-profile rounded-circle img-fluid ms-2" src="' . htmlspecialchars($profile_picture) . '" alt="User Profile" style="width: 30px; height: 30px;">';
                    } else {
                        echo '<img class="img-profile rounded-circle img-fluid ms-2" src="assets/images/default_avatar.png" alt="0" style="width: 30px; height: 30px;">';
                    }
                ?>
            </a>
            <!-- Dropdown - User Information -->
            <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>Settings</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>Activity Log</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</div>
