<script>
        // Data for the bar chart
        const data = {
            labels: <?= json_encode($all_periods); ?>,
            datasets: [{
                label: 'Approved Payments',
                data: <?= json_encode($approved_payments); ?>,
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
        const payments_bar_chart = new Chart(
            document.getElementById('payments_bar_chart'),
            config
        );
    </script>