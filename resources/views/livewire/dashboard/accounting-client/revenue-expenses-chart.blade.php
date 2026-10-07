<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Ingresos vs Gastos (Últimos 6 Meses)</h5>
    </div>
    <div class="card-body">
        <canvas id="revenueExpensesChart"></canvas>
    </div>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const ctx = document.getElementById('revenueExpensesChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {{ json_encode($chartData) }},
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
</div>