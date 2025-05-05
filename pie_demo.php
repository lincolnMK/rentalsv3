<?php
// Include database connection
include('db_connection.php');

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="assets/bootstrap/js/Chart.min.js"></script>
   
</head>
<body>
    <div class="container-fluid">
        <div class="row bg-white py-3 shadow-sm">
            <div class="col">
                <h4 class="text-primary">Dashboard</h4>
            </div>
        </div>
        <div class="row p-4">
            <div class="col-md-12">
                <canvas id="paymentsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Data for the pie chart
        const data = {
            labels: ['Approved Payments', 'Other Payments'],
            datasets: [{
                data: [<?= $approved_payments; ?>, <?= $total_payments - $approved_payments; ?>],
                backgroundColor: ['#36a2eb', '#ff6384'],
                hoverBackgroundColor: ['#36a2eb', '#ff6384']
            }]
        };

        // Configuration for the pie chart
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart._metasets[0].total;
                                const percentage = (value / total * 100).toFixed(2) + '%';
                                return label + ': ' + value + ' (' + percentage + ')';
                            }
                        }
                    }
                }
            }
        };

        // Render the pie chart
        const paymentsChart = new Chart(
            document.getElementById('paymentsChart'),
            config
        );
    </script>
</body>
</html>
