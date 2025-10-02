<div class="conditions-content">
    <!-- Search Bar -->
    <div style="margin-bottom: 25px;">
        <div style="position: relative; max-width: 500px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="conditionsSearchTerm"
                placeholder="🔍 Buscar por nombre o código ICD-10..."
                style="width: 100%; padding: 12px 40px 12px 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; outline: none; transition: all 0.3s ease;"
                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.1)'"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
            >
            @if($conditionsSearchTerm)
                <button
                    wire:click="$set('conditionsSearchTerm', '')"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: #94a3b8; font-size: 18px; padding: 5px;"
                    title="Limpiar búsqueda"
                >
                    ✕
                </button>
            @endif
        </div>
        @if($conditionsSearchTerm)
            <div style="margin-top: 10px; font-size: 13px; color: #64748b;">
                Buscando: <strong style="color: #7c3aed;">{{ $conditionsSearchTerm }}</strong>
            </div>
        @endif
    </div>

    @if($sectionData && (isset($sectionData['data']) ? count($sectionData['data']) > 0 : count($sectionData) > 0))
        <div class="conditions-grid" style="display: grid; gap: 20px;">
            @foreach((isset($sectionData['data']) ? $sectionData['data'] : $sectionData) as $condition)

                <div class="condition-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                    <div class="condition-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                                🩺 {{ $condition->code }}
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <span class="badge badge-{{ $condition->clinical_status === 'active' ? 'active' : ($condition->clinical_status === 'resolved' ? 'resolved' : 'inactive') }}">
                                    @switch($condition->clinical_status)
                                        @case('active')
                                            🟢 Activa
                                            @break
                                        @case('resolved')
                                            ✅ Resuelta
                                            @break
                                        @case('inactive')
                                            ⚫ Inactiva
                                            @break
                                        @case('remission')
                                            🔵 En Remisión
                                            @break
                                        @default
                                            {{ ucfirst($condition->clinical_status) }}
                                    @endswitch
                                </span>
                                @if($condition->severity)
                                    <span class="badge" style="background:
                                        @if($condition->severity === 'severe') #fee2e2; color: #dc2626;
                                        @elseif($condition->severity === 'moderate') #fef3c7; color: #92400e;
                                        @else #f0fdf4; color: #166534; @endif">
                                        @switch($condition->severity)
                                            @case('severe')
                                                🔴 Severa
                                                @break
                                            @case('moderate')
                                                🟡 Moderada
                                                @break
                                            @case('mild')
                                                🟢 Leve
                                                @break
                                            @default
                                                {{ ucfirst($condition->severity) }}
                                        @endswitch
                                    </span>
                                @endif
                                @if($condition->chronic)
                                    <span class="badge" style="background: #e0f2fe; color: #0277bd;">
                                        ♻️ Crónica
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>Inicio:</strong> {{ $condition->onset_date ? Carbon\Carbon::parse($condition->onset_date)->format('d/m/Y') : 'No especificado' }}</div>
                            @if($condition->resolution_date)
                                <div><strong>Resolución:</strong> {{ Carbon\Carbon::parse($condition->resolution_date)->format('d/m/Y') }}</div>
                            @endif
                            @if($condition->diagnosis_date)
                                <div><strong>Diagnóstico:</strong> {{ Carbon\Carbon::parse($condition->diagnosis_date)->format('d/m/Y') }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Descripción de la Condición -->
                    @if($condition->description)
                        <div style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 5px; font-weight: 600;">📝 DESCRIPCIÓN</div>
                            <div style="color: #374151; line-height: 1.6;">{{ $condition->onset_info }}</div>
                        </div>
                    @endif

                    <!-- Información Clínica -->
                    <div class="clinical-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        @if($condition->icd_10_code)
                            <div>
                                <div style="font-size: 12px; color: #3b82f6; font-weight: 600; margin-bottom: 5px;">🏷️ Código ICD-10</div>
                                <div style="background: #dbeafe; padding: 10px; border-radius: 8px; font-size: 13px; color: #1e40af; font-family: monospace;">
                                    {{ $condition->code }}
                                </div>
                            </div>
                        @endif

                        @if($condition->category)
                            <div>
                                <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">📂 Categoría</div>
                                <div style="background: #d1fae5; padding: 10px; border-radius: 8px; font-size: 13px; color: #065f46;">
                                    {{ $condition->category }}
                                </div>
                            </div>
                        @endif

                        @if($condition->body_system)
                            <div>
                                <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">🫀 Sistema Corporal</div>
                                <div style="background: #ede9fe; padding: 10px; border-radius: 8px; font-size: 13px; color: #5b21b6;">
                                    {{ $condition->body_system }}
                                </div>
                            </div>
                        @endif

                        @if($condition->stage)
                            <div>
                                <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">📊 Estadio</div>
                                <div style="background: #fed7aa; padding: 10px; border-radius: 8px; font-size: 13px; color: #9a3412;">
                                    {{ $condition->stage }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Síntomas Principales -->
                    @if($condition->symptoms)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 8px;">🤒 SÍNTOMAS PRINCIPALES</div>
                            <div style="background: #fef2f2; padding: 12px; border-radius: 8px; border-left: 4px solid #dc2626;">
                                {{ $condition->symptoms }}
                            </div>
                        </div>
                    @endif

                    <!-- Tratamiento Actual -->
                    @if($condition->current_treatment)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 8px;">💊 TRATAMIENTO ACTUAL</div>
                            <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; border-left: 4px solid #059669;">
                                {{ $condition->current_treatment }}
                            </div>
                        </div>
                    @endif

                    <!-- Pronóstico -->
                    @if($condition->prognosis)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 8px;">🔮 PRONÓSTICO</div>
                            <div style="background: #faf5ff; padding: 12px; border-radius: 8px; border-left: 4px solid #7c3aed;">
                                {{ $condition->prognosis }}
                            </div>
                        </div>
                    @endif

                    <!-- Notas del Médico -->
                    @if($condition->notes)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 8px;">📋 NOTAS MÉDICAS</div>
                            <div style="background: #fffbeb; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b; font-style: italic;">
                                {{ $condition->notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Footer con Metadatos -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                        <div>
                            @if($condition->encounter)
                                <span>📅 Consulta: {{ Carbon\Carbon::parse($condition->encounter->encounter_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            @if($condition->updated_at)
                                <span>✏️ Actualizado: {{ Carbon\Carbon::parse($condition->updated_at)->diffForHumans() }}</span>
                            @endif
                        </div>
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
                        <button wire:click="previousConditionsPage"
                                class="pagination-btn"
                                style="background: #7c3aed; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                                <span style="background: #7c3aed; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoConditionsPage({{ $page }})"
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextConditionsPage"
                                class="pagination-btn"
                                style="background: #7c3aed; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
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
                Mostrando condiciones {{ $sectionData['from'] ?? 0 }} a {{ $sectionData['to'] ?? 0 }}
                de {{ $sectionData['total'] ?? 0 }} total
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🩺</div>
            <h3>No hay condiciones médicas registradas</h3>
            <p>Este paciente no tiene condiciones médicas en el período seleccionado.</p>
        </div>
    @endif
</div>
