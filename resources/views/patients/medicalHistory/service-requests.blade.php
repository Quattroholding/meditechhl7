<div class="service-requests-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="service-requests-grid" style="display: grid; gap: 20px;">
            @foreach($sectionData as $serviceRequest)
                <div class="service-request-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                    <div class="service-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                @switch($serviceRequest->service_type)
                                    @case('rehabilitation')
                                        🏃‍♂️ Rehabilitación
                                        @break
                                    @case('nursing')
                                        👩‍⚕️ Enfermería
                                        @break
                                    @case('nutrition')
                                        🥗 Nutrición
                                        @break
                                    @case('psychology')
                                        🧠 Psicología
                                        @break
                                    @case('social_work')
                                        🤝 Trabajo Social
                                        @break
                                    @case('physiotherapy')
                                        💪 Fisioterapia
                                        @break
                                    @case('occupational_therapy')
                                        🎯 Terapia Ocupacional
                                        @break
                                    @case('speech_therapy')
                                        🗣️ Fonoaudiología
                                        @break
                                    @default
                                        🧪 Servicio Especializado
                                @endswitch
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <span class="badge badge-{{ $serviceRequest->status === 'completed' ? 'resolved' : ($serviceRequest->status === 'cancelled' ? 'inactive' : ($serviceRequest->status === 'in-progress' ? 'active' : 'pending')) }}">
                                    @switch($serviceRequest->status)
                                        @case('requested')
                                            📝 Solicitado
                                            @break
                                        @case('scheduled')
                                            📅 Programado
                                            @break
                                        @case('in-progress')
                                            🔄 En Progreso
                                            @break
                                        @case('completed')
                                            ✅ Completado
                                            @break
                                        @case('cancelled')
                                            ❌ Cancelado
                                            @break
                                        @case('on-hold')
                                            ⏸️ En Espera
                                            @break
                                        @default
                                            {{ ucfirst($serviceRequest->status) }}
                                    @endswitch
                                </span>
                                @if($serviceRequest->urgency)
                                    <span class="badge" style="background:
                                        @if($serviceRequest->urgency === 'urgent') #fee2e2; color: #dc2626;
                                        @elseif($serviceRequest->urgency === 'high') #fef3c7; color: #92400e;
                                        @else #f0fdf4; color: #166534; @endif">
                                        @switch($serviceRequest->urgency)
                                            @case('urgent')
                                                🚨 Urgente
                                                @break
                                            @case('high')
                                                ⚡ Alta
                                                @break
                                            @case('routine')
                                                📅 Rutina
                                                @break
                                            @default
                                                {{ ucfirst($serviceRequest->urgency) }}
                                        @endswitch
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>Solicitado:</strong> {{ Carbon\Carbon::parse($serviceRequest->request_date)->format('d/m/Y H:i') }}</div>
                            @if($serviceRequest->practitioner)
                                <div><strong>Por:</strong> {{ $serviceRequest->practitioner->name }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Razón del Servicio -->
                    <div class="service-reason" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                            📝 Razón del Servicio
                        </div>
                        <div style="color: #1e293b; line-height: 1.6;">
                            {{ $serviceRequest->reason ?? $serviceRequest->description }}
                        </div>
                    </div>

                    <!-- Detalles Específicos del Servicio -->
                    <div class="service-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        @if($serviceRequest->sessions_requested)
                            <div>
                                <div style="font-size: 12px; color: #3b82f6; font-weight: 600; margin-bottom: 5px;">🔢 Sesiones Solicitadas</div>
                                <div style="background: #dbeafe; padding: 10px; border-radius: 8px; font-size: 13px; color: #1e40af;">
                                    {{ $serviceRequest->sessions_requested }} sesiones
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->frequency)
                            <div>
                                <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">📅 Frecuencia</div>
                                <div style="background: #d1fae5; padding: 10px; border-radius: 8px; font-size: 13px; color: #065f46;">
                                    {{ $serviceRequest->frequency }}
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->duration_per_session)
                            <div>
                                <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">⏱️ Duración por Sesión</div>
                                <div style="background: #ede9fe; padding: 10px; border-radius: 8px; font-size: 13px; color: #5b21b6;">
                                    {{ $serviceRequest->duration_per_session }} minutos
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->location_preference)
                            <div>
                                <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">📍 Ubicación Preferida</div>
                                <div style="background: #fed7aa; padding: 10px; border-radius: 8px; font-size: 13px; color: #9a3412;">
                                    {{ $serviceRequest->location_preference }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Objetivos del Tratamiento -->
                    @if($serviceRequest->treatment_goals)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                🎯 Objetivos del Tratamiento
                            </div>
                            <div style="background: linear-gradient(135deg, #fef3c7, #fcd34d); padding: 15px; border-radius: 10px; color: #92400e;">
                                {{ $serviceRequest->treatment_goals }}
                            </div>
                        </div>
                    @endif

                    <!-- Información de Programación -->
                    @if($serviceRequest->scheduled_date || $serviceRequest->assigned_provider)
                        <div class="scheduling-info" style="background: #f0f9ff; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #3b82f6;">
                            <div style="font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 10px;">
                                📅 Información de Programación
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                                @if($serviceRequest->scheduled_date)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">Fecha Programada</div>
                                        <div style="color: #1e293b;">{{ Carbon\Carbon::parse($serviceRequest->scheduled_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($serviceRequest->assigned_provider)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">Profesional Asignado</div>
                                        <div style="color: #1e293b;">{{ $serviceRequest->assigned_provider }}</div>
                                    </div>
                                @endif
                                @if($serviceRequest->estimated_duration)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">Duración Estimada</div>
                                        <div style="color: #1e293b;">{{ $serviceRequest->estimated_duration }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Progreso del Tratamiento -->
                    @if($serviceRequest->sessions_completed || $serviceRequest->progress_notes)
                        <div class="treatment-progress" style="background: #f0fdf4; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #059669;">
                            <div style="font-size: 14px; font-weight: 600; color: #065f46; margin-bottom: 10px;">
                                📊 Progreso del Tratamiento
                            </div>
                            @if($serviceRequest->sessions_completed && $serviceRequest->sessions_requested)
                                <div style="margin-bottom: 10px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <span style="font-size: 12px; color: #065f46; font-weight: 600;">Sesiones Completadas</span>
                                        <span style="font-size: 12px; color: #065f46;">{{ $serviceRequest->sessions_completed }}/{{ $serviceRequest->sessions_requested }}</span>
                                    </div>
                                    <div style="background: #dcfce7; height: 8px; border-radius: 4px; overflow: hidden;">
                                        <div style="background: #16a34a; height: 100%; width: {{ ($serviceRequest->sessions_completed / $serviceRequest->sessions_requested) * 100 }}%; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                            @endif
                            @if($serviceRequest->progress_notes)
                                <div>
                                    <div style="font-size: 12px; color: #065f46; font-weight: 600; margin-bottom: 5px;">Notas de Progreso</div>
                                    <div style="color: #374151; font-size: 13px; line-height: 1.5;">{{ $serviceRequest->progress_notes }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Resultados y Evaluación Final -->
                    @if($serviceRequest->outcome || $serviceRequest->final_assessment)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                📋 Evaluación Final
                            </div>
                            <div style="background: linear-gradient(135d, #d1fae5, #a7f3d0); padding: 15px; border-radius: 10px; color: #065f46;">
                                {{ $serviceRequest->outcome ?? $serviceRequest->final_assessment }}
                            </div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                        <div>
                            @if($serviceRequest->encounter)
                                <span>📅 Consulta: {{ Carbon\Carbon::parse($serviceRequest->encounter->encounter_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            @if($serviceRequest->reference_id)
                                <span>🔖 ID: {{ $serviceRequest->reference_id }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{--}}
        <div style="margin-top: 20px;">
            {{ $sectionData->links() }}
        </div>
        {{--}}
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🧪</div>
            <h3>No hay solicitudes de servicios registradas</h3>
            <p>Este paciente no tiene solicitudes de servicios en el período seleccionado.</p>
        </div>
    @endif
</div>
