<div class="medical-requests-content">
    @if($sectionData && isset($sectionData['grouped_by_encounter']) && count($sectionData['grouped_by_encounter']) > 0)
        <div class="encounters-grid" style="display: grid; gap: 30px;">
            @foreach($sectionData['grouped_by_encounter'] as $encounterId => $encounterGroup)
                @php
                    $encounter = $encounterGroup['encounter'];
                    $medications = $encounterGroup['medications'];
                    $services = $encounterGroup['services'];
                    $totalItems = $medications->count() + $services->count();
                @endphp

                <!-- Encounter Card -->
                <div class="encounter-card" style="background: white; border: 2px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">

                    <!-- Encounter Header -->
                    <div class="encounter-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px;">
                                    🏥 Consulta Médica
                                    @if($encounter)
                                        {!!  $encounter->status !!}
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
                                <div style="font-size: 24px; font-weight: 700;">{{ $totalItems }}</div>
                                <div style="font-size: 12px; opacity: 0.9;">{{ $totalItems == 1 ? 'orden' : 'órdenes' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Content -->
                    <div style="padding: 25px;">

                        <!-- Medications Section -->
                        @if($medications->count() > 0)
                            <div class="medications-section" style="margin-bottom: 30px;">
                                <h3 style="font-size: 18px; font-weight: 600; color: #059669; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #d1fae5; padding-bottom: 8px;">
                                    💊 Medicamentos Prescritos ({{ $medications->count() }})
                                </h3>
                                <div style="display: grid; gap: 15px;">
                                    @foreach($medications as $medication)
                                        <div class="medication-item" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; border-left: 4px solid #059669;">
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                                <div>
                                                    <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">
                                                        {{ $medication->medicine ? $medication->medicine->full_name : $medication->medication }}
                                                        {{ $medication->medication2 ? $medication->medication2->display : $medication->medication2 }}
                                                    </h4>
                                                    <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 13px; color: #374151;">
                                                        {{--}}
                                                        @if($medication->dosage_text)
                                                            <span><strong>📏 Dosis:</strong> {{ $medication->dosage_text }}</span>
                                                        @endif
                                                        {{--}}
                                                        @if($medication->frequency)
                                                            <span><strong>⏰ Frecuencia:</strong> {{ $medication->frequency }}</span>
                                                        @endif
                                                        @if($medication->duration)
                                                            <span><strong>📅 Duración:</strong> {{ $medication->duration }}</span>
                                                        @endif
                                                        @if($medication->route)
                                                            <span><strong>🎯 Vía:</strong> {{ $medication->route }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                    @switch($medication->status)
                                                        @case('active') ✅ Activo @break
                                                        @case('completed') ✔️ Completado @break
                                                        @case('cancelled') ❌ Cancelado @break
                                                        @case('stopped') ⏹️ Suspendido @break
                                                        @default {!!  ucfirst($medication->status) !!}
                                                    @endswitch

                                            </div>
                                            @if($medication->dosage_instruction)
                                                <div style="background: white; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151; border: 1px solid #d1fae5;">
                                                    <strong>📋 Instrucciones:</strong>
                                                    {{$medication->dosage_text}}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Services Section -->
                        @if($services->count() > 0)
                            <div class="services-section">
                                <h3 style="font-size: 18px; font-weight: 600; color: #0891b2; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #cffafe; padding-bottom: 8px;">
                                    🩺 Servicios y Estudios Solicitados ({{ $services->count() }})
                                </h3>
                                <div style="display: grid; gap: 15px;">
                                    @foreach($services as $service)
                                        <div class="service-item" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 20px; border-left: 4px solid #0891b2;">
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                                        @switch($service->cpt->type ?? 'procedure')
                                                            @case('laboratory')
                                                                <span style="font-size: 20px;">🧪</span>
                                                                @break
                                                            @case('images')
                                                                <span style="font-size: 20px;">📸</span>
                                                                @break
                                                            @case('procedure')
                                                                <span style="font-size: 20px;">🩺</span>
                                                                @break
                                                            @default
                                                                <span style="font-size: 20px;">📋</span>
                                                        @endswitch
                                                        <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0;">
                                                            {{ $service->code_display ?? ($service->cpt->description ?? 'Servicio no especificado') }}
                                                        </h4>
                                                    </div>
                                                    <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 13px; color: #374151; margin-bottom: 10px;">
                                                        <span><strong>🏷️ Código:</strong> {{ $service->code }}</span>
                                                        @if($service->cpt)
                                                            <span><strong>📋 Tipo:</strong> {{ ucfirst($service->cpt->type) }}</span>
                                                        @endif
                                                        @if($service->quantity)
                                                            <span><strong>📊 Cantidad:</strong> {{ $service->quantity }}</span>
                                                        @endif
                                                        @if($service->occurrence_start)
                                                            <span><strong>📅 Programado:</strong> {{ \Carbon\Carbon::parse($service->occurrence_start)->format('d/m/Y') }}</span>
                                                        @endif
                                                    </div>
                                                    @if($service->note || $service->patient_instruction)
                                                        <div style="background: white; padding: 10px; border-radius: 8px; font-size: 13px; color: #374151; border: 1px solid #bae6fd;">
                                                            @if($service->note)
                                                                <div><strong>📝 Notas:</strong> {{ $service->note }}</div>
                                                            @endif
                                                            @if($service->patient_instruction)
                                                                <div><strong>👤 Instrucciones al paciente:</strong> {{ $service->patient_instruction }}</div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Resultados de Laboratorio (Observaciones) -->
                                                    @if($service->observations()->count() > 0)
                                                        <div style="background: white; padding: 15px; border-radius: 10px; margin-top: 12px; border: 2px solid #3b82f6;">
                                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                                                <div style="font-size: 14px; font-weight: 700; color: #1e40af;">
                                                                    🧪 Resultados de Laboratorio
                                                                </div>
                                                                <span style="background: #dbeafe; color: #1e40af; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                                                    {{ $service->observations()->count() }} resultados
                                                                </span>
                                                            </div>

                                                            @if(in_array($service->code, ['85025', '85027']))
                                                                <!-- Código HemoScreen para CBC -->
                                                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 8px;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 16 16">
                                                                        <path d="M8.06 6.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 0 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V7a.5.5 0 0 1 .5-.5Z"/>
                                                                        <path d="M6.06 7a2 2 0 1 1 4 0 .5.5 0 1 1-1 0 1 1 0 1 0-2 0v.332q0 .613-.066 1.221A.5.5 0 0 1 6 8.447q.06-.555.06-1.115zm3.509 1a.5.5 0 0 1 .5.5v.67q0 .613-.066 1.221a.5.5 0 1 1-.994-.112q.06-.555.06-1.109V8.5a.5.5 0 0 1 .5-.5"/>
                                                                    </svg>
                                                                    <div>
                                                                        <div style="color: white; font-size: 9px; opacity: 0.9;">HemoScreen</div>
                                                                        <div style="color: white; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; font-family: monospace;">
                                                                            {{ $service->hemo_identification ?? 'N/A' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;">
                                                                @foreach($service->observations as $observation)
                                                                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                                        <div style="font-size: 10px; color: #6b7280; font-weight: 600; margin-bottom: 4px;">
                                                                            {{ \App\Enums\LoincCode::getShortLabel($observation->code) }}
                                                                        </div>
                                                                        <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">
                                                                            {{ $observation->value }}
                                                                            <span style="font-size: 11px; color: #64748b; font-weight: 500;">{{ $observation->unit }}</span>
                                                                        </div>
                                                                        @if($observation->status === 'final')
                                                                            <div style="font-size: 9px; color: #059669;">✓ Final</div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            @if($service->observations->first() && $service->observations->first()->issued_date)
                                                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #64748b;">
                                                                    <strong>Resultados emitidos:</strong> {{ \Carbon\Carbon::parse($service->observations->first()->issued_date)->format('d/m/Y H:i') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div style="display: flex; flex-direction: column; gap: 8px; align-items: end;">
                                                    <span class="badge badge-{{ $service->status === 'completed' ? 'resolved' : ($service->status === 'cancelled' ? 'inactive' : ($service->status === 'in-progress' ? 'active' : 'pending')) }}" style="font-size: 11px;">
                                                        @switch($service->status)
                                                            @case('draft') ⏳ Borrador @break
                                                            @case('active') ✅ Activa @break
                                                            @case('completed') ✔️ Completada @break
                                                            @case('cancelled') ❌ Cancelada @break
                                                            @default {{ ucfirst($service->status) }}
                                                        @endswitch
                                                    </span>
                                                    @if($service->priority && $service->priority !== 'routine')
                                                        <span class="priority-badge" style="font-size: 10px; padding: 3px 6px; border-radius: 6px; font-weight: 600;
                                                            @if($service->priority === 'urgent') background: #fee2e2; color: #dc2626;
                                                            @elseif($service->priority === 'asap') background: #fef3c7; color: #92400e;
                                                            @elseif($service->priority === 'stat') background: #f3e8ff; color: #7c3aed;
                                                            @else background: #f0fdf4; color: #166534; @endif">
                                                            @switch($service->priority)
                                                                @case('urgent') 🚨 URGENTE @break
                                                                @case('asap') ⚡ ASAP @break
                                                                @case('stat') ⚡⚡ STAT @break
                                                                @default {{ strtoupper($service->priority) }}
                                                            @endswitch
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Encounter Actions -->
                        @if($encounter)
                            <div class="encounter-actions" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 12px; color: #64748b;">
                                    Consulta ID: {{ $encounter->id }} | Creado: {{ \Carbon\Carbon::parse($encounter->end)->format('d/m/Y H:i') }}
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    @if($medications->count() > 0)
                                        <a href="{{route('prescription.download',$encounter->id)}}" target="_blank"
                                           class="btn btn-sm" style="background: #059669; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
                                            💊 Descargar Receta
                                        </a>
                                    @endif
                                    @if($services->count() > 0)
                                        <a href="{{route('medical-order.download',$encounter->id)}}" target="_blank"
                                           class="btn btn-sm" style="background: #0891b2; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
                                            📋 Descargar Orden Médica
                                        </a>
                                    @endif
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
                        <button wire:click="previousEncounterPage"
                                class="pagination-btn"
                                style="background: #667eea; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                                <span style="background: #667eea; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoEncounterPage({{ $page }})"
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextEncounterPage"
                                class="pagination-btn"
                                style="background: #667eea; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
    @elseif($sectionData && count($sectionData['types']) > 0)
        <!-- Fallback to original view if no grouped data -->
        @include('patients.medicalHistory.medical-requests')
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📋</div>
            <h3>No hay órdenes médicas registradas</h3>
            <p>Este paciente no tiene órdenes médicas en el período seleccionado.</p>
        </div>
    @endif
</div>
