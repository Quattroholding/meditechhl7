<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit mb-3">
            <h4>
                <i class="fe fe-pie-chart me-2"></i>
                Citas por Fuente - {{ now()->format('F Y') }}
            </h4>
            <div>
                <span class="badge bg-secondary">Total: {{ $total }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <canvas id="sourceChart-{{ $this->getId() }}" height="250" wire:ignore></canvas>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <i class="fa fa-circle text-primary me-2"></i>
                            <span>Web</span>
                        </div>
                        <span class="badge bg-primary">{{ $web }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <i class="fa fa-circle text-success me-2"></i>
                            <span>WhatsApp</span>
                        </div>
                        <span class="badge bg-success">{{ $whatsapp }}</span>
                    </div>
                </div>
                
                @if($total > 0)
                <div class="mt-3">
                    <small class="text-muted">
                        Web: {{ number_format(($web / $total) * 100, 1) }}% | 
                        WhatsApp: {{ number_format(($whatsapp / $total) * 100, 1) }}%
                    </small>
                </div>
                @endif
            </div>
        </div>
        
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
            const chartId = 'sourceChart-' + data.componentId;
            const ctx = document.getElementById(chartId);
            
            if (ctx) {
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                new Chart(ctx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',  // Blue for Web
                                'rgba(75, 192, 192, 0.8)',  // Green for WhatsApp
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
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
            const ctx = document.getElementById('sourceChart-{{ $this->getId() }}');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            data: @json($chartData),
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',  // Blue for Web
                                'rgba(75, 192, 192, 0.8)',  // Green for WhatsApp
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
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
