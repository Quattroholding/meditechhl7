<div class="overview-content">
    <!-- Estadísticas Principales -->
    <div class="overview-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🏥</div>
                <div class="stat-title">Total Consultas</div>
            </div>
            <div class="stat-value">{{ $overviewData['total_encounters'] ?? 0 }}</div>
            <div class="stat-subtitle">
                @if($overviewData['last_visit'])
                    Última: {{ Carbon\Carbon::parse($overviewData['last_visit'])->diffForHumans() }}
                @else
                    Sin consultas registradas
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🩺</div>
                <div class="stat-title">Condiciones Activas</div>
            </div>
            <div class="stat-value">{{ $overviewData['active_conditions'] ?? 0 }}</div>
            <div class="stat-subtitle">Requieren seguimiento</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📋</div>
                <div class="stat-title">Órdenes Médicas</div>
            </div>
            <div class="stat-value">{{ $overviewData['total_requests'] ?? 0 }}</div>
            <div class="stat-subtitle">Solicitudes realizadas</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">❤️</div>
                <div class="stat-title">Signos Vitales</div>
            </div>
            <div class="stat-value">{{ $overviewData['vital_signs_count'] ?? 0 }}</div>
            <div class="stat-subtitle">Registros capturados</div>
        </div>
    </div>

    <!-- Información Crítica -->
    <div class="overview-grid">
        @if(!empty($overviewData['allergies']))
            <div class="stat-card" style="border-left: 5px solid #dc3545;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #dc3545;">⚠️</div>
                    <div class="stat-title">Alergias</div>
                </div>
                <div style="margin-top: 15px;">
                    @foreach($overviewData['allergies'] as $allergy)
                        <span class="badge" style="background: #fee2e2; color: #dc2626; margin: 2px;">
                            {{ $allergy->title }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        @if(!empty($overviewData['medications']))
            <div class="stat-card" style="border-left: 5px solid #059669;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #059669;">💊</div>
                    <div class="stat-title">Medicamentos Actuales</div>
                </div>
                <div style="margin-top: 15px;">
                    @foreach($overviewData['medications'] as $medication)
                        <div style="margin-bottom: 8px; padding: 8px; background: #f0fdf4; border-radius: 6px;">
                            <strong>{{ $medication->medicine->full_name ?? 'N/A' }}</strong>
                            <br><small style="color: #16a34a;">{{ $medication->dosage_text ?? '' }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Actividad Reciente -->
    @if(!empty($overviewData['recent_activity']))
        <div class="timeline-section">
            <h3 class="timeline-title">
                ⏰ Actividad Reciente
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
                                <br><small>Por: {{ $activity['provider'] }}</small>
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
