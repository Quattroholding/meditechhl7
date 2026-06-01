<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-calendar-alt me-2" ></i>
            {{ __('doctor.dashboard.peak_activity_hours') }}
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
                <!-- Skeleton para estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="skeleton-stat-card">
                            <div class="skeleton-stat-number"></div>
                            <div class="skeleton-stat-label"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="skeleton-stat-card">
                            <div class="skeleton-stat-number"></div>
                            <div class="skeleton-stat-label"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="skeleton-stat-card">
                            <div class="skeleton-stat-number"></div>
                            <div class="skeleton-stat-label"></div>
                        </div>
                    </div>
                </div>

                <!-- Skeleton para heatmap -->
                <div class="skeleton-heatmap mb-4"></div>

                <!-- Skeleton para top listas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="skeleton-list-header mb-2"></div>
                        @for($i = 0; $i < 3; $i++)
                            <div class="skeleton-list-item mb-1"></div>
                        @endfor
                    </div>
                    <div class="col-md-6">
                        <div class="skeleton-list-header mb-2"></div>
                        @for($i = 0; $i < 3; $i++)
                            <div class="skeleton-list-item mb-1"></div>
                        @endfor
                    </div>
                </div>
            </div>
        @elseif($totalAppointments > 0)
            <!-- Estadísticas rápidas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-primary">{{ $totalAppointments }}</h5>
                        <small class="text-muted">{{ __('doctor.dashboard.total_appointments') }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-success">
                            @if(count($peakHours) > 0)
                                {{ $this->getFormattedHour(array_key_first($peakHours)) }}
                            @else
                                N/A
                            @endif
                        </h5>
                        <small class="text-muted">{{ __('doctor.dashboard.peak_hour') }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item text-center">
                        <h5 class="mb-1 text-warning">
                            @if(count($peakDays) > 0)
                                {{ $this->getDayName(array_key_first($peakDays)) }}
                            @else
                                N/A
                            @endif
                        </h5>
                        <small class="text-muted">{{ __('doctor.dashboard.peak_day') }}</small>
                    </div>
                </div>
            </div>

            <!-- Heatmap -->
            <div class="heatmap-container">
                <div class="heatmap-header">
                    <div class="heatmap-corner"></div>
                    @for($day = 1; $day <= 6; $day++)
                        <div class="heatmap-day-header">
                            {{ substr($this->getDayName($day), 0, 3) }}
                        </div>
                    @endfor
                    <div class="heatmap-day-header">
                        {{ substr($this->getDayName(0), 0, 3) }}
                    </div>
                </div>

                @foreach($workingHours as $hour)
                    <div class="heatmap-row">
                        <div class="heatmap-hour-label">
                            {{ $this->getFormattedHour($hour) }}
                        </div>
                        @for($day = 1; $day <= 6; $day++)
                            <div class="heatmap-cell {{ $this->getIntensityClass($heatmapData[$hour][$day]) }}"
                                 title="{{ $this->getDayName($day) }} {{ $this->getFormattedHour($hour) }}: {{ $heatmapData[$hour][$day] }} {{ __('doctor.dashboard.appointments_lowercase') }}">
                                {{ $heatmapData[$hour][$day] > 0 ? $heatmapData[$hour][$day] : '' }}
                            </div>
                        @endfor
                        <div class="heatmap-cell {{ $this->getIntensityClass($heatmapData[$hour][0]) }}"
                             title="{{ $this->getDayName(0) }} {{ $this->getFormattedHour($hour) }}: {{ $heatmapData[$hour][0] }} {{ __('doctor.dashboard.appointments_lowercase') }}">
                            {{ $heatmapData[$hour][0] > 0 ? $heatmapData[$hour][0] : '' }}
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Leyenda -->
            <div class="heatmap-legend mt-3">
                <small class="text-muted me-2">{{ __('doctor.dashboard.less') }}</small>
                <div class="legend-colors">
                    <div class="legend-square intensity-0"></div>
                    <div class="legend-square intensity-1"></div>
                    <div class="legend-square intensity-2"></div>
                    <div class="legend-square intensity-3"></div>
                    <div class="legend-square intensity-4"></div>
                    <div class="legend-square intensity-5"></div>
                </div>
                <small class="text-muted ms-2">{{ __('doctor.dashboard.more') }}</small>
            </div>

            <!-- Top horas y días -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-clock me-1"></i>
                        {{ __('doctor.dashboard.top_hours') }}
                    </h6>
                    @foreach($peakHours as $hour => $count)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary">{{ $this->getFormattedHour($hour) }}</span>
                            <span class="text-muted">{{ $count }} {{ __('doctor.dashboard.appointments_lowercase') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-calendar-day me-1"></i>
                        {{ __('doctor.dashboard.top_days') }}
                    </h6>
                    @foreach($peakDays as $day => $count)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-success">{{ $this->getDayName($day) }}</span>
                            <span class="text-muted">{{ $count }} {{ __('doctor.dashboard.appointments_lowercase') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('doctor.dashboard.no_activity_data') }}</h5>
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
            {{ __('doctor.dashboard.based_on_confirmed_completed') }}
        </small>
    </div>
    <style>
        .heatmap-container {
            overflow-x: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
        }

        .heatmap-header {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .heatmap-corner {
            padding: 8px;
            border-right: 1px solid #dee2e6;
        }

        .heatmap-day-header {
            padding: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.8em;
            color: #495057;
            border-right: 1px solid #dee2e6;
        }

        .heatmap-row {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            border-bottom: 1px solid #dee2e6;
        }

        .heatmap-hour-label {
            padding: 8px;
            font-size: 0.75em;
            color: #6c757d;
            text-align: center;
            border-right: 1px solid #dee2e6;
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .heatmap-cell {
            padding: 8px;
            text-align: center;
            font-size: 0.8em;
            font-weight: 600;
            border-right: 1px solid #dee2e6;
            cursor: help;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Intensidades del heatmap */
        .intensity-0 { background-color: #ebedf0; color: #6c757d; }
        .intensity-1 { background-color: #c6e48b; color: #495057; }
        .intensity-2 { background-color: #7bc96f; color: white; }
        .intensity-3 { background-color: #239a3b; color: white; }
        .intensity-4 { background-color: #196127; color: white; }
        .intensity-5 { background-color: #0d2818; color: white; }

        .heatmap-legend {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .legend-colors {
            display: flex;
            gap: 2px;
        }

        .legend-square {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .stat-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
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

        .skeleton-stat-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .skeleton-stat-number {
            height: 24px;
            background: #e9ecef;
            border-radius: 4px;
            margin-bottom: 8px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        .skeleton-stat-label {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .skeleton-heatmap {
            height: 200px;
            background: #e9ecef;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .skeleton-heatmap::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg,
                transparent,
                rgba(255,255,255,0.3) 50%,
                transparent
            );
            animation: shimmer 2s infinite;
        }

        .skeleton-list-header {
            height: 18px;
            background: #e9ecef;
            border-radius: 4px;
            width: 40%;
        }

        .skeleton-list-item {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 100%;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @media (max-width: 768px) {
            .heatmap-container {
                font-size: 0.8em;
            }

            .heatmap-header,
            .heatmap-row {
                grid-template-columns: 60px repeat(7, 1fr);
            }

            .heatmap-hour-label {
                font-size: 0.65em;
                padding: 4px;
            }

            .heatmap-cell {
                padding: 4px;
                font-size: 0.7em;
                min-height: 24px;
            }

            .heatmap-day-header {
                font-size: 0.7em;
                padding: 4px;
            }
        }
    </style>
</div>
