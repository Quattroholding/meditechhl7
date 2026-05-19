<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit mb-3">
            <h4>
                <i class="fe fe-dollar-sign me-2"></i>
                Ingresos por Suscripciones - Últimos 30 Días
            </h4>
            <div>
                <span class="badge bg-primary">Total: ${{ number_format($total_month, 2) }}</span>
            </div>
        </div>

        <canvas id="subscriptionRevenueChart-{{ $this->getId() }}" height="200" wire:ignore></canvas>

        <div class="mt-2 text-muted text-end">
            <small>Actualizado automáticamente cada 5 minutos</small>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const ctx = document.getElementById('subscriptionRevenueChart-{{ $this->getId() }}');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Ingresos por Suscripciones ($)',
                            data: @json($data),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 2,
                            borderRadius: 5,
                            hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)',
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
