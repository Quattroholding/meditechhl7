<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit mb-3">
            <h4>
                <i class="fe fe-trending-up me-2"></i>
                Ingresos - {{ now()->format('F Y') }}
            </h4>
            <div>
                <span class="badge bg-success">Total: ${{ number_format($total_month, 2) }}</span>
            </div>
        </div>
        
        <canvas id="revenueChart-{{ $this->getId() }}" height="200" wire:ignore></canvas>
        
        <div class="mt-2 text-muted text-end">
            <small>Actualizado automáticamente cada 5 minutos</small>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('chartUpdated', (data) => {
            const chartId = 'revenueChart-' + data.componentId;
            const ctx = document.getElementById(chartId);
            
            if (ctx) {
                // Destroy existing chart if it exists
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                // Create new chart
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ingresos ($)',
                            data: data.data,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgb(75, 192, 192)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Ingresos: $' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toFixed(0);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    });
    
    // Initial chart render
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const ctx = document.getElementById('revenueChart-{{ $this->getId() }}');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Ingresos ($)',
                            data: @json($data),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgb(75, 192, 192)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Ingresos: $' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toFixed(0);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }, 100);
    });
</script>
@endpush
