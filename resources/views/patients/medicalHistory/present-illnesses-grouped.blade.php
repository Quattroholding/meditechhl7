<div class="present-illnesses-content">
    @if($sectionData && isset($sectionData['grouped_by_encounter']) && count($sectionData['grouped_by_encounter']) > 0)
        <div class="encounters-grid" style="display: grid; gap: 30px;">
            @foreach($sectionData['grouped_by_encounter'] as $encounterId => $encounterGroup)
                @php
                    $encounter = $encounterGroup['encounter'];
                    $illnesses = $encounterGroup['present_illnesses'];
                    $totalIllnesses = $illnesses->count();
                @endphp

                <!-- Encounter Card -->
                <div class="encounter-card" style="background: white; border: 2px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">

                    <!-- Encounter Header -->
                    <div class="encounter-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 20px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px;">
                                    🤒 Consulta Médica - Enfermedad Actual
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
                                        @if($encounter->reason)
                                            <div style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
                                                📋 {{ $encounter->reason }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 24px; font-weight: 700;">{{ $totalIllnesses }}</div>
                                <div style="font-size: 12px; opacity: 0.9;">{{ $totalIllnesses == 1 ? 'padecimiento' : 'padecimientos' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Present Illnesses Content -->
                    <div style="padding: 25px;">
                        @foreach($illnesses as $illness)
                            <div class="illness-item" style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #8b5cf6;">

                                <!-- Illness Header -->
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                    <div>
                                        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">
                                            🩺 Padecimiento Actual
                                        </h3>
                                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                            @if($illness->severity)
                                                <span class="badge" style="background:
                                                    @if($illness->severity === 'severe') #fee2e2; color: #dc2626;
                                                    @elseif($illness->severity === 'moderate') #fef3c7; color: #92400e;
                                                    @else #f0fdf4; color: #166534; @endif
                                                    padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                    {{ ucfirst($illness->severity) }}
                                                </span>
                                            @endif
                                            @if($illness->status)
                                                <span class="badge" style="background: {{ $illness->status === 'active' ? '#dbeafe' : '#f3f4f6' }}; color: {{ $illness->status === 'active' ? '#1e40af' : '#6b7280' }}; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                    {{ ucfirst($illness->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="text-align: right; font-size: 12px; color: #64748b;">
                                        @if($illness->created_at)
                                            <div>📅 {{ Carbon\Carbon::parse($illness->created_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Historia de la Enfermedad Actual -->
                                @if($illness->history_present_illness)
                                    <div style="margin-bottom: 15px;">
                                        <div style="font-size: 13px; font-weight: 600; color: #6d28d9; margin-bottom: 8px;">
                                            📖 Historia de la Enfermedad Actual
                                        </div>
                                        <div style="background: white; padding: 12px; border-radius: 8px; line-height: 1.6; font-size: 13px; color: #374151;">
                                            {{ $illness->history_present_illness }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Descripción -->
                                @if($illness->description)
                                    <div style="margin-bottom: 15px;">
                                        <div style="font-size: 13px; font-weight: 600; color: #6d28d9; margin-bottom: 8px;">
                                            🎯 Descripción
                                        </div>
                                        <div style="background: white; padding: 12px; border-radius: 8px; line-height: 1.6; font-size: 13px; color: #374151;">
                                            {{ $illness->description }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Características Clínicas -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-bottom: 15px;">
                                    @if($illness->onset_date)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">⏱️ Inicio</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ Carbon\Carbon::parse($illness->onset_date)->format('d/m/Y') }}</div>
                                        </div>
                                    @endif
                                    @if($illness->duration)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">⏳ Duración</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $illness->duration }}</div>
                                        </div>
                                    @endif
                                    @if($illness->location)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">📍 Localización</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $illness->location }}</div>
                                        </div>
                                    @endif
                                    @if($illness->quality)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">🔍 Calidad</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $illness->quality }}</div>
                                        </div>
                                    @endif
                                    @if($illness->radiation)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">↗️ Irradiación</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $illness->radiation }}</div>
                                        </div>
                                    @endif
                                    @if($illness->timing)
                                        <div style="background: white; padding: 10px; border-radius: 8px;">
                                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600; margin-bottom: 3px;">⏰ Cronología</div>
                                            <div style="font-size: 13px; color: #1e293b; font-weight: 500;">{{ $illness->timing }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Escala de Dolor -->
                                @if($illness->pain_scale)
                                    <div style="margin-bottom: 15px;">
                                        <div style="font-size: 13px; font-weight: 600; color: #6d28d9; margin-bottom: 8px;">
                                            🌡️ Escala de Dolor
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; background: white; padding: 12px; border-radius: 8px;">
                                            <div style="display: flex; gap: 4px;">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <div style="width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600;
                                                        background: {{ $i <= $illness->pain_scale ? ($i <= 3 ? '#22c55e' : ($i <= 6 ? '#f59e0b' : '#ef4444')) : '#e5e7eb' }};
                                                        color: {{ $i <= $illness->pain_scale ? 'white' : '#9ca3af' }};">
                                                        {{ $i }}
                                                    </div>
                                                @endfor
                                            </div>
                                            <div style="font-size: 18px; font-weight: 700; color: #8b5cf6;">
                                                {{ $illness->pain_scale }}/10
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Factores y Síntomas -->
                                @if($illness->aggravating_factors || $illness->associated_symptoms)
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;">
                                        @if($illness->aggravating_factors)
                                            <div>
                                                <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 6px;">
                                                    ⚠️ Factores Agravantes
                                                </div>
                                                <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 12px; color: #374151;">
                                                    {{ $illness->aggravating_factors }}
                                                </div>
                                            </div>
                                        @endif
                                        @if($illness->associated_symptoms)
                                            <div>
                                                <div style="font-size: 12px; color: #d97706; font-weight: 600; margin-bottom: 6px;">
                                                    🔗 Síntomas Asociados
                                                </div>
                                                <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 12px; color: #374151;">
                                                    {{ $illness->associated_symptoms }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- Encounter Footer -->
                        @if($encounter)
                            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e9d5ff; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 12px; color: #64748b;">
                                    Consulta ID: {{ $encounter->id }} | {{ \Carbon\Carbon::parse($encounter->start)->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <span style="background: #8b5cf6; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        🤒 {{ $totalIllnesses }} Padecimiento{{ $totalIllnesses != 1 ? 's' : '' }}
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
                        <button wire:click="previousPresentIllnessPage"
                                class="pagination-btn"
                                style="background: #8b5cf6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                                <span style="background: #8b5cf6; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPresentIllnessPage({{ $page }})"
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextPresentIllnessPage"
                                class="pagination-btn"
                                style="background: #8b5cf6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                de {{ $sectionData['total'] ?? 0 }} total
            </div>
        @endif
    @elseif($sectionData && (isset($sectionData['data']) ? count($sectionData['data']) > 0 : (is_countable($sectionData) ? count($sectionData) > 0 : !empty($sectionData))))
        <!-- Fallback to original view if no grouped data -->
        @include('patients.medicalHistory.present-illnesses')
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🤒</div>
            <h3>No hay enfermedades actuales registradas</h3>
            <p>Este paciente no tiene enfermedades actuales en el período seleccionado.</p>
        </div>
    @endif
</div>