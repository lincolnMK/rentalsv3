
<?php
include_once __DIR__ . '/../auth_check.php';

if (!isset($_GET['occupant_id'])) {
    die('Occupant ID not specified.');
}

$occupantId = (int) $_GET['occupant_id'];

// --- Fetch occupant details ---
$sql = "SELECT 
            occupant_id,
            Name,
            contact,
            email,
            description,
            mda_id
FROM occupants WHERE occupant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $occupantId);
$stmt->execute();
$result = $stmt->get_result();
$occupant = $result->fetch_assoc();

if (!$occupant) {
    die('Occupant not found.');
}

// --- Generate CSRF token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- Fetch MDA list ---
$mdaResult = $conn->query("SELECT MDA_ID, MDA_name FROM mda");
if (!$mdaResult) {
    die('Error fetching MDA list: ' . $conn->error);
}
$mda = $mdaResult->fetch_all(MYSQLI_ASSOC);

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }

    $occupantName = $_POST['Name'] ?? '';
    $occupantPhone = $_POST['contact'] ?? '';
    $occupantEmail = $_POST['email'] ?? '';
    $occupantMdaId = (int) ($_POST['mda_id'] ?? 0);
    $occupantDescription = $_POST['description'] ?? '';

    $updateSql = "
        UPDATE occupants 
        SET Name = ?, contact = ?, email = ?, mda_id = ?, description = ?
        WHERE occupant_id = ?
    ";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param(
        "sssisi",
        $occupantName,
        $occupantPhone,
        $occupantEmail,
        $occupantMdaId,
        $occupantDescription,
        $occupantId
    );

    if ($updateStmt->execute()) {
        $idEscaped = (int) $occupantId;  // ensure it’s safe for JS
        echo "<script>
            window.location.href = 'index.php?page=occupant_details&Occupant_ID={$idEscaped}&updated=1';
        </script>";
        $updateStmt->close();
        $stmt->close();
        $conn->close();
        exit;
    } else {
        echo "Error updating record: " . htmlspecialchars($updateStmt->error);
        $updateStmt->close();
    }
}

// Free resources if no POST update occurred
$stmt->close();
$conn->close();
?>



<div class="row bg-white py-3 shadow-sm">
    <div class="col">
        <h4 class="text-primary">Edit Occupant Details</h4>
    </div>
</div>

<div class="row p-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="Name" class="form-label">Occupant Name</label>
                        <input type="text" class="form-control" id="Name" name="Name" value="<?= htmlspecialchars($occupant['Name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Occupant Phone</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($occupant['contact'] ?? ''); ?>" required>       
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Occupant Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($occupant['email'] ?? ''); ?>" required>  
                    </div>
                    <div class="mb-3">
                        <label for="mda_id" class="form-label">MDA</label>
                        <select class="form-select" id="mda_id" name="mda_id" required>
                            <option value="0">--Select MDA--</option>
                            <?php foreach ($mda as $mdaItem): ?>
                                <option value="<?= $mdaItem['MDA_ID']; ?>" <?= ($occupant['mda_id'] == $mdaItem['MDA_ID']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($mdaItem['MDA_name'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($occupant['description'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Occupant</button>
                    <a href="index.php?page=occupant_details&Occupant_ID= <?= $occupantId?>" class="btn btn-secondary ms-2">Cancel</a>
                    
                </form>
                
            </div>
            
        </div>
       
    </div>
</div>
