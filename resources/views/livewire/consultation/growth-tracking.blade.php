<div class="growth-tracking-section">
    <div class="row">
        {{-- Left Side: Measurement Form --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-ruler-combined me-2"></i>
                        Medición de Crecimiento
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Patient Info --}}
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Paciente:</strong> {{ $patient->name ?? 'N/A' }}<br>
                        <strong>Género:</strong> {{ $gender === 'male' ? 'Masculino' : 'Femenino' }} |
                        <strong>Edad:</strong> {{ $age_months }} meses ({{ floor($age_months / 12) }} años, {{ $age_months % 12 }} meses)
                    </div>

                    <form wire:submit.prevent="saveMeasurement">
                        {{-- Measurement Date --}}
                        <div class="mb-3">
                            <label class="form-label">Fecha de Medición <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('measurement_date') is-invalid @enderror"
                                   wire:model.live="measurement_date">
                            @error('measurement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Weight --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-weight me-1"></i>
                                Peso (kg)
                            </label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('weight_kg') is-invalid @enderror"
                                   wire:model.live.debounce.500ms="weight_kg"
                                   placeholder="0.00">
                            @error('weight_kg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if(isset($previewPercentiles['weight_for_age']))
                                <small class="text-success">
                                    <i class="fas fa-chart-line"></i>
                                    Percentil: {{ $previewPercentiles['weight_for_age']['percentile'] ?? 'N/A' }}
                                    (Z-score: {{ $previewPercentiles['weight_for_age']['zscore'] ?? 'N/A' }})
                                </small>
                            @endif
                        </div>

                        {{-- Height --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-arrows-alt-v me-1"></i>
                                {{ $age_months < 24 ? 'Longitud (cm)' : 'Estatura (cm)' }}
                            </label>
                            <input type="number"
                                   step="0.1"
                                   class="form-control @error('height_cm') is-invalid @enderror"
                                   wire:model.live.debounce.500ms="height_cm"
                                   placeholder="0.0">
                            @error('height_cm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if(isset($previewPercentiles['height_for_age']))
                                <small class="text-success">
                                    <i class="fas fa-chart-line"></i>
                                    Percentil: {{ $previewPercentiles['height_for_age']['percentile'] ?? 'N/A' }}
                                    (Z-score: {{ $previewPercentiles['height_for_age']['zscore'] ?? 'N/A' }})
                                </small>
                            @endif
                        </div>

                        {{-- Head Circumference (only for children under 24 months) --}}
                        @if($age_months <= 24)
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-circle-notch me-1"></i>
                                    Circunferencia Cefálica (cm)
                                </label>
                                <input type="number"
                                       step="0.1"
                                       class="form-control @error('head_circumference_cm') is-invalid @enderror"
                                       wire:model.live.debounce.500ms="head_circumference_cm"
                                       placeholder="0.0">
                                @error('head_circumference_cm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if(isset($previewPercentiles['head_circ_for_age']))
                                    <small class="text-success">
                                        <i class="fas fa-chart-line"></i>
                                        Percentil: {{ $previewPercentiles['head_circ_for_age']['percentile'] ?? 'N/A' }}
                                        (Z-score: {{ $previewPercentiles['head_circ_for_age']['zscore'] ?? 'N/A' }})
                                    </small>
                                @endif
                            </div>
                        @endif

                        {{-- BMI (calculated) --}}
                        @if($bmi)
                            <div class="mb-3">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="text-primary">
                                                    <i class="fas fa-calculator me-1"></i>
                                                    IMC Calculado:
                                                </strong>
                                                <span class="fs-5 ms-2">{{ $bmi }} kg/m²</span>
                                            </div>
                                        </div>
                                        @if(isset($previewPercentiles['bmi_for_age']))
                                            <small class="text-success d-block mt-2">
                                                <i class="fas fa-chart-line"></i>
                                                Percentil: {{ $previewPercentiles['bmi_for_age']['percentile'] ?? 'N/A' }}
                                                (Z-score: {{ $previewPercentiles['bmi_for_age']['zscore'] ?? 'N/A' }})
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" wire:model="note" rows="2"
                                      placeholder="Observaciones adicionales..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-save me-1"></i>
                                {{ $editingMeasurementId ? 'Actualizar' : 'Guardar' }} Medición
                            </button>
                            @if($editingMeasurementId)
                                <button type="button" class="btn btn-secondary" wire:click="resetForm">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Historical Measurements --}}
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historial de Mediciones
                    </h6>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    @forelse($measurements as $measurement)
                        <div class="list-group-item list-group-item-action" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1" wire:click="editMeasurement({{ $measurement->id }})">
                                    <div class="fw-bold">
                                        {{ $measurement->measurement_date->format('d/m/Y') }}
                                        <span class="badge bg-info ms-2">{{ $measurement->age_months }} meses</span>
                                    </div>
                                    <small class="text-muted">
                                        @if($measurement->weight_kg)
                                            Peso: {{ $measurement->weight_kg }} kg
                                            @if($measurement->weight_for_age_percentile)
                                                (P{{ $measurement->weight_for_age_percentile }})
                                            @endif
                                            •
                                        @endif
                                        @if($measurement->height_cm)
                                            {{ $measurement->age_months < 24 ? 'Long.' : 'Est.' }}: {{ $measurement->height_cm }} cm
                                            @if($measurement->height_for_age_percentile)
                                                (P{{ $measurement->height_for_age_percentile }})
                                            @endif
                                        @endif
                                    </small>
                                    @if($measurement->nutritional_status && $measurement->nutritional_status !== 'normal')
                                        <br>
                                        <span class="badge bg-warning text-dark">
                                            {{ ucfirst($measurement->nutritional_status) }}
                                        </span>
                                    @endif
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteMeasurement({{ $measurement->id }})"
                                        onclick="return confirm('¿Está seguro de eliminar esta medición?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No hay mediciones registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Side: Growth Charts --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Curvas de Crecimiento OMS
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Chart Type Selector --}}
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button"
                                wire:click="setActiveChart('weight_for_age')"
                                class="btn btn-sm {{ $activeChart === 'weight_for_age' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="fas fa-weight"></i> Peso/Edad
                        </button>
                        <button type="button"
                                wire:click="setActiveChart('height_for_age')"
                                class="btn btn-sm {{ $activeChart === 'height_for_age' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="fas fa-arrows-alt-v"></i> Talla/Edad
                        </button>
                        <button type="button"
                                wire:click="setActiveChart('bmi_for_age')"
                                class="btn btn-sm {{ $activeChart === 'bmi_for_age' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="fas fa-calculator"></i> IMC/Edad
                        </button>
                        @if($age_months <= 24)
                            <button type="button"
                                    wire:click="setActiveChart('head_circumference_for_age')"
                                    class="btn btn-sm {{ $activeChart === 'head_circumference_for_age' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-circle-notch"></i> CC/Edad
                            </button>
                        @endif
                    </div>

                    {{-- Chart Canvas --}}
                    <div class="chart-container" style="position: relative; height: 500px;"
                         x-data="{ chartInitialized: false }"
                         x-intersect.once="
                            if (!chartInitialized) {
                                console.log('📊 Sección visible, inicializando gráfico...');
                                setTimeout(() => {
                                    if (typeof initGrowthChart === 'function') {
                                        initGrowthChart();
                                        chartInitialized = true;
                                        // Forzar carga de datos del gráfico activo
                                        console.log('📈 Cargando datos del gráfico...');
                                        $wire.call('prepareChartData');
                                    }
                                }, 100);
                            }
                         ">
                        <canvas id="growthChart" wire:ignore></canvas>
                    </div>

                    {{-- Chart Legend --}}
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Percentiles OMS:</strong>
                            <span class="badge bg-danger ms-2">P3</span>
                            <span class="badge bg-warning ms-1">P10</span>
                            <span class="badge bg-secondary ms-1">P25</span>
                            <span class="badge bg-success ms-1">P50</span>
                            <span class="badge bg-secondary ms-1">P75</span>
                            <span class="badge bg-warning ms-1">P90</span>
                            <span class="badge bg-danger ms-1">P97</span>
                            <span class="badge bg-primary ms-2">● Paciente</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Cargar Chart.js primero --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Definir en el scope global para acceso desde Alpine.js
    window.growthChartInstance = null;

    window.initGrowthChart = function() {
        console.log('🎨 Intentando inicializar gráfico...');
        const ctx = document.getElementById('growthChart');

        if (!ctx) {
            console.error('❌ No se encontró el elemento canvas con id "growthChart"');
            return;
        }

        console.log('✅ Canvas encontrado:', ctx);

        if (window.growthChartInstance) {
            console.log('🔄 Destruyendo gráfico anterior...');
            window.growthChartInstance.destroy();
        }

        try {
            console.log('🎨 Creando nuevo gráfico Chart.js...');
            window.growthChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Edad (meses)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Valor'
                        }
                    }
                }
            }
        });

            console.log('✅ Gráfico creado exitosamente');
        } catch (error) {
            console.error('❌ Error al crear el gráfico:', error);
        }
    }

    window.updateGrowthChart = function(chartData) {
        console.log('🔄 Actualizando gráfico...');

        if (!window.growthChartInstance) {
            console.error('❌ window.growthChartInstance no está inicializado');
            return;
        }

        if (!chartData) {
            console.error('❌ chartData está vacío');
            return;
        }

        const whoCurves = chartData.who_curves || {};
        const patientData = chartData.patient_data || {};

        console.log('  - WHO curves ages:', whoCurves.ages?.length || 0);
        console.log('  - Patient data points:', patientData.ages?.length || 0);

        const chartTypeLabels = {
            'weight_for_age': 'Peso (kg)',
            'height_for_age': 'Talla (cm)',
            'bmi_for_age': 'IMC (kg/m²)',
            'head_circumference_for_age': 'Circunferencia Cefálica (cm)'
        };

        window.growthChartInstance.data.labels = whoCurves.ages || [];
        window.growthChartInstance.data.datasets = [
            {
                label: 'P3',
                data: whoCurves.p3 || [],
                borderColor: 'rgba(220, 53, 69, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P10',
                data: whoCurves.p10 || [],
                borderColor: 'rgba(255, 193, 7, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P25',
                data: whoCurves.p25 || [],
                borderColor: 'rgba(108, 117, 125, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P50 (Mediana)',
                data: whoCurves.p50 || [],
                borderColor: 'rgba(25, 135, 84, 0.7)',
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P75',
                data: whoCurves.p75 || [],
                borderColor: 'rgba(108, 117, 125, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P90',
                data: whoCurves.p90 || [],
                borderColor: 'rgba(255, 193, 7, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'P97',
                data: whoCurves.p97 || [],
                borderColor: 'rgba(220, 53, 69, 0.5)',
                backgroundColor: 'transparent',
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            },
            {
                label: 'Paciente',
                data: patientData.ages && patientData.values ?
                    patientData.ages.map((age, i) => ({x: age, y: patientData.values[i]})) : [],
                borderColor: 'rgba(13, 110, 253, 1)',
                backgroundColor: 'rgba(13, 110, 253, 0.5)',
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.1
            }
        ];

        window.growthChartInstance.options.scales.y.title.text = chartTypeLabels[chartData.chart_type] || 'Valor';
        window.growthChartInstance.update();
    }

    // No inicializar automáticamente - esperar a que la sección del acordeón se abra
    console.log('📊 Script de gráfico cargado, esperando a que se abra la sección...');

    // Debug: Verificar si Chart.js está cargado
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js NO está cargado!');
    } else {
        console.log('✅ Chart.js cargado correctamente, versión:', Chart.version);
    }

    // Esperar a que Livewire esté listo para eventos
    document.addEventListener('livewire:initialized', () => {
        console.log('✅ Livewire inicializado, registrando listeners...');

        Livewire.on('updateGrowthChart', (data) => {
            console.log('📈 Evento updateGrowthChart recibido:', data);
            if (data && data[0]) {
                console.log('  - Tipo de gráfico:', data[0].chart_type);
                console.log('  - Curvas OMS edades:', data[0].who_curves?.ages?.length || 0);
                console.log('  - Datos paciente:', data[0].patient_data?.ages?.length || 0);
            }
            updateGrowthChart(data[0]);
        });

        // Show toastr notifications
        Livewire.on('showToastrGrowth', (event) => {
            toastr[event.type](event.message, '', {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: event.type === 'error' ? 5000 : 3000,
            });
        });
    });
</script>
@endpush
