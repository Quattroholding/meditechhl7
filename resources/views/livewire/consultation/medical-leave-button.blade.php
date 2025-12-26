<div>
    <!-- Botón Flotante -->
    @if($hasDiagnoses)
    <button
        wire:click="openModal"
        type="button"
        class="btn btn-primary"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 1000; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 24px;"
        title="Crear Incapacidad Médica"
    >

        <i class="fa fa-file-medical"></i>
    </button>
    @endif

    <!-- Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #4CAF50; color: white;">
                    <h5 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-medical" viewBox="0 0 16 16" style="margin-right: 8px; display: inline;">
                            <path d="M7.5 5.5a.5.5 0 0 0-1 0v.634l-.549-.317a.5.5 0 1 0-.5.866L6 7l-.549.317a.5.5 0 1 0 .5.866l.549-.317V8.5a.5.5 0 1 0 1 0v-.634l.549.317a.5.5 0 1 0 .5-.866L8 7l.549-.317a.5.5 0 1 0-.5-.866l-.549.317V5.5zm-2 4.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                        </svg>
                        Nueva Incapacidad Médica
                    </h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true" style="color: white; font-size: 28px;">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Mensajes de Error/Éxito -->
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <!-- Información del Paciente y Encuentro -->
                        <div class="alert alert-info">
                            <strong>Paciente:</strong> {{ $encounter->patient->name }}<br>
                            <strong>Médico:</strong> {{ $encounter->practitioner->name }}<br>
                            <strong>Fecha de Consulta:</strong> {{ $encounter->start->format('d/m/Y H:i') }}
                        </div>

                        <!-- Selección de Diagnóstico -->
                        <div class="form-group">
                            <label for="selected_condition_id">
                                Diagnóstico <span class="text-danger">*</span>
                            </label>
                            <select
                                wire:model="selected_condition_id"
                                class="form-control @error('selected_condition_id') is-invalid @enderror"
                                id="selected_condition_id"
                            >
                                <option value="">Seleccione un diagnóstico</option>
                                @foreach($encounter->diagnoses as $diagnosis)
                                    <option value="{{ $diagnosis->condition_id }}">
                                        {{ $diagnosis->condition->code }} - {{ $diagnosis->condition->icd10Code?->description_es ?? $diagnosis->condition->onset_info }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selected_condition_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha y Hora de Inicio -->
                        <div class="form-group">
                            <label for="start_datetime">
                                Fecha y Hora de Inicio de Incapacidad <span class="text-danger">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model="start_datetime"
                                class="form-control @error('start_datetime') is-invalid @enderror"
                                id="start_datetime"
                            >
                            @error('start_datetime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formato: Día {{ \Carbon\Carbon::parse($start_datetime)->format('d') }},
                                Mes {{ \Carbon\Carbon::parse($start_datetime)->format('m') }},
                                Año {{ \Carbon\Carbon::parse($start_datetime)->format('Y') }},
                                Hora {{ \Carbon\Carbon::parse($start_datetime)->format('H:i') }}
                            </small>
                        </div>

                        <!-- Fecha y Hora de Fin -->
                        <div class="form-group">
                            <label for="end_datetime">
                                Fecha y Hora de Fin de Incapacidad <span class="text-danger">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model="end_datetime"
                                class="form-control @error('end_datetime') is-invalid @enderror"
                                id="end_datetime"
                            >
                            @error('end_datetime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formato: Día {{ \Carbon\Carbon::parse($end_datetime)->format('d') }},
                                Mes {{ \Carbon\Carbon::parse($end_datetime)->format('m') }},
                                Año {{ \Carbon\Carbon::parse($end_datetime)->format('Y') }},
                                Hora {{ \Carbon\Carbon::parse($end_datetime)->format('H:i') }}
                            </small>
                        </div>

                        <!-- Días Totales (Calculado) -->
                        @if($start_datetime && $end_datetime)
                        <div class="alert alert-success">
                            <strong>Días Totales de Incapacidad:</strong>
                            {{ \Carbon\Carbon::parse($start_datetime)->diffInDays(\Carbon\Carbon::parse($end_datetime)) + 1 }} días
                        </div>
                        @endif

                        <!-- Notas Adicionales -->
                        <div class="form-group">
                            <label for="notes">Notas Adicionales (Opcional)</label>
                            <textarea
                                wire:model="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                id="notes"
                                rows="3"
                                placeholder="Recomendaciones especiales, restricciones, etc."
                            ></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <small>
                                <strong>Nota:</strong> Esta licencia quedará registrada en el expediente del paciente
                                y estará disponible para impresión con todos los datos requeridos por las leyes de Panamá.
                            </small>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary submit-form me-2" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">

                            Crear Incapacidad Médica
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Guardando...
                        </span>
                    </button>
                    <button type="button" class="btn btn-secondary cancel-form" wire:click="closeModal">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
