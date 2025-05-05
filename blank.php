<?php
$page_title = 'Homepage';
include('assets/templates/header.php');
?>

<div class="container-fluid main-container">
    <div class="row flex-nowrap">
        <?php include('assets/templates/sidebar.php'); ?>

        <main id="mainContent" class="col px-3">
            <?php include('homepage_copy.php'); ?>
        </main>
    </div>
</div>

<?php include('assets/templates/footer.php'); ?>
