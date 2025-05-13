<?php



// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div class='alert alert-danger'>Invalid access method.</div>";
    return;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>window.location.href='index.php?page=step1.php';</script>";
    exit;
}


// Validate required POST inputs
if (
    empty($_POST['tenancy_id']) ||
    empty($_POST['payment_date']) ||
    empty($_POST['receipt']) ||
    !isset($_POST['amount'])
) {
    echo "<div class='alert alert-danger'>Missing required payment information.</div>";
    exit;
}

// Sanitize and assign inputs
$tenancy_id = intval($_POST['tenancy_id']);
$payment_date = $_POST['payment_date'];
$receipt = trim($_POST['receipt']);
$amount = floatval($_POST['amount']);

// Calculate VAT (optional, you can hardcode or load from config)
$vat_rate = defined('VAT_RATE') ? VAT_RATE : 0.16; // 16% as example
$vat = $amount * $vat_rate;

// Prepare insert query
$stmt = $conn->prepare("
    INSERT INTO payments (Tenancy_ID, Payment_date, Receipt_Landlord, VAT, amount)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo "<div class='alert alert-danger'>Database error: " . $conn->error . "</div>";
    exit;
}

$stmt->bind_param("issdd", $tenancy_id, $payment_date, $receipt, $vat, $amount);
$success = $stmt->execute();

if ($success) {
    $_SESSION['success'] = "Payment recorded successfully.";
     echo "<script>window.location.href='index.php?page=payment_success';</script>";
     
   
    exit;
}


else {
    echo "<div class='alert alert-danger'>Failed to record payment: " . $stmt->error . "</div>";
}


$stmt->close();
?>
