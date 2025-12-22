<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-chart-bar text-success me-2"></i>
            Estado de Facturas
        </h5>
    </div>
    <div class="card-body">
        @if(empty($counts))
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay facturas disponibles</p>
            </div>
        @else
            <canvas id="invoiceStatusChart" height="80"></canvas>
        @endif
    </div>
</div>

@if(!empty($counts))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('invoiceStatusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Cantidad',
                            data: @json($counts),
                            backgroundColor: @json($colors),
                            yAxisID: 'y'
                        },
                        {
                            label: 'Monto Total',
                            data: @json($amounts),
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            yAxisID: 'y1',
                            type: 'line'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 1) {
                                        label += '$' + context.parsed.y.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    } else {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Monto ($)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$&,');
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
