<?php 
include('db_connection.php');

include ('assets\charts_data\bar_chart.php');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Load Chart.js locally -->
    <script src="assets/js/chart.min.js"></script>
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
                <canvas id="paymentsChart_bar"></canvas>
            </div>
        </div>
    </div>

    <script>
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
