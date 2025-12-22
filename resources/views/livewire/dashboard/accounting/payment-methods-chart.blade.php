<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-chart-pie text-info me-2"></i>
            Métodos de Pago
        </h5>
    </div>
    <div class="card-body">
        @if(empty($data))
            <div class="text-center py-5">
                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay datos disponibles</p>
            </div>
        @else
            <canvas id="paymentMethodsChart" height="200"></canvas>
        @endif
    </div>
</div>

@if(!empty($data))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paymentMethodsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        data: @json($data),
                        backgroundColor: @json($colors)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = '$' + context.parsed.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endif
