<div class="overview-content">
    <!-- Estadísticas Principales -->
    <div class="overview-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🏥</div>
                <div class="stat-title">{{ __('patient.medical_history.total_consultations') }}</div>
            </div>
            <div class="stat-value">{{ $overviewData['total_encounters'] ?? 0 }}</div>
            <div class="stat-subtitle">
                @if($overviewData['last_visit'])
                    {{ __('patient.medical_history.last') }}: {{ Carbon\Carbon::parse($overviewData['last_visit'])->diffForHumans() }}
                @else
                    {{ __('patient.medical_history.no_consultations_recorded') }}
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🩺</div>
                <div class="stat-title">{{ __('patient.medical_history.active_conditions') }}</div>
            </div>
            <div class="stat-value">{{ $overviewData['active_conditions'] ?? 0 }}</div>
            <div class="stat-subtitle">{{ __('patient.medical_history.require_followup') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📋</div>
                <div class="stat-title">{{ __('patient.medical_history.medical_orders') }}</div>
            </div>
            <div class="stat-value">{{ $overviewData['total_requests'] ?? 0 }}</div>
            <div class="stat-subtitle">{{ __('patient.medical_history.requests_made') }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">❤️</div>
                <div class="stat-title">{{ __('patient.medical_history.vital_signs') }}</div>
            </div>
            <div class="stat-value">{{ $overviewData['vital_signs_count'] ?? 0 }}</div>
            <div class="stat-subtitle">{{ __('patient.medical_history.records_captured') }}</div>
        </div>
    </div>

    <!-- Información Crítica -->
    <div class="overview-grid">
        @if(!empty($overviewData['allergies']))
            <div class="stat-card" style="border-left: 5px solid #dc3545;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #dc3545;">⚠️</div>
                    <div class="stat-title">{{ __('patient.medical_history.allergies') }}</div>
                </div>
                <div style="margin-top: 15px;">
                    @foreach($overviewData['allergies'] as $allergy)
                        <span class="badge" style="background: #fee2e2; color: #dc2626; margin: 2px;">
                            {{ $allergy['title'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        @if(!empty($overviewData['medications']))
            <div class="stat-card" style="border-left: 5px solid #059669;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #059669;">💊</div>
                    <div class="stat-title">{{ __('patient.medical_history.current_medications') }}</div>
                </div>
                <div style="margin-top: 15px;">
                    @foreach($overviewData['medications'] as $medication)
                        <div style="margin-bottom: 8px; padding: 8px; background: #f0fdf4; border-radius: 6px;">
                            <strong>{{ $medication['medicine_name'] }}</strong>
                            <br><small style="color: #16a34a;">{{ $medication['dosage_text'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Historial Médico por Categorías -->
    @if(!empty($overviewData['medical_histories']))
        <div class="overview-grid">
            @php
                $categoryConfig = [
                    //'allergy' => ['icon' => '⚠️', 'title' => __('patient.medical_history.allergies'), 'color' => '#dc3545'],
                    'medication' => ['icon' => '💊', 'title' => __('patient.medical_history.previous_medications'), 'color' => '#059669'],
                    'surgery' => ['icon' => '🔪', 'title' => __('patient.medical_history.surgeries'), 'color' => '#7c3aed'],
                    'chronic-illness' => ['icon' => '🩺', 'title' => __('patient.medical_history.chronic_diseases'), 'color' => '#ea580c'],
                    'hospitalization' => ['icon' => '🏥', 'title' => __('patient.medical_history.hospitalizations'), 'color' => '#0ea5e9'],
                    'immunization' => ['icon' => '💉', 'title' => __('patient.medical_history.vaccines'), 'color' => '#10b981'],
                    'family-history' => ['icon' => '👨‍👩‍👧‍👦', 'title' => __('patient.medical_history.family_history'), 'color' => '#8b5cf6'],
                    'social-history' => ['icon' => '🚬', 'title' => __('patient.medical_history.social_history'), 'color' => '#f59e0b'],
                    'other' => ['icon' => '📝', 'title' => __('patient.medical_history.others'), 'color' => '#6b7280']
                ];
            @endphp

            @foreach($overviewData['medical_histories'] as $category => $histories)
                @php $config = $categoryConfig[$category] ?? $categoryConfig['other']; @endphp
                <div class="stat-card" style="border-left: 5px solid {{ $config['color'] }};">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: {{ $config['color'] }};">{{ $config['icon'] }}</div>
                        <div class="stat-title">{{ $config['title'] }}</div>
                        <div class="stat-count" style="font-size: 14px; color: #6b7280;">{{ count($histories) }}</div>
                    </div>
                    <div style="margin-top: 15px; max-height: 150px; overflow-y: auto;">
                        @foreach(array_slice($histories, 0, 5) as $history)
                            <div style="margin-bottom: 8px; padding: 8px; background: #f8fafc; border-radius: 6px; border-left: 3px solid {{ $config['color'] }};">
                                <strong>{{ $history['title'] }}</strong>
                                @if($history['description'])
                                    <br><small style="color: #6b7280;">{{ Str::limit($history['description'], 50) }}</small>
                                @endif
                                @if($history['recorded_date'])
                                    <br><small style="color: {{ $config['color'] }};">{{ Carbon\Carbon::parse($history['recorded_date'])->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        @endforeach
                        @if(count($histories) > 5)
                            <div style="text-align: center; padding: 8px;">
                                <small style="color: #6b7280;">{{ count($histories) - 5 }} {{ __('patient.medical_history.more') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Actividad Reciente -->
    @if(!empty($overviewData['recent_activity']))
        <div class="timeline-section">
            <h3 class="timeline-title">
                ⏰ {{ __('patient.medical_history.recent_activity') }}
            </h3>
            <div class="timeline">
                @foreach($overviewData['recent_activity'] as $activity)
                    <div class="timeline-item">
                        <div class="timeline-header">
                            <div class="timeline-title-item">
                                {{ $activity['icon'] }} {{ $activity['title'] }}
                            </div>
                            <div class="timeline-date">
                                {{ Carbon\Carbon::parse($activity['date'])->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="timeline-content">
                            <strong>{{ $activity['description'] }}</strong>
                            @if(isset($activity['provider']))
                                <br><small>{{ __('patient.medical_history.by') }}: {{ $activity['provider'] }}</small>
                            @endif
                            @if(isset($activity['severity']))
                                <span class="badge badge-{{ $activity['severity'] }}" style="margin-left: 10px;">
                                    {{ ucfirst($activity['severity']) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
