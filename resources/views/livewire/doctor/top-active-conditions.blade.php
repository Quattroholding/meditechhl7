<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-chart-bar me-2" style="color: var(--primary-color, #3498db);"></i>
            Top 5 Condiciones Activas
        </h4>
        <div class="dropdown">
            <select wire:model.live="timeFrame" class="form-select form-select-sm">
                <option value="7">Últimos 7 días</option>
                <option value="30">Últimos 30 días</option>
                <option value="90">Últimos 3 meses</option>
                <option value="365">Último año</option>
                <option value="0">Todos los registros</option>
            </select>
        </div>
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                @for($i = 0; $i < 5; $i++)
                    <div class="skeleton-condition-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="skeleton-badge"></div>
                                <div class="skeleton-name"></div>
                            </div>
                            <div class="skeleton-count"></div>
                        </div>
                        <div class="skeleton-progress mb-2"></div>
                        <div class="skeleton-stats">
                            <div class="skeleton-stat"></div>
                            <div class="skeleton-stat"></div>
                            <div class="skeleton-stat"></div>
                        </div>
                    </div>
                @endfor
            </div>
        @elseif(count($topConditions) > 0)
            <div class="condition-stats">
                @foreach($topConditions as $index => $condition)
                    <div class="condition-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="condition-info">
                                <span class="condition-badge"
                                      style="background-color: {{ $this->getColorForCondition($index) }};">
                                    {{ $index + 1 }}
                                </span>
                                <span class="condition-name">{{ $condition['description'] }}</span>
                                <small class="text-muted">({{ $condition['code'] }})</small>
                            </div>
                            <div class="condition-stats-text">
                                <span class="condition-count">{{ $condition['count'] }}</span>
                                <small class="text-muted">casos</small>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width: {{ $condition['percentage'] }}%; background-color: {{ $this->getColorForCondition($index) }};"
                                 aria-valuenow="{{ $condition['percentage'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>

                        <!-- Detalles adicionales -->
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">{{__('consultation.title')}}</small>
                                <span class="fw-bold">{{ $condition['encounter_count'] }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">{{__('patient.title')}}</small>
                                <span class="fw-bold">{{ $condition['patient_count'] }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">{{__('Porcentaje')}}</small>
                                <span class="fw-bold">{{ $condition['percentage'] }}%</span>
                            </div>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <hr class="my-3">
                    @endif
                @endforeach
            </div>

            <!-- Resumen total -->
            <div class="total-summary mt-3 pt-3 border-top">
                <div class="row text-center">
                    <div class="col-12">
                        <h6 class="mb-1">Total de Diagnósticos</h6>
                        <span class="h4 text-primary">{{ collect($topConditions)->sum('count') }}</span>
                        <small class="text-muted d-block">
                            en {{ collect($topConditions)->sum('encounter_count') }} consultas
                        </small>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay datos disponibles</h5>
                <p class="text-muted mb-0">
                    @if($timeFrame == '0')
                        No se encontraron diagnósticos en las consultas finalizadas.
                    @else
                        No se encontraron diagnósticos en los últimos {{ $timeFrame }} días.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <div class="card-footer text-muted text-center">
        <small>
            <i class="fas fa-info-circle me-1"></i>
            Basado en consultas finalizados del doctor
        </small>
    </div>
    <style>
        .condition-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            color: white;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }

        .condition-name {
            font-weight: 500;
            color: #333;
        }

        .condition-count {
            font-size: 1.1em;
            font-weight: bold;
            color: var(--primary-color, #3498db);
        }

        .condition-item {
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .condition-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }

        .progress {
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .total-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .empty-state {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Loading skeleton styles */
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-condition-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .skeleton-badge {
            width: 24px;
            height: 24px;
            background: #e9ecef;
            border-radius: 50%;
            margin-right: 10px;
        }

        .skeleton-name {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 200px;
        }

        .skeleton-count {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 50px;
        }

        .skeleton-progress {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            width: 100%;
        }

        .skeleton-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .skeleton-stat {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 30%;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 10px;
            }

            .condition-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .condition-badge {
                margin-bottom: 5px;
            }
        }
    </style>
</div>

