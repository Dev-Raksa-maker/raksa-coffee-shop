document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('myChart');

    if (ctx) {

        const revenueData = window.dynamicRevenue || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        const expensesData = window.dynamicExpenses || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    backgroundColor: '#2EBA7F',
                    borderRadius: 5,
                    maxBarThickness: 15
                }, {
                    label: 'Expenses',
                    data: expensesData,
                    backgroundColor: '#D1F243',
                    borderRadius: 5,
                    maxBarThickness: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        stacked: false, // ដាក់ false ដើម្បីឱ្យសសរឈរក្បែរគ្នា ងាយស្រួលប្រៀបធៀប
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#efefef' },
                        ticks: {
                            callback: function (value) {
                                if (value >= 1000) {
                                    return '$' + (value / 1000) + 'k';
                                }
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
});