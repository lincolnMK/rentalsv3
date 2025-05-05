
<?php
// Define all the periods for 2025
$all_periods = [
    "Jan 2025", "Feb 2025", "Mar 2025", "Apr 2025", "May 2025", 
    "Jun 2025", "Jul 2025", "Aug 2025", "Sep 2025", "Oct 2025", 
    "Nov 2025", "Dec 2025"
];

// Query to get approved payments for each period (string format)
$approved_payments_period = "
    SELECT period, COUNT(*) AS approved_payments_bar
    FROM payments 
    WHERE approval_status = 'Approved'
    GROUP BY period
    ORDER BY FIELD(period, 'Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 
                    'Jun 2025', 'Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 
                    'Nov 2025', 'Dec 2025')
";

$result_bar = $conn->query($approved_payments_period);

// Initialize arrays for periods and approved payments
$approved_payments_bar = array_fill(0, count($all_periods), 0);

// Map results to the correct periods
while ($row = $result_bar->fetch_assoc()) {
    $index = array_search($row['period'], $all_periods);
    if ($index !== false) {
        $approved_payments_bar[$index] = (int) $row['approved_payments_bar'];
    }
}
?>

