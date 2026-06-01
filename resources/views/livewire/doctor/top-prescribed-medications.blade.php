<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-prescription-bottle me-2"></i>
            {{ __('doctor.dashboard.top_5_prescribed_medications') }}
        </h4>
        <div class="dropdown">
            <select wire:model.live="timeFrame" class="form-select form-select-sm">
                <option value="7">{{ __('doctor.dashboard.last_7_days') }}</option>
                <option value="30">{{ __('doctor.dashboard.last_30_days') }}</option>
                <option value="90">{{ __('doctor.dashboard.last_3_months') }}</option>
                <option value="365">{{ __('doctor.dashboard.last_year') }}</option>
                <option value="0">{{ __('doctor.dashboard.all_records') }}</option>
            </select>
        </div>
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                @for($i = 0; $i < 5; $i++)
                    <div class="skeleton-medication-item mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="skeleton-badge"></div>
                                <div class="skeleton-icon"></div>
                                <div>
                                    <div class="skeleton-name mb-1"></div>
                                    <div class="skeleton-generic"></div>
                                </div>
                            </div>
                            <div class="skeleton-count"></div>
                        </div>
                        <div class="skeleton-badges mb-2">
                            <div class="skeleton-badge-small"></div>
                            <div class="skeleton-badge-small"></div>
                            <div class="skeleton-badge-small"></div>
                        </div>
                        <div class="skeleton-progress mb-2"></div>
                        <div class="skeleton-stats">
                            <div class="skeleton-stat"></div>
                            <div class="skeleton-stat"></div>
                            <div class="skeleton-stat"></div>
                            <div class="skeleton-stat"></div>
                        </div>
                    </div>
                @endfor
            </div>
        @elseif(count($topMedications) > 0)
            <div class="medication-stats">
                @foreach($topMedications as $index => $medication)
                    <div class="medication-item mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="medication-info">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="medication-badge"
                                          style="background-color: {{ $this->getColorForMedication($index) }};">
                                        {{ $index + 1 }}
                                    </span>
                                    <i class="{{ $this->getMedicationIcon($medication['form']) }} me-2"
                                       style="color: {{ $this->getColorForMedication($index) }};"></i>
                                    <div>
                                        <span class="medication-name">{{ $medication['home_name'] ?? $medication['medication'] }}</span>
                                        @if($medication['home_name'] !== $medication['generic_name'])
                                            <small class="text-muted d-block">{{ $medication['generic_name'] }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="medication-details">
                                    <span class="badge bg-light text-dark me-1">{{ $medication['concentration'] }}</span>
                                    <span class="badge bg-light text-dark me-1">{{ ucfirst($medication['form']) }}</span>
                                    <span class="badge bg-light text-dark">{{ $medication['route'] }}</span>
                                </div>
                            </div>
                            <div class="medication-stats-text text-end">
                                <span class="medication-count">{{ $medication['prescription_count'] }}</span>
                                <small class="text-muted d-block">{{ __('doctor.dashboard.prescriptions') }}</small>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width: {{ $medication['percentage'] }}%; background-color: {{ $this->getColorForMedication($index) }};"
                                 aria-valuenow="{{ $medication['percentage'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>

                        <!-- Detalles de prescripción -->
                        <div class="row text-center">
                            <div class="col-3">
                                <small class="text-muted d-block">{{ __('doctor.dashboard.consultations') }}</small>
                                <span class="fw-bold">{{ $medication['encounter_count'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">{{ __('doctor.dashboard.patients') }}</small>
                                <span class="fw-bold">{{ $medication['patient_count'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">{{ __('doctor.dashboard.common_freq') }}</small>
                                <span class="fw-bold text-truncate" title="{{ $medication['frequency'] }}">
                                    {{ Str::limit($medication['frequency'], 8) }}
                                </span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">{{ __('doctor.dashboard.avg_quantity') }}</small>
                                <span class="fw-bold">{{ $medication['avg_quantity'] }}</span>
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
                    <div class="col-6">
                        <h6 class="mb-1">{{ __('doctor.dashboard.total_prescriptions') }}</h6>
                        <span class="h4 text-primary">{{ collect($topMedications)->sum('prescription_count') }}</span>
                    </div>
                    <div class="col-6">
                        <h6 class="mb-1">{{ __('doctor.dashboard.unique_patients') }}</h6>
                        <span class="h4 text-success">{{ collect($topMedications)->sum('patient_count') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-prescription-bottle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('doctor.dashboard.no_prescribed_medications') }}</h5>
                <p class="text-muted mb-0">
                    @if($timeFrame == '0')
                        {{ __('doctor.dashboard.no_prescriptions_found') }}
                    @else
                        {{ __('doctor.dashboard.no_prescriptions_in_days', ['days' => $timeFrame]) }}
                    @endif
                </p>
            </div>
        @endif
    </div>

    <div class="card-footer text-muted text-center">
        <small>
            <i class="fas fa-info-circle me-1"></i>
            {{ __('doctor.dashboard.based_on_doctor_prescriptions') }}
        </small>
    </div>
    <style>
        .medication-badge {
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

        .medication-name {
            font-weight: 500;
            color: #333;
            font-size: 0.95em;
        }

        .medication-count {
            font-size: 1.1em;
            font-weight: bold;
            color: var(--primary-color, #3498db);
        }

        .medication-item {
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .medication-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }

        .medication-details {
            margin-top: 5px;
        }

        .medication-details .badge {
            font-size: 0.7em;
            border: 1px solid #dee2e6;
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

        .medication-info .fas {
            font-size: 1.1em;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 10px;
            }

            .medication-info {
                margin-bottom: 10px;
            }

            .medication-badge {
                margin-bottom: 5px;
            }

            .medication-details .badge {
                font-size: 0.6em;
                margin-bottom: 2px;
            }

            .row.text-center .col-3 {
                margin-bottom: 10px;
            }
        }

        /* Loading skeleton styles */
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-medication-item {
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

        .skeleton-icon {
            width: 16px;
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            margin-right: 10px;
        }

        .skeleton-name {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 150px;
        }

        .skeleton-generic {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 120px;
        }

        .skeleton-count {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 60px;
        }

        .skeleton-badges {
            display: flex;
            gap: 5px;
        }

        .skeleton-badge-small {
            height: 14px;
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
            width: 22%;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Colores específicos para diferentes tipos de medicamentos */
        .medication-item:nth-child(1) .fas { opacity: 0.8; }
        .medication-item:nth-child(2) .fas { opacity: 0.8; }
        .medication-item:nth-child(3) .fas { opacity: 0.8; }
        .medication-item:nth-child(4) .fas { opacity: 0.8; }
        .medication-item:nth-child(5) .fas { opacity: 0.8; }
    </style>
</div>

