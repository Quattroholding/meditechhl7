<div class="vital-signs-content">
    @if($sectionData && isset($sectionData['grouped_by_encounter']) && count($sectionData['grouped_by_encounter']) > 0)
        <div class="encounters-grid" style="display: grid; gap: 30px;">
            @foreach($sectionData['grouped_by_encounter'] as $encounterId => $encounterGroup)
                @php
                    $encounter = $encounterGroup['encounter'];
                    $vitalSigns = $encounterGroup['vital_signs'];
                    $summary = $encounterGroup['vital_signs_summary'];
                    $totalVitals = $vitalSigns->count();
                @endphp
                
                <!-- Encounter Card -->
                <div class="encounter-card" style="background: white; border: 2px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                    
                    <!-- Encounter Header -->
                    <div class="encounter-header" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; padding: 20px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px;">
                                    🏥 Consulta Médica - Signos Vitales
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
                                <div style="font-size: 24px; font-weight: 700;">{{ $totalVitals }}</div>
                                <div style="font-size: 12px; opacity: 0.9;">{{ $totalVitals == 1 ? 'signo vital' : 'signos vitales' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Vital Signs Content -->
                    <div style="padding: 25px;">
                        
                        <!-- Summary Section -->
                        <div class="vital-signs-summary" style="margin-bottom: 25px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #dc2626; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #fecaca; padding-bottom: 8px;">
                                ❤️ Resumen de Signos Vitales
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                
                                <!-- Presión Arterial -->
                                @if($summary['blood_pressure']['systolic'] || $summary['blood_pressure']['diastolic'])
                                    <div class="vital-summary-card" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 15px; border-left: 4px solid #dc2626;">
                                        <div style="font-size: 12px; color: #b91c1c; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            🩸 Presión Arterial
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['blood_pressure']['systolic'] ?? '?' }}/{{ $summary['blood_pressure']['diastolic'] ?? '?' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">mmHg</div>
                                    </div>
                                @endif

                                <!-- Frecuencia Cardíaca -->
                                @if($summary['heart_rate'])
                                    <div class="vital-summary-card" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 15px; border-left: 4px solid #dc2626;">
                                        <div style="font-size: 12px; color: #b91c1c; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            💓 Frecuencia Cardíaca
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['heart_rate'] }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">bpm</div>
                                    </div>
                                @endif

                                <!-- Temperatura -->
                                @if($summary['temperature'])
                                    <div class="vital-summary-card" style="background: #fffbeb; border: 1px solid #fed7aa; border-radius: 12px; padding: 15px; border-left: 4px solid #f59e0b;">
                                        <div style="font-size: 12px; color: #d97706; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            🌡️ Temperatura
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['temperature'] }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">°C</div>
                                    </div>
                                @endif

                                <!-- Saturación de Oxígeno -->
                                @if($summary['oxygen_saturation'])
                                    <div class="vital-summary-card" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 15px; border-left: 4px solid #0ea5e9;">
                                        <div style="font-size: 12px; color: #0284c7; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            🫁 Saturación O2
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['oxygen_saturation'] }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">%</div>
                                    </div>
                                @endif

                                <!-- Peso -->
                                @if($summary['weight'])
                                    <div class="vital-summary-card" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 15px; border-left: 4px solid #059669;">
                                        <div style="font-size: 12px; color: #047857; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            ⚖️ Peso
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['weight'] }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">kg</div>
                                    </div>
                                @endif

                                <!-- Altura -->
                                @if($summary['height'])
                                    <div class="vital-summary-card" style="background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 12px; padding: 15px; border-left: 4px solid #7c3aed;">
                                        <div style="font-size: 12px; color: #6d28d9; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
                                            📏 Altura
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b;">
                                            {{ $summary['height'] }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">cm</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Detailed Vital Signs -->
                        <div class="vital-signs-detail" style="margin-bottom: 20px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                📊 Detalles de Signos Vitales ({{ $totalVitals }})
                            </h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($vitalSigns as $vital)
                                    <div class="vital-sign-item" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                                    <h5 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0;">
                                                        {{ $vital->observationType->name ?? 'Signo Vital' }}
                                                    </h5>
                                                    @if($vital->status)
                                                        <span style="background: #dc2626; color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                                                            {{ ucfirst($vital->status) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
                                                    <span><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($vital->effective_date)->format('d/m/Y H:i') }}</span>
                                                    @if($vital->practitioner)
                                                        <span><strong>👨‍⚕️ Registrado por:</strong> {{ $vital->practitioner->name }}</span>
                                                    @endif
                                                    @if($vital->code)
                                                        <span><strong>🏷️ Código:</strong> {{ $vital->code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: 18px; font-weight: 700; color: #dc2626;">
                                                    {{ $vital->value }}
                                                </div>
                                                <div style="font-size: 11px; color: #64748b;">
                                                    {{ $vital->observationType->default_unit ?? $vital->unit }}
                                                </div>
                                            </div>
                                        </div>
                                        @if($vital->note)
                                            <div style="margin-top: 10px; padding: 8px; background: rgba(59, 130, 246, 0.1); border-radius: 6px; font-size: 12px; color: #1e40af;">
                                                📝 <strong>Nota:</strong> {{ $vital->note }}
                                            </div>
                                        @endif
                                        @if($vital->interpretation)
                                            <div style="margin-top: 8px; padding: 8px; background: rgba(34, 197, 94, 0.1); border-radius: 6px; font-size: 12px; color: #059669;">
                                                🎯 <strong>Interpretación:</strong> {{ $vital->interpretation }}
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
                                    <span style="background: #dc2626; color: white; padding: 4px 8px; border-radius: 6px; text-decoration: none; font-size: 11px;">
                                        ❤️ {{ $totalVitals }} Signo{{ $totalVitals != 1 ? 's' : '' }} Vital{{ $totalVitals != 1 ? 'es' : '' }}
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
                        <button wire:click="previousVitalSignPage" 
                                class="pagination-btn" 
                                style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                                <span style="background: #dc2626; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoVitalSignPage({{ $page }})" 
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextVitalSignPage" 
                                class="pagination-btn" 
                                style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
        @include('patients.medicalHistory.vital-signs')
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">❤️</div>
            <h3>No hay signos vitales registrados</h3>
            <p>Este paciente no tiene signos vitales en el período seleccionado.</p>
        </div>
    @endif
</div>