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
        <!-- Tabla de Condiciones -->

        <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="padding: 15px; font-weight: 600; border: none;">Código</th>
                            <th style="padding: 15px; font-weight: 600; border: none;">Descripción</th>
                            <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Estado</th>
                            <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Severidad</th>
                            <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Fecha Dx</th>
                            <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach((isset($sectionData['data']) ? $sectionData['data'] : $sectionData) as $condition)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div style="font-family: monospace; font-weight: 600; color: #3b82f6; font-size: 14px;">
                                        {{ $condition->code }}
                                    </div>
                                    @if($condition->chronic)
                                        <span class="badge" style="background: #e0f2fe; color: #0277bd; font-size: 10px; margin-top: 4px;">
                                            ♻️ Crónica
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; max-width: 300px;">
                                    <div style="font-weight: 500; color: #1e293b; margin-bottom: 4px;">

                                        {{ $condition->onset_info ?? strtoupper($condition->icd10Code->description_es) }}
                                    </div>
                                    @if($condition->symptoms)
                                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                            <strong>Síntomas:</strong> {{ Str::limit($condition->symptoms, 60) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                    <span class="badge badge-{{ $condition->clinical_status === 'active' ? 'active' : ($condition->clinical_status === 'resolved' ? 'resolved' : 'inactive') }}" style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
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
                                                🔵 Remisión
                                                @break
                                            @default
                                                {{ ucfirst($condition->clinical_status) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                    @if($condition->severity)
                                        <span class="badge" style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                            @if($condition->severity === 'severe') background: #fee2e2; color: #dc2626;
                                            @elseif($condition->severity === 'moderate') background: #fef3c7; color: #92400e;
                                            @else background: #f0fdf4; color: #166534; @endif">
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
                                    @else
                                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                    <div style="font-size: 13px; color: #475569; font-weight: 500;">
                                        {{ $condition->onset_date ? Carbon\Carbon::parse($condition->onset_date)->format('d/m/Y') : '-' }}
                                    </div>
                                    @if($condition->resolution_date)
                                        <div style="font-size: 11px; color: #059669; margin-top: 2px;">
                                            ✓ {{ Carbon\Carbon::parse($condition->resolution_date)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                    <button type="button" class="btn btn-sm" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; padding: 6px 12px; border-radius: 6px;"
                                            data-bs-toggle="modal" data-bs-target="#conditionModal{{ $condition->id }}"
                                            title="Ver detalles">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal de detalles -->
                            <div class="modal fade" id="conditionModal{{ $condition->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white;">
                                            <h5 class="modal-title">🩺 Detalles de la Condición</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #64748b; font-size: 12px;">CÓDIGO ICD-10:</strong>
                                                    <div style="font-family: monospace; font-size: 16px; color: #3b82f6; font-weight: 600;">{{ $condition->code }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong style="color: #64748b; font-size: 12px;">CATEGORÍA:</strong>
                                                    <div style="font-size: 14px; color: #1e293b;">{{ $condition->category ?? 'No especificado' }}</div>
                                                </div>
                                            </div>

                                            @if($condition->description || $condition->onset_info)
                                                <div class="mb-3">
                                                    <strong style="color: #64748b; font-size: 12px;">DESCRIPCIÓN:</strong>
                                                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px; margin-top: 5px;">
                                                        {{ $condition->onset_info ?? $condition->description }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($condition->symptoms)
                                                <div class="mb-3">
                                                    <strong style="color: #dc2626; font-size: 12px;">SÍNTOMAS:</strong>
                                                    <div style="background: #fef2f2; padding: 12px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #dc2626;">
                                                        {{ $condition->symptoms }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($condition->current_treatment)
                                                <div class="mb-3">
                                                    <strong style="color: #059669; font-size: 12px;">TRATAMIENTO ACTUAL:</strong>
                                                    <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #059669;">
                                                        {{ $condition->current_treatment }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($condition->prognosis)
                                                <div class="mb-3">
                                                    <strong style="color: #7c3aed; font-size: 12px;">PRONÓSTICO:</strong>
                                                    <div style="background: #faf5ff; padding: 12px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #7c3aed;">
                                                        {{ $condition->prognosis }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($condition->notes)
                                                <div class="mb-3">
                                                    <strong style="color: #f59e0b; font-size: 12px;">NOTAS MÉDICAS:</strong>
                                                    <div style="background: #fffbeb; padding: 12px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #f59e0b; font-style: italic;">
                                                        {{ $condition->notes }}
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row mt-3">
                                                <div class="col-md-4">
                                                    <strong style="color: #64748b; font-size: 12px;">FECHA INICIO:</strong>
                                                    <div>{{ $condition->onset_date ? Carbon\Carbon::parse($condition->onset_date)->format('d/m/Y') : 'No especificado' }}</div>
                                                </div>
                                                @if($condition->resolution_date)
                                                    <div class="col-md-4">
                                                        <strong style="color: #64748b; font-size: 12px;">FECHA RESOLUCIÓN:</strong>
                                                        <div>{{ Carbon\Carbon::parse($condition->resolution_date)->format('d/m/Y') }}</div>
                                                    </div>
                                                @endif
                                                @if($condition->diagnosis_date)
                                                    <div class="col-md-4">
                                                        <strong style="color: #64748b; font-size: 12px;">FECHA DIAGNÓSTICO:</strong>
                                                        <div>{{ Carbon\Carbon::parse($condition->diagnosis_date)->format('d/m/Y') }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
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
