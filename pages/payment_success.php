<?php
include_once __DIR__ . '/../auth_check.php';
?>

<h2>Payment Submitted</h2>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<a href="index.php?page=add_payment" class="btn btn-primary">Record Another Payment</a>
