<div class="vaccination-record-section">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-syringe me-2"></i>
                Registro de Vacunación
            </h5>
        </div>
        <div class="card-body">
            {{-- Patient Age Info --}}
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Paciente:</strong> {{ $patient->name ?? 'N/A' }} |
                <strong>Edad:</strong> {{ round($patientAgeMonths,0) }} meses ({{ floor($patientAgeMonths / 12) }} años, {{ $patientAgeMonths % 12 }} meses)
            </div>

            {{-- Vaccination Schedule Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Vacuna</th>
                            <th style="width: 15%;">Enfermedad</th>
                            <th style="width: 10%;">Edad Mín.</th>
                            <th style="width: 10%;">Dosis Req.</th>
                            <th style="width: 30%;">Dosis Administradas</th>
                            <th style="width: 10%;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccinationSchedule as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['vaccine_type']->localized_name }}</strong>
                                    <br>
                                    <small class="text-muted">CVX: {{ $item['vaccine_type']->cvx_code }}</small>
                                </td>
                                <td>
                                    <small>{{ ucfirst(str_replace('_', ' ', $item['vaccine_type']->target_disease)) }}</small>
                                </td>
                                <td>
                                    {{ $item['vaccine_type']->min_age_months }} meses
                                </td>
                                <td class="text-center">
                                    {{ $item['vaccine_type']->doses_required }}
                                </td>
                                <td>
                                    {{-- Dose badges --}}
                                    @if($item['given_doses']->isNotEmpty())
                                        @foreach($item['given_doses'] as $dose)
                                            <span class="badge bg-success me-1 mb-1"
                                                  style="cursor: pointer;"
                                                  wire:click="editImmunization({{ $dose->id }})"
                                                  title="Dosis {{ $dose->dose_number }} - {{ $dose->occurrence_datetime->format('d/m/Y') }}">
                                                Dosis {{ $dose->dose_number }}: {{ $dose->occurrence_datetime->format('d/m/Y') }}
                                                @if($dose->isExpired())
                                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Sin dosis registradas</span>
                                    @endif

                                    {{-- Add dose button --}}
                                    @if($item['status'] !== 'complete')
                                        @if($item['is_age_appropriate'])
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary ms-2"
                                                    wire:click="addVaccine({{ $item['vaccine_type']->id }})">
                                                <i class="fas fa-plus"></i> Agregar Dosis {{ $item['next_dose_number'] }}
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary ms-2"
                                                    wire:click="addVaccine({{ $item['vaccine_type']->id }})"
                                                    title="Registrar dosis histórica administrada fuera del rango de edad recomendado">
                                                <i class="fas fa-history"></i> Registrar Dosis {{ $item['next_dose_number'] }}
                                            </button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($item['status'] === 'complete')
                                        <span class="badge bg-success">Completo</span>
                                    @elseif($item['status'] === 'overdue')
                                        <span class="badge bg-danger">Atrasado</span>
                                    @elseif($item['status'] === 'due')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($item['status'] === 'partial')
                                        <span class="badge bg-info">Parcial</span>
                                    @elseif($item['status'] === 'pending')
                                        <span class="badge bg-secondary">Próximo</span>
                                    @else
                                        <span class="badge bg-light text-dark">No aplica</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No hay vacunas configuradas en el sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="mt-3">
                <small class="text-muted">
                    <strong>Leyenda:</strong>
                    <span class="badge bg-success ms-2">Completo</span>
                    <span class="badge bg-warning ms-1">Pendiente</span>
                    <span class="badge bg-danger ms-1">Atrasado</span>
                    <span class="badge bg-info ms-1">Parcial</span>
                    <span class="badge bg-secondary ms-1">Próximo</span>
                    <span class="badge bg-light text-dark ms-1">No aplica</span>
                </small>
            </div>
        </div>
    </div>

    {{-- Modal: Add/Edit Vaccine --}}
    @if($showVaccineModal)
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" style="color:#fff;">
                            <i class="fas fa-syringe me-2"></i>
                            {{ $editingImmunizationId ? 'Editar Inmunización' : 'Nueva Inmunización' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveImmunization">
                            {{-- Vaccine Type --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Vacuna <span class="text-danger">*</span></label>
                                    <select class="form-select @error('vaccine_type_id') is-invalid @enderror"
                                            wire:model.live="vaccine_type_id"
                                            {{ $editingImmunizationId ? 'disabled' : '' }}>
                                        <option value="">Seleccione...</option>
                                        @foreach(\App\Models\VaccineType::active()->ordered()->get() as $vt)
                                            <option value="{{ $vt->id }}">{{ $vt->localized_name }} (CVX: {{ $vt->cvx_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('vaccine_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                           class="form-control @error('occurrence_datetime') is-invalid @enderror"
                                           wire:model="occurrence_datetime">
                                    @error('occurrence_datetime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Lot Information --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-barcode me-2"></i>Información del Lote</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Número de Lote</label>
                                            <input type="text" class="form-control" wire:model="lot_number" placeholder="ej: A123456">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fecha de Vencimiento</label>
                                            <input type="date"
                                                   class="form-control @error('expiration_date') is-invalid @enderror"
                                                   wire:model="expiration_date">
                                            @error('expiration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fabricante</label>
                                            <input type="text" class="form-control" wire:model="manufacturer" placeholder="ej: Pfizer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Administration Details --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-notes-medical me-2"></i>Detalles de Administración</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Vía <span class="text-danger">*</span></label>
                                            <select class="form-select @error('route_code') is-invalid @enderror" wire:model="route_code">
                                                <option value="IM">Intramuscular (IM)</option>
                                                <option value="PO">Oral (PO)</option>
                                                <option value="NASAL">Intranasal (NASAL)</option>
                                                <option value="SC">Subcutánea (SC)</option>
                                                <option value="ID">Intradérmica (ID)</option>
                                            </select>
                                            @error('route_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sitio de Aplicación</label>
                                            <input type="text" class="form-control" wire:model="site_code"
                                                   placeholder="ej: Deltoides izquierdo">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" wire:model="status">
                                                <option value="completed">Completada</option>
                                                <option value="not-done">No Realizada</option>
                                                <option value="entered-in-error">Error de Registro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Cantidad de Dosis</label>
                                            <input type="number" step="0.01" class="form-control"
                                                   wire:model="dose_quantity" placeholder="0.5">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Unidad</label>
                                            <input type="text" class="form-control" wire:model="dose_unit" value="mL">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Número de Dosis <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   class="form-control @error('dose_number') is-invalid @enderror"
                                                   wire:model="dose_number" min="1">
                                            @error('dose_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Dosis en Serie <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   class="form-control @error('series_doses') is-invalid @enderror"
                                                   wire:model="series_doses" min="1">
                                            @error('series_doses') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Adverse Reactions & Notes --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Reacciones y Notas</strong>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Reacción Adversa</label>
                                        <input type="text" class="form-control" wire:model="reaction"
                                               placeholder="Describir cualquier reacción adversa (si aplica)">
                                        <small class="text-muted">Dejar en blanco si no hubo reacciones</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notas Adicionales</label>
                                        <textarea class="form-control" wire:model="note" rows="2"
                                                  placeholder="Cualquier observación adicional..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Inmunización
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    @endif

    {{-- Toastr notifications --}}
    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrVaccination', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: event.type === 'error' ? 5000 : 3000,
                });
            });
        });
    </script>
    @endscript
</div>
