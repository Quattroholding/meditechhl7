<div class="card patient-structure h-96">
    <div class="card-body">
        @if($loading)
            <div class="loading-skeleton">
                <div class="skeleton-title mb-2"></div>
                <div class="skeleton-number mb-3"></div>
                <div class="skeleton-filters mb-3"></div>
                @if($filter !== 'daily')
                    <div class="skeleton-chart"></div>
                @endif
            </div>
        @else
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="text-base mb-1">Duración Promedio de Consultas</h5>
                    <small class="text-muted">Tiempo real de consulta (inicio → fin)</small>
                </div>
            </div>

            <h3 class="text-success mb-3">
                {{ $averageDuration }}
                <small class="text-muted fs-6">min</small>
            </h3>

            <div class="btn-group btn-group-sm mb-3" role="group">
                <button type="button"
                        class="btn {{ $filter === 'daily' ? 'btn-success' : 'btn-outline-success' }}"
                        wire:click="$set('filter', 'daily')"
                        wire:loading.attr="disabled">
                    Hoy
                </button>
                <button type="button"
                        class="btn {{ $filter === 'weekly' ? 'btn-success' : 'btn-outline-success' }}"
                        wire:click="$set('filter', 'weekly')"
                        wire:loading.attr="disabled">
                    Semanal
                </button>
                <button type="button"
                        class="btn {{ $filter === 'monthly' ? 'btn-success' : 'btn-outline-success' }}"
                        wire:click="$set('filter', 'monthly')"
                        wire:loading.attr="disabled">
                    Mensual
                </button>
            </div>

            @if($filter !== 'daily' && count($chartData) > 0)
                <div class="chart-container">
                    <canvas id="durationChart-{{ $this->getId() }}" height="120"></canvas>
                </div>
            @endif

            <div class="mt-2">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i>
                    @if($filter === 'daily')
                        Datos de hoy
                    @elseif($filter === 'weekly')
                        Últimos 7 días
                    @else
                        Últimos 30 días
                    @endif
                </small>
            </div>
        @endif
    </div>

    @if(!$loading && $filter !== 'daily' && count($chartData) > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                let durationChart = null;
                const componentId = '{{ $this->getId() }}';

                function updateChart() {
                    const chartData = @json($chartData);

                    if (chartData.length === 0) {
                        return;
                    }

                    const ctx = document.getElementById('durationChart-' + componentId);
                    if (!ctx) {
                        return;
                    }

                    if (durationChart) {
                        durationChart.destroy();
                    }

                    const labels = chartData.map(item => item.label);
                    const values = chartData.map(item => item.value);

                    durationChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Duración Promedio (minutos)',
                                data: values,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(75, 192, 192)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' minutos';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value + ' min';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Initial chart render
                setTimeout(() => updateChart(), 500);

                // Update chart on component updates
                Livewire.hook('morph.updated', ({ component, el }) => {
                    if (component.id === componentId) {
                        setTimeout(() => updateChart(), 300);
                    }
                });
            });
        </script>
        @endpush
    @endif

    <style>
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-title {
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80%;
        }

        .skeleton-number {
            height: 36px;
            background: #e9ecef;
            border-radius: 4px;
            width: 50%;
        }

        .skeleton-filters {
            height: 32px;
            background: #e9ecef;
            border-radius: 4px;
            width: 70%;
        }

        .skeleton-chart {
            height: 120px;
            background: #e9ecef;
            border-radius: 4px;
            width: 100%;
        }

        .chart-container {
            position: relative;
            margin-top: 1rem;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</div>
