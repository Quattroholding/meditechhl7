<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit mb-3">
            <h4>
                <i class="fe fe-package me-2"></i>
                Suscripciones Activas por Paquete
            </h4>
            <div>
                <span class="badge bg-success">Total: {{ $total }}</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <canvas id="packageChart-{{ $this->getId() }}" height="250" wire:ignore></canvas>
            </div>

            <div class="col-md-6">
                @foreach($labels as $index => $label)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <i class="fa fa-circle me-2" style="color: {{ $colors[$index] }}"></i>
                            <span>{{ $label }}</span>
                        </div>
                        <div>
                            <span class="badge" style="background-color: {{ $colors[$index] }}">{{ $data[$index] }}</span>
                            @if($total > 0)
                            <small class="text-muted ms-2">
                                {{ number_format(($data[$index] / $total) * 100, 1) }}%
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
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
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const ctx = document.getElementById('packageChart-{{ $this->getId() }}');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            data: @json($data),
                            backgroundColor: @json($colors),
                            borderColor: @json($colors),
                            borderWidth: 2,
                            hoverOffset: 10
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
