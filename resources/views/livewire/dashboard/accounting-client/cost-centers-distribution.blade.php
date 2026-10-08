<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Distribución por Centros de Costo</h5>
    </div>
    <div class="card-body">
        @if(count($chartData['labels']) > 0)
            <canvas id="costCentersChart"></canvas>
            <script>
                document.addEventListener('livewire:navigated', () => {
                    const ctx = document.getElementById('costCentersChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {{ json_encode($chartData) }},
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
        @else
            <div class="text-center text-muted py-4">
                <p>No hay datos de distribución de costos</p>
            </div>
        @endif
    </div>
</div>