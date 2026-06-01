<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-chart-line me-2"></i>
            {{ __('doctor.dashboard.consultation_effectiveness') }}
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
            <!-- Loading skeleton -->
            <div class="loading-skeleton">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="skeleton-card">
                            <div class="skeleton-text-large"></div>
                            <div class="skeleton-text-small"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="skeleton-card">
                            <div class="skeleton-text-large"></div>
                            <div class="skeleton-text-small"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="skeleton-card">
                            <div class="skeleton-text-large"></div>
                            <div class="skeleton-text-small"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="skeleton-card">
                            <div class="skeleton-text-large"></div>
                            <div class="skeleton-text-small"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-flow">
                    <div class="skeleton-circle"></div>
                    <div class="skeleton-arrow"></div>
                    <div class="skeleton-circle"></div>
                    <div class="skeleton-arrow"></div>
                    <div class="skeleton-circle"></div>
                    <div class="skeleton-arrow"></div>
                    <div class="skeleton-circle"></div>
                </div>
            </div>
        @elseif($totalAppointments > 0)
            <!-- Métricas principales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-primary">{{ $totalAppointments }}</h5>
                        <small class="text-muted">{{ __('doctor.dashboard.total_appointments') }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-success">{{ $conversionRate }}%</h5>
                        <small class="text-muted">{{ __('doctor.dashboard.completion_rate') }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-info">{{ $this->getFormattedTime($averageCompletionTime) }}</h5>
                        <small class="text-muted">{{ __('doctor.dashboard.average_time') }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-warning">{{ $statusCounts['fulfilled'] ?? 0 }}</h5>
                        <small class="text-muted">{{ __('doctor.dashboard.completed_appointments') }}</small>
                    </div>
                </div>
            </div>

            <!-- Flujo de conversión -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3">
                        <i class="fas fa-route me-1"></i>
                        {{ __('doctor.dashboard.conversion_flow') }}
                    </h6>
                    <div class="conversion-flow">
                        <div class="flow-step">
                            <div class="step-circle bg-primary">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="step-content">
                                <h6>{{ __('doctor.dashboard.status_booked') }}</h6>
                                <span class="count">{{ $statusCounts['booked'] ?? 0 }}</span>
                                <span class="percentage">{{ $this->getStatusPercentage('booked') }}%</span>
                            </div>
                        </div>

                        <div class="flow-arrow">
                            <i class="fas fa-arrow-right"></i>
                            <span class="conversion-rate">{{ $this->getEffectivenessPercentage('booked', 'arrived') }}%</span>
                        </div>

                        <div class="flow-step">
                            <div class="step-circle bg-info">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="step-content">
                                <h6>{{ __('doctor.dashboard.status_arrived') }}</h6>
                                <span class="count">{{ $statusCounts['arrived'] ?? 0 }}</span>
                                <span class="percentage">{{ $this->getStatusPercentage('arrived') }}%</span>
                            </div>
                        </div>

                        <div class="flow-arrow">
                            <i class="fas fa-arrow-right"></i>
                            <span class="conversion-rate">{{ $this->getEffectivenessPercentage('arrived', 'checked-in') }}%</span>
                        </div>

                        <div class="flow-step">
                            <div class="step-circle bg-warning">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="step-content">
                                <h6>{{ __('doctor.dashboard.status_checked_in') }}</h6>
                                <span class="count">{{ $statusCounts['checked-in'] ?? 0 }}</span>
                                <span class="percentage">{{ $this->getStatusPercentage('checked-in') }}%</span>
                            </div>
                        </div>

                        <div class="flow-arrow">
                            <i class="fas fa-arrow-right"></i>
                            <span class="conversion-rate">{{ $this->getEffectivenessPercentage('checked-in', 'fulfilled') }}%</span>
                        </div>

                        <div class="flow-step">
                            <div class="step-circle bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="step-content">
                                <h6>{{ __('doctor.dashboard.status_completed') }}</h6>
                                <span class="count">{{ $statusCounts['fulfilled'] ?? 0 }}</span>
                                <span class="percentage">{{ $this->getStatusPercentage('fulfilled') }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Puntos de abandono -->
            @if(count($dropoffPoints) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ __('doctor.dashboard.dropoff_points') }}
                    </h6>
                    <div class="row">
                        @foreach($dropoffPoints as $transition => $data)
                            @if($data['count'] > 0)
                            <div class="col-md-4 mb-3">
                                <div class="dropoff-card">
                                    <div class="dropoff-header">
                                        @switch($transition)
                                            @case('booked_to_arrived')
                                                <i class="fas fa-calendar-times text-danger"></i>
                                                <span>{{ __('doctor.dashboard.did_not_arrive') }}</span>
                                                @break
                                            @case('arrived_to_checked_in')
                                                <i class="fas fa-user-times text-warning"></i>
                                                <span>{{ __('doctor.dashboard.did_not_check_in') }}</span>
                                                @break
                                            @case('checked_in_to_fulfilled')
                                                <i class="fas fa-times-circle text-info"></i>
                                                <span>{{ __('doctor.dashboard.did_not_complete') }}</span>
                                                @break
                                        @endswitch
                                    </div>
                                    <div class="dropoff-stats">
                                        <span class="count">{{ $data['count'] }}</span>
                                        <span class="percentage">{{ $data['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Progreso visual -->
            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3">
                        <i class="fas fa-chart-bar me-1"></i>
                        {{ __('doctor.dashboard.progress_by_status') }}
                    </h6>
                    @foreach(['booked', 'arrived', 'checked-in', 'fulfilled'] as $status)
                        @php
                            $percentage = $this->getStatusPercentage($status);
                            $count = $statusCounts[$status] ?? 0;
                        @endphp
                        <div class="progress-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="progress-label">
                                    @switch($status)
                                        @case('booked')
                                            <i class="fas fa-calendar-plus text-primary me-1"></i>
                                            {{ __('doctor.dashboard.status_booked_plural') }}
                                            @break
                                        @case('arrived')
                                            <i class="fas fa-user-check text-info me-1"></i>
                                            {{ __('doctor.dashboard.status_arrived_plural') }}
                                            @break
                                        @case('checked-in')
                                            <i class="fas fa-clipboard-check text-warning me-1"></i>
                                            {{ __('doctor.dashboard.status_checked_in_plural') }}
                                            @break
                                        @case('fulfilled')
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            {{ __('doctor.dashboard.status_completed_plural') }}
                                            @break
                                    @endswitch
                                </span>
                                <span class="progress-value">{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar
                                    @switch($status)
                                        @case('booked') bg-primary @break
                                        @case('arrived') bg-info @break
                                        @case('checked-in') bg-warning @break
                                        @case('fulfilled') bg-success @break
                                    @endswitch"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('doctor.dashboard.no_effectiveness_data') }}</h5>
                <p class="text-muted mb-0">
                    @if($timeFrame == '0')
                        {{ __('doctor.dashboard.no_appointments_found_period') }}
                    @else
                        {{ __('doctor.dashboard.no_appointments_found_in_days', ['days' => $timeFrame]) }}
                    @endif
                </p>
            </div>
        @endif
    </div>

    <div class="card-footer text-muted text-center">
        <small>
            <i class="fas fa-info-circle me-1"></i>
            {{ __('doctor.dashboard.based_on_appointment_status_history') }}
        </small>
    </div>

    <style>
        .stat-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .conversion-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow-x: auto;
            padding: 20px 0;
        }

        .flow-step {
            text-align: center;
            min-width: 100px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: white;
            font-size: 18px;
        }

        .step-content h6 {
            margin-bottom: 5px;
            font-size: 0.9em;
            color: #495057;
        }

        .step-content .count {
            display: block;
            font-weight: 600;
            font-size: 1.2em;
            color: #212529;
        }

        .step-content .percentage {
            display: block;
            font-size: 0.8em;
            color: #6c757d;
        }

        .flow-arrow {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 10px;
            color: #6c757d;
        }

        .flow-arrow i {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .conversion-rate {
            font-size: 0.8em;
            font-weight: 600;
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .dropoff-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .dropoff-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            gap: 8px;
        }

        .dropoff-header i {
            font-size: 18px;
        }

        .dropoff-header span {
            font-weight: 600;
            color: #495057;
        }

        .dropoff-stats .count {
            display: block;
            font-size: 1.5em;
            font-weight: 600;
            color: #dc3545;
        }

        .dropoff-stats .percentage {
            display: block;
            font-size: 0.9em;
            color: #6c757d;
        }

        .progress-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }

        .progress-label {
            font-weight: 500;
            color: #495057;
        }

        .progress-value {
            font-weight: 600;
            color: #212529;
        }

        .empty-state {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Loading skeleton styles */
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .skeleton-text-large {
            height: 24px;
            background: #e9ecef;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .skeleton-text-small {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 70%;
            margin: 0 auto;
        }

        .skeleton-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
        }

        .skeleton-circle {
            width: 50px;
            height: 50px;
            background: #e9ecef;
            border-radius: 50%;
        }

        .skeleton-arrow {
            width: 30px;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .conversion-flow {
                flex-direction: column;
                gap: 20px;
            }

            .flow-arrow {
                margin: 0;
            }

            .flow-arrow i {
                transform: rotate(90deg);
            }

            .stat-item {
                margin-bottom: 10px;
            }
        }

    </style>
</div>
