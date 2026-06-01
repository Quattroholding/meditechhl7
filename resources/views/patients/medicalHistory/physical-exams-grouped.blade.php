<div class="physical-exams-content">
    @if($sectionData && isset($sectionData['grouped_by_encounter']) && count($sectionData['grouped_by_encounter']) > 0)
        <div class="encounters-grid" style="display: grid; gap: 30px;">
            @foreach($sectionData['grouped_by_encounter'] as $encounterId => $encounterGroup)
                @php
                    $encounter = $encounterGroup['encounter'];
                    $physicalExams = $encounterGroup['physical_exams'];
                    $totalExams = $physicalExams->count();
                @endphp
                
                <!-- Encounter Card -->
                <div class="encounter-card" style="background: white; border: 2px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                    
                    <!-- Encounter Header -->
                    <div class="encounter-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px;">
                                    🏥 Consulta Médica - Exámenes Físicos
                                    @if($encounter)
                                        {!! $encounter->status !!}
                                    @endif
                                </h2>
                                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                                    @if($encounter)
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                            📅 {{ \Carbon\Carbon::parse($encounter->start)->format('d/m/Y H:i') }}
                                        </div>
                                        @if($encounter->practitioner)
                                            <div style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                                👨‍⚕️ {{ $encounter->practitioner->name }}
                                            </div>
                                        @endif
                                        @if($encounter->medicalSpeciality)
                                            <div style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                                🎯 {{ $encounter->medicalSpeciality->name }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 24px; font-weight: 700;">{{ $totalExams }}</div>
                                <div style="font-size: 12px; opacity: 0.9;">{{ $totalExams == 1 ? '{{ __('patient.medical_history.physical_exams_grouped.exam') }}' : '{{ __('patient.medical_history.physical_exams_grouped.exams') }}' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Physical Exams Content -->
                    <div style="padding: 25px;">
                        
                        <!-- Physical Exams Section -->
                        <div class="physical-exams-section">
                            <h3 style="font-size: 18px; font-weight: 600; color: #d97706; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #fed7aa; padding-bottom: 8px;">
                                🔍 Exámenes Físicos Realizados ({{ $totalExams }})
                            </h3>
                            <div style="display: grid; gap: 20px;">
                                @foreach($physicalExams as $exam)
                                    <div class="physical-exam-item" style="background: #fffbeb; border: 1px solid #fed7aa; border-radius: 12px; padding: 20px; border-left: 4px solid #f59e0b;">
                                        
                                        <!-- Exam Header -->
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                            <div>
                                                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                                                    🔍 Examen Físico
                                                    @if($exam->observationType && $exam->observationType->name)
                                                        - {{ $exam->observationType->name }}
                                                    @endif
                                                </h4>
                                                <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 12px; color: #374151;">
                                                    @if($exam->effective_date)
                                                        <span><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($exam->effective_date)->format('d/m/Y H:i') }}</span>
                                                    @endif
                                                    @if($exam->practitioner)
                                                        <span><strong>👨‍⚕️ Examinador:</strong> {{ $exam->practitioner->name }}</span>
                                                    @endif
                                                    @if($exam->observationType && $exam->observationType->system)
                                                        <span><strong>🏷️ Sistema:</strong> {{ $exam->observationType->system }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($exam->status)
                                                <span class="badge" style="background: #f59e0b; color: white; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                                    {{ ucfirst($exam->status) }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Hallazgos Principales -->
                                        @if($exam->finding && is_array($exam->finding))
                                            <div class="findings-section" style="margin-bottom: 15px;">
                                                <div style="font-size: 14px; font-weight: 600; color: #92400e; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                                    📋 Hallazgos del Examen
                                                </div>
                                                <div style="display: grid; gap: 8px;">
                                                    @foreach($exam->finding as $key => $value)
                                                        <div style="background: white; padding: 10px; border-radius: 8px; border: 1px solid #fbbf24;">
                                                            <strong style="color: #92400e;">{{ ucfirst($key) }}:</strong> {{ $value }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Sistemas Examinados -->
                                        <div class="systems-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 15px;">
                                            @if($exam->cardiovascular_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        ❤️ Cardiovascular
                                                    </div>
                                                    <div style="background: #fef2f2; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #fecaca;">
                                                        {{ $exam->cardiovascular_findings }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($exam->respiratory_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        🫁 Respiratorio
                                                    </div>
                                                    <div style="background: #f0f9ff; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #bae6fd;">
                                                        {{ $exam->respiratory_findings }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($exam->abdominal_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        🫄 Abdominal
                                                    </div>
                                                    <div style="background: #f0fdf4; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #bbf7d0;">
                                                        {{ $exam->abdominal_findings }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($exam->neurological_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        🧠 Neurológico
                                                    </div>
                                                    <div style="background: #faf5ff; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #ddd6fe;">
                                                        {{ $exam->neurological_findings }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($exam->musculoskeletal_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        🦴 Musculoesquelético
                                                    </div>
                                                    <div style="background: #fff7ed; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #fed7aa;">
                                                        {{ $exam->musculoskeletal_findings }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($exam->skin_findings)
                                                <div class="system-finding">
                                                    <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                        🌸 Piel
                                                    </div>
                                                    <div style="background: #fdf2f8; padding: 8px; border-radius: 6px; font-size: 12px; color: #374151; border: 1px solid #fbcfe8;">
                                                        {{ $exam->skin_findings }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Conclusiones e Impresión -->
                                        @if($exam->impression || $exam->recommendations)
                                            <div class="conclusions-section" style="margin-top: 15px;">
                                                @if($exam->impression)
                                                    <div style="margin-bottom: 10px;">
                                                        <div style="font-size: 12px; font-weight: 600; color: #92400e; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                            🎯 Impresión Clínica
                                                        </div>
                                                        <div style="background: #fef3c7; padding: 10px; border-radius: 8px; color: #92400e; font-size: 12px; border: 1px solid #fcd34d;">
                                                            {{ $exam->impression }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($exam->recommendations)
                                                    <div>
                                                        <div style="font-size: 12px; font-weight: 600; color: #92400e; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                                            💡 Recomendaciones
                                                        </div>
                                                        <div style="background: #d1fae5; padding: 10px; border-radius: 8px; color: #065f46; font-size: 12px; border: 1px solid #a7f3d0;">
                                                            {{ $exam->recommendations }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Encounter Actions -->
                        @if($encounter)
                            <div class="encounter-actions" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 12px; color: #64748b;">
                                    Consulta ID: {{ $encounter->id }} | {{ \Carbon\Carbon::parse($encounter->start)->format('d/m/Y H:i') }}
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <!-- Placeholder for future actions -->
                                    <span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 11px;">
                                        📊 {{ $totalExams }} Examen{{ $totalExams != 1 ? 'es' : '' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination Controls -->
        @if(isset($sectionData['last_page']) && $sectionData['last_page'] > 1)
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                <nav style="display: flex; align-items: center; gap: 10px;">
                    <!-- Previous Button -->
                    @if($sectionData['current_page'] > 1)
                        <button wire:click="previousPhysicalExamPage" 
                                class="pagination-btn" 
                                style="background: #f59e0b; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
                            ← Anterior
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            ← Anterior
                        </span>
                    @endif

                    <!-- Page Numbers -->
                    <div style="display: flex; align-items: center; gap: 5px;">
                        @foreach(range(1, $sectionData['last_page']) as $page)
                            @if($page == $sectionData['current_page'])
                                <span style="background: #f59e0b; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPhysicalExamPage({{ $page }})" 
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextPhysicalExamPage" 
                                class="pagination-btn" 
                                style="background: #f59e0b; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
                            Siguiente →
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            Siguiente →
                        </span>
                    @endif
                </nav>
            </div>

            <!-- Pagination Info -->
            <div style="margin-top: 15px; text-align: center; font-size: 13px; color: #64748b;">
                Mostrando consultas {{ $sectionData['from'] ?? 0 }} a {{ $sectionData['to'] ?? 0 }} 
                de {{ $sectionData['total_encounters'] ?? 0 }} total
            </div>
        @endif
    @elseif($sectionData && (is_countable($sectionData) ? count($sectionData) > 0 : !empty($sectionData)))
        <!-- Fallback to original view if no grouped data -->
        @include('patients.medicalHistory.physical-exams')
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🔍</div>
            <h3>No hay {{ __('patient.medical_history.physical_exams_grouped.exams') }} físicos registrados</h3>
            <p>Este paciente no tiene {{ __('patient.medical_history.physical_exams_grouped.exams') }} físicos en el período seleccionado.</p>
        </div>
    @endif
</div>