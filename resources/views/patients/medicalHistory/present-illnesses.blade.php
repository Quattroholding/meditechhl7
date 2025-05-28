<div class="present-illnesses-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="illnesses-grid" style="display: grid; gap: 25px;">
            @foreach($sectionData as $illness)
                <div class="illness-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                    <div class="illness-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                🤒 {{ $illness->encounter->reason ?? 'Enfermedad Actual' }}
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                @if($illness->severity)
                                    <span class="badge" style="background:
                                        @if($illness->severity === 'severe') #fee2e2; color: #dc2626;
                                        @elseif($illness->severity === 'moderate') #fef3c7; color: #92400e;
                                        @else #f0fdf4; color: #166534; @endif">
                                        {{ ucfirst($illness->severity) }}
                                    </span>
                                @endif
                                @if($illness->status)
                                    <span class="badge badge-{{ $illness->status === 'active' ? 'active' : ($illness->status === 'resolved' ? 'resolved' : 'inactive') }}">
                                        {{ ucfirst($illness->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>Inicio:</strong> {{ Carbon\Carbon::parse($illness->onset_date)->format('d/m/Y') }}</div>
                            @if($illness->duration)
                                <div><strong>Duración:</strong> {{ $illness->duration }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Historia de la Enfermedad Actual -->
                    @if($illness->history_present_illness)
                        <div class="hpi-section" style="margin-bottom: 20px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                📖 Historia de la Enfermedad Actual
                            </div>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border-left: 4px solid #8b5cf6; line-height: 1.6;">
                                {{ $illness->history_present_illness }}
                            </div>
                        </div>
                    @endif

                    <!-- Síntomas y Características -->
                    <div class="symptoms-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        @if($illness->location)
                            <div class="symptom-item">
                                <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    📍 Localización
                                </div>
                                <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $illness->location }}
                                </div>
                            </div>
                        @endif

                        @if($illness->quality)
                            <div class="symptom-item">
                                <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🔍 Calidad
                                </div>
                                <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $illness->quality }}
                                </div>
                            </div>
                        @endif

                        @if($illness->radiation)
                            <div class="symptom-item">
                                <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    ↗️ Irradiación
                                </div>
                                <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $illness->radiation }}
                                </div>
                            </div>
                        @endif

                        @if($illness->timing)
                            <div class="symptom-item">
                                <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    ⏰ Cronología
                                </div>
                                <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $illness->timing }}
                                </div>
                            </div>
                        @endif

                        @if($illness->aggravating_factors)
                            <div class="symptom-item">
                                <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🔄 Factores Agravantes
                                </div>
                                <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $illness->aggravating_factors }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Escala de Dolor -->
                    @if($illness->pain_scale)
                        <div class="pain-scale-section" style="margin-bottom: 20px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                🌡️ Escala de Dolor
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 10px;">
                                <div style="display: flex; gap: 5px;">
                                    @for($i = 1; $i <= 10; $i++)
                                        <div style="width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;
                                            background: {{ $i <= $illness->pain_scale ? ($i <= 3 ? '#22c55e' : ($i <= 6 ? '#f59e0b' : '#ef4444')) : '#e5e7eb' }};
                                            color: {{ $i <= $illness->pain_scale ? 'white' : '#6b7280' }};">
                                            {{ $i }}
                                        </div>
                                    @endfor
                                </div>
                                <div style="font-size: 18px; font-weight: 700; color: #1e293b;">
                                    {{ $illness->pain_scale }}/10
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Síntomas Asociados -->
                    @if($illness->associated_symptoms)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                🔗 Síntomas Asociados
                            </div>
                            <div style="background: linear-gradient(135deg, #fef3c7, #fcd34d); padding: 15px; border-radius: 10px; color: #92400e;">
                                {{ $illness->associated_symptoms }}
                            </div>
                        </div>
                    @endif

                    <!-- Síntomas Asociados -->
                    @if($illness->description)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                🎯 Descripcion
                            </div>
                            <div style="background: linear-gradient(135deg, #c7fefe, #4d84fc); padding: 15px; border-radius: 10px; color: #92400e;">
                                {{ $illness->description }}
                            </div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                        <div>
                            @if($illness->encounter)
                                <span>📅 Consulta: {{ Carbon\Carbon::parse($illness->encounter->created_at)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            @if($illness->updated_at)
                                <span>✏️ Actualizado: {{ Carbon\Carbon::parse($illness->updated_at)->diffForHumans() }}</span>
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
            <div style="font-size: 48px; margin-bottom: 20px;">🤒</div>
            <h3>No hay enfermedades actuales registradas</h3>
            <p>Este paciente no tiene enfermedades actuales en el período seleccionado.</p>
        </div>
    @endif
</div>
