<?php

include_once __DIR__ . '/../auth_check.php';


$step = $_POST['step'] ?? 1;
$landlords = [];
$properties = [];

// Step 1a: Search for landlord
if ($step == 1 && isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    $like = "%$keyword%";
    $stmt = $conn->prepare("
        SELECT
            l.name AS name,
            l.landlord_id AS id,
            l.Phone_Number,
            p.Property_ID,
            p.Plot_number,
            p.District
        FROM landlord l
        LEFT JOIN property p ON l.landlord_id = p.landlord_id
        WHERE l.name LIKE ? OR p.plot_number LIKE ?
    ");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $landlords = $result->fetch_all(MYSQLI_ASSOC);
}

// Step 1b: Landlord is selected
if ($step == 1 && isset($_POST['select_landlord'])) {
    $_SESSION['landlord_id'] = $_POST['landlord_id'];
    $step = 2;


     // Fetch and store landlord name
    $stmt = $conn->prepare("SELECT name FROM landlord WHERE landlord_id = ?");
    $stmt->bind_param("i", $_SESSION['landlord_id']);
    $stmt->execute();
    $stmt->bind_result($landlord_name);
    $stmt->fetch();
    $_SESSION['landlord_name'] = $landlord_name; // store in session
    $stmt->close();


    $stmt = $conn->prepare("SELECT * FROM property WHERE landlord_id = ?");
    $stmt->bind_param("i", $_SESSION['landlord_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);
}
?>


<h2>Tenancy Payment Form</h2>

<?php if ($step == 1): ?>
    <h3 class="mb-4">Step 1: Search for Landlord</h3>
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="step" value="1">
        <div class="col-md-6">
            <input type="text" name="keyword" class="form-control" placeholder="Enter landlord name or plot number" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="search" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if (!empty($landlords)): ?>
        <h5 class="mb-3">Search Results:</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($landlords as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['name']) ?></td>
                        <td><?= htmlspecialchars($l['Phone_Number']) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="step" value="1">
                                <input type="hidden" name="select_landlord" value="1">
                                <input type="hidden" name="landlord_id" value="<?= $l['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Select</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach;
                
                ?>

                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($step == 2): ?>
     <h3 class="mb-4">Step 2: Select Property for Landlord: <?= htmlspecialchars($_SESSION['landlord_name'] ?? 'Unknown') ?></h3>


    <?php if (!empty($properties)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Property ID</th>
                        <th>Plot Number</th>
                        <th>District</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($properties as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['Property_ID']) ?></td>
                        <td><?= htmlspecialchars($p['Plot_number']) ?></td>
                        <td><?= htmlspecialchars($p['District']) ?></td>
                        <td>
                            <form method="POST" action="index.php?page=capture_payment&property_id=<?= $p['Property_ID'] ?>" class="d-inline">
                                <input type="hidden" name="property_id" value="<?= $p['Property_ID'] ?>">
                                <input type="hidden" name="step" value="3">
                                <button type="submit" class="btn btn-primary btn-sm">Select</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No properties found for this landlord.</p>
    <?php endif; ?>
<?php endif; ?>
