<?php
// Include database connection


// Query to count total payments
$total_payments_query = "SELECT COUNT(*) AS total_payments FROM payments";
$result = $conn->query($total_payments_query);
$total_payments_row = $result->fetch_assoc();
$total_payments = $total_payments_row['total_payments'];

// Query to count approved payments
$approved_payments_query = "SELECT COUNT(*) AS approved_payments FROM payments WHERE approval_status = 'approved'";
$result = $conn->query($approved_payments_query);
$approved_payments_row = $result->fetch_assoc();
$approved_payments = $approved_payments_row['approved_payments'];

?>
