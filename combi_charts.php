<?php
// Include database connection
include('db_connection.php');
include ('assets\charts_data\bar_chart.php');

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
            <!-- Pie Chart -->
            <div class="col-md-6">
                <canvas id="pieChart"></canvas>
            </div>
            <!-- Bar Chart -->
            <div class="col-md-6">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Data for the pie chart
        const pieData = {
            labels: ['Approved Payments', 'Other Payments'],
            datasets: [{
                data: [<?= $approved_payments; ?>, <?= $total_payments - $approved_payments; ?>],
                backgroundColor: ['#36a2eb', '#ff6384'],
                hoverBackgroundColor: ['#36a2eb', '#ff6384']
            }]
        };

        // Configuration for the pie chart
        const pieConfig = {
            type: 'pie',
            data: pieData,
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

       

            // Data for the bar chart
            const data = {
            labels: <?= json_encode($all_periods); ?>,
            datasets: [{
                label: 'Approved Payments',
                data: <?= json_encode($approved_payments_bar); ?>,
                backgroundColor: '#36a2eb',
                hoverBackgroundColor: '#36a2eb'
            }]
        };

        // Configuration for the bar chart
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.raw || 0;
                                return 'Approved Payments: ' + value;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Period',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Approved Payments'
                        }
                    }
                }
            }
        };

        // Render the bar chart
        const paymentsChart_bar = new Chart(
            document.getElementById('paymentsChart_bar'),
            config
        );
    </script>
</body>
</html>
