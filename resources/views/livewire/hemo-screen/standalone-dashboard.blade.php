<div >
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-primary">
                        <i class="fas fa-vial"></i>
                    </span>
                        <div class="dash-count">
                            <h3>{{ $totalResults }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Total de Pruebas</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </span>
                        <div class="dash-count">
                            <h3>{{ $results->total() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Resultados Actuales</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-info">
                        <i class="fas fa-microscope"></i>
                    </span>
                        <div class="dash-count">
                            <h3>{{ $availableDevices->count() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Dispositivos</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                    <span class="dash-widget-icon bg-warning">
                        <i class="fas fa-calendar"></i>
                    </span>
                        <div class="dash-count">
                            <h3>{{ $chartDays }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Días de Historial</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Cards -->

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Dispositivo</label>
                    <select wire:model.live="deviceSerial" class="form-select">
                        <option value="">Todos los dispositivos</option>
                        @foreach($availableDevices as $device)
                            <option value="{{ $device }}">{{ $device }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" wire:model.live="showAbnormalOnly" class="form-check-input" id="abnormalOnly">
                        <label class="form-check-label" for="abnormalOnly">
                            Solo valores anormales
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo me-1"></i>Restablecer Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Filters Section -->

    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Resultados de Pruebas
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input"
                                   wire:click="$toggle('selectAll')"
                                   id="selectAll">
                        </th>
                        <th>Fecha y Hora</th>
                        <th>Dispositivo</th>
                        <th>Paciente</th>
                        <th>Panel</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input"
                                       wire:model.live="selectedResults"
                                       value="{{ $result->id }}">
                            </td>
                            <td>
                                <strong>{{ $result->test_performed_at->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ $result->test_performed_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <i class="fas fa-microscope me-1"></i>
                                    {{ $result->device_serial ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($result->patient_identifier)
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-user-tag me-1"></i>
                                        {{ $result->patient_identifier }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $result->panel_name }}</div>
                                <small class="text-muted">CPT {{ $result->panel_code }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ count($result->observations) }} parámetros
                                </span>
                            </td>
                            <td>
                                @if($result->hasAbnormalValues())
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Valores Anormales
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('hemoscreen.results.show', $result->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay resultados disponibles</h5>
                                    <p class="text-muted">Los resultados de tu dispositivo HemoScreen aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($results->hasPages())
            <div class="card-footer">
                {{ $results->links() }}
            </div>
        @endif
    </div>
    <!-- /Results Table -->

    <!-- Chart Section -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Tendencia
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <select wire:model.live="chartParameter" class="form-select form-select-sm">
                                @foreach($chartParameters as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select wire:model.live="chartDays" class="form-select form-select-sm">
                                <option value="7">Últimos 7 días</option>
                                <option value="14">Últimos 14 días</option>
                                <option value="30">Últimos 30 días</option>
                                <option value="60">Últimos 60 días</option>
                                <option value="90">Últimos 90 días</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="trendChart" height="80"></canvas>
        </div>
    </div>
    <!-- /Chart Section -->

    <!-- Export Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <button wire:click="exportToPdf" class="btn btn-primary me-2"
                            @if(empty($selectedResults)) disabled @endif>
                        <i class="fas fa-file-pdf me-1"></i>Exportar Seleccionados a PDF
                    </button>
                    <button wire:click="exportToCsv" class="btn btn-success">
                        <i class="fas fa-file-csv me-1"></i>Exportar Todo a CSV
                    </button>
                    @if(!empty($selectedResults))
                        <span class="ms-3 text-muted">
                        ({{ count($selectedResults) }} seleccionados)
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /Export Actions -->

    <p>&nbsp;</p>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                let chart = null;

                function renderChart() {
                    const chartData = @json($chartData);
                    const ctx = document.getElementById('trendChart');

                    if (!ctx) return;

                    // Destroy existing chart
                    if (chart) {
                        chart.destroy();
                    }

                    // Create new chart
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: '{{ $chartParameters[$chartParameter] ?? "Valor" }}',
                                data: chartData.values,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1,
                                fill: true
                            },
                                // Reference range min line
                                chartData.referenceMin ? {
                                    label: 'Rango Min',
                                    data: Array(chartData.labels.length).fill(chartData.referenceMin),
                                    borderColor: 'rgba(255, 99, 132, 0.5)',
                                    borderDash: [5, 5],
                                    borderWidth: 1,
                                    pointRadius: 0,
                                    fill: false
                                } : null,
                                // Reference range max line
                                chartData.referenceMax ? {
                                    label: 'Rango Max',
                                    data: Array(chartData.labels.length).fill(chartData.referenceMax),
                                    borderColor: 'rgba(255, 99, 132, 0.5)',
                                    borderDash: [5, 5],
                                    borderWidth: 1,
                                    pointRadius: 0,
                                    fill: false
                                } : null].filter(Boolean)
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Initial render
                renderChart();

                // Re-render on Livewire updates
                Livewire.on('chartUpdated', () => {
                    renderChart();
                });

                // Watch for parameter changes
                Livewire.hook('message.processed', (message, component) => {
                    if (component.id === @this.__instance.id) {
                        renderChart();
                    }
                });
            });
        </script>
    @endpush

</div>
