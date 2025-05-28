<div class="physical-exams-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="exams-grid" style="display: grid; gap: 25px;">
            @foreach($sectionData as $exam)
                <div class="exam-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease; hover: border-color: #3b82f6;">
                    <div class="exam-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                                🔍 Examen Físico
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                @if($exam->observationType->name)
                                    <span class="badge badge-active">{{ ucfirst($exam->name) }}</span>
                                @endif
                                @if($exam->observationType->system)
                                    <span class="badge" style="background: #e0f2fe; color: #0277bd;">
                                        {{ $exam->observationType->system }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>Fecha:</strong> {{ Carbon\Carbon::parse($exam->effective_date)->format('d/m/Y H:i') }}</div>
                            @if($exam->practitioner)
                                <div><strong>Examinador:</strong> {{ $exam->practitioner->name }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Hallazgos Principales -->
                    @if($exam->finding)
                        <div class="findings-section" style="margin-bottom: 20px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                📋 Hallazgos del Examen
                            </div>
                            @foreach($exam->finding as $key=>$value)
                                <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border-left: 4px solid #3b82f6;">
                                {{$key}}:{{ $value }}
                                </div>
                            @endforeach

                        </div>
                    @endif

                    <!-- Sistemas Examinados -->
                    <div class="systems-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        @if($exam->cardiovascular_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    ❤️ Cardiovascular
                                </div>
                                <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->cardiovascular_findings }}
                                </div>
                            </div>
                        @endif

                        @if($exam->respiratory_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🫁 Respiratorio
                                </div>
                                <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->respiratory_findings }}
                                </div>
                            </div>
                        @endif

                        @if($exam->abdominal_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🫄 Abdominal
                                </div>
                                <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->abdominal_findings }}
                                </div>
                            </div>
                        @endif

                        @if($exam->neurological_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🧠 Neurológico
                                </div>
                                <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->neurological_findings }}
                                </div>
                            </div>
                        @endif

                        @if($exam->musculoskeletal_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🦴 Musculoesquelético
                                </div>
                                <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->musculoskeletal_findings }}
                                </div>
                            </div>
                        @endif

                        @if($exam->skin_findings)
                            <div class="system-finding">
                                <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                    🌸 Piel
                                </div>
                                <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151;">
                                    {{ $exam->skin_findings }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Conclusiones e Impresión -->
                    @if($exam->impression || $exam->recommendations)
                        <div class="conclusions-section">
                            @if($exam->impression)
                                <div style="margin-bottom: 15px;">
                                    <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                        🎯 Impresión Clínica
                                    </div>
                                    <div style="background: linear-gradient(135deg, #fef3c7, #fcd34d); padding: 15px; border-radius: 10px; color: #92400e;">
                                        {{ $exam->impression }}
                                    </div>
                                </div>
                            @endif

                            @if($exam->recommendations)
                                <div>
                                    <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                        💡 Recomendaciones
                                    </div>
                                    <div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); padding: 15px; border-radius: 10px; color: #065f46;">
                                        {{ $exam->recommendations }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Footer con metadatos -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                        <div>
                            @if($exam->encounter)
                                <span>📅 Consulta: {{ Carbon\Carbon::parse($exam->encounter->encounter_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            @if($exam->duration_minutes)
                                <span>⏱️ Duración: {{ $exam->duration_minutes }} min</span>
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
            <div style="font-size: 48px; margin-bottom: 20px;">🔍</div>
            <h3>No hay exámenes físicos registrados</h3>
            <p>Este paciente no tiene exámenes físicos en el período seleccionado.</p>
        </div>
    @endif
</div>
