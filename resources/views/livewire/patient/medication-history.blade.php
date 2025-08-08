<div>
    @if($showOffcanvas)
        <!-- Offcanvas -->
        <div class="offcanvas offcanvas-end show" tabindex="-1" id="medicationHistoryOffcanvas"
             style="visibility: visible; width: 600px;" aria-labelledby="medicationHistoryOffcanvasLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="medicationHistoryOffcanvasLabel">
                    <i class="fas fa-pills me-2"></i>
                    Historial de Medicamentos - {{ $patient->name ?? 'Paciente' }}
                </h5>
                <button type="button" class="btn-close" wire:click="hideMedicationHistory" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0">
                @if($groupedByEncounter->isEmpty())
                    <div class="text-center p-4">
                        <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay medicamentos registrados</h5>
                        <p class="text-muted">En los últimos 6 meses no se encontraron medicamentos para este paciente.</p>
                    </div>
                @else
                    <!-- Botones de acción -->
                    <div class="sticky-top bg-white p-3 border-bottom">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm flex-fill" wire:click="copySelectedMedications"
                                    @if(empty($selectedMedicationIds)) disabled @endif>
                                <i class="fas fa-copy me-1"></i>
                                Copiar Seleccionados ({{ count($selectedMedicationIds) }})
                            </button>
                        </div>
                    </div>

                    <!-- Lista de recetas agrupadas por consulta -->
                    <div class="px-3">
                        @foreach($groupedByEncounter as $encounterId => $encounterData)
                            <?php
                            $encounter = \App\Models\Encounter::withoutGlobalScope(\App\Models\Scopes\EncouterScope::class)->whereId($encounterId)->first();
                            ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <h6 class="mb-1 text-white">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $encounter->created_at ?? 'Fecha no disponible' }}
                                        </h6>
                                        <small class="text-white">
                                            <i class="fa fa-user-md me-2"></i>
                                            {{ $encounter->practitioner->name ?? 'No especificado' }}
                                        </small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm"
                                            wire:click="selectEntireRecipe({{ $encounterId }})">
                                        @if($selectedEncounterId === $encounterId && !empty(array_intersect($encounterData['medications']->pluck('id')->toArray(), $selectedMedicationIds)))
                                            <i class="fas fa-check-square me-1"></i>Deseleccionar Receta
                                        @else
                                            <i class="far fa-square me-1"></i>Seleccionar Receta
                                        @endif
                                    </button>
                                </div>

                                <div class="card-body p-0">
                                    @foreach($encounterData['medications'] as $medication)
                                        <div class="medication-item p-3 border-bottom"
                                             style="cursor: pointer; {{ in_array($medication->id, $selectedMedicationIds) ? 'background-color: #f0f8ff;' : '' }}"
                                             wire:click="toggleMedicationSelection({{ $medication->id }})">

                                            <div class="d-flex align-items-start">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                           {{ in_array($medication->id, $selectedMedicationIds) ? 'checked' : '' }}
                                                           readonly>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-capsules me-2 text-primary"></i>
                                                        {{ $medication->medicine->full_name ?? 'Medicamento no especificado' }}
                                                    </h6>

                                                    <div class="medication-details">
                                                        @if($medication->dosage_text)
                                                            <p class="mb-1">
                                                                <strong>Indicaciones:</strong> {{ $medication->dosage_text }}
                                                            </p>
                                                        @endif

                                                        <div class="row text-sm">
                                                            @if($medication->quantity)
                                                                <div class="col-6">
                                                                    <small><strong>Cantidad:</strong> {{ $medication->quantity }}</small>
                                                                </div>
                                                            @endif
                                                            @if($medication->frequency)
                                                                <div class="col-6">
                                                                    <small><strong>Frecuencia:</strong> Cada {{ $medication->frequency }} horas</small>
                                                                </div>
                                                            @endif
                                                            @if($medication->route)
                                                                <div class="col-6">
                                                                    <small><strong>Vía:</strong> {{ $medication->route }}</small>
                                                                </div>
                                                            @endif
                                                            @if($medication->duration)
                                                                <div class="col-6">
                                                                    <small><strong>Duración:</strong> {{ $medication->duration }} días</small>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if($medication->refills)
                                                            <div class="mt-2">
                                                                <span class="badge bg-info">
                                                                    <i class="fas fa-repeat me-1"></i>
                                                                    {{ $medication->refills }} meses de refill
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Backdrop -->
        <div class="offcanvas-backdrop fade show" wire:click="hideMedicationHistory"></div>
    @endif

        <style>
            .medication-item:hover {
                background-color: #f8f9fa !important;
            }

            .medication-details small {
                color: #6c757d;
            }

            .offcanvas {
                z-index: 1055;
            }

            .offcanvas-backdrop {
                z-index: 1054;
            }

            .sticky-top {
                z-index: 1020;
            }
        </style>
</div>

