<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-hourglass-half text-warning me-2"></i>
                    Lista de Espera
                </h2>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total de Entradas</p>
                            <h3 class="text-primary mb-0">{{ $statistics['total'] }}</h3>
                        </div>
                        <i class="fas fa-list text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Urgentes</p>
                            <h3 class="text-danger mb-0">{{ $statistics['urgent'] }}</h3>
                        </div>
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 2rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Expirando en 3 días</p>
                            <h3 class="text-warning mb-0">{{ $statistics['expiring_soon'] }}</h3>
                        </div>
                        <i class="fas fa-clock text-warning" style="font-size: 2rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Doctor</label>
                    <select wire:model.live="filterPractitionerId" class="form-select">
                        <option value="">Todos</option>
                        @foreach($practitioners as $practitioner)
                            <option value="{{ $practitioner->id }}">{{ $practitioner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Especialidad</label>
                    <select wire:model.live="filterSpecialityId" class="form-select">
                        <option value="">Todas</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Urgencia</label>
                    <select wire:model.live="filterUrgency" class="form-select">
                        <option value="">Todas</option>
                        @foreach($urgencyLevels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Búsqueda</label>
                    <input type="text" wire:model.live="searchQuery" class="form-control"
                        placeholder="Paciente o ID..." />
                </div>

                <div class="col-md-12">
                    <button wire:click="clearFilters" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabla de Waitlist -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="8%" class="text-center">
                            <i class="fas fa-star text-warning"></i> Score
                        </th>
                        <th width="20%">Paciente</th>
                        <th width="15%">Especialidad</th>
                        <th width="15%">Urgencia</th>
                        <th width="12%">Fecha Preferida</th>
                        <th width="10%">Días</th>
                        <th width="12%">Expira</th>
                        <th width="18%" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitlistEntries as $entry)
                        <tr>
                            <td class="text-center">
                                <span class="badge" style="background-color: #ffa10b; color: white;">
                                    {{ number_format($entry->priority_score, 1) }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-500">{{ $entry->patient->name }}</div>
                                <small class="text-muted">{{ $entry->patient->identifier }}</small>
                            </td>

                            <td>
                                {{ $entry->medicalSpeciality?->name ?? 'N/A' }}
                            </td>

                            <td>
                                <span class="{{ $entry->urgency_level->badgeClass() }}">
                                    {{ $entry->urgency_level->label() }}
                                </span>
                            </td>

                            <td>
                                @if($entry->preferred_date)
                                    {{ $entry->preferred_date->format('d/m/Y') }}
                                    @if($entry->preferred_time)
                                        <br/>
                                        <small class="text-muted">{{ $entry->preferred_time->format('H:i') }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Flexible</span>
                                @endif
                            </td>

                            <td>
                                {{ max(0, (int) ($entry->created_at ?? now())->diffInDays(now())) }}
                            </td>

                            <td>
                                <small class="text-muted">
                                    {{ $entry->expires_at->diffForHumans() }}
                                </small>
                            </td>

                            <td class="text-end">
                                <button wire:click="openAssignModal({{ $entry->id }})"
                                    class="btn btn-sm btn-success me-2"
                                    title="Asignar cita">
                                    <i class="fas fa-check"></i> Asignar
                                </button>

                                <button wire:click="cancelEntry({{ $entry->id }})"
                                    wire:confirm="¿Estás seguro de que deseas cancelar esta entrada?"
                                    class="btn btn-sm btn-danger"
                                    title="Cancelar entrada">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted">No hay entradas en la lista de espera</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($waitlistEntries->hasPages())
            <div class="card-footer border-top">
                {{ $waitlistEntries->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de Asignación -->
    @if($showAssignModal && $selectedEntry)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            Asignar Cita
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeAssignModal()"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-500">Paciente</label>
                            <div class="form-control-plaintext">
                                {{ $selectedEntry->patient->name }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assignDate" class="form-label">Fecha</label>
                                <input type="date" id="assignDate" wire:model="assignDate"
                                    class="form-control @error('assignDate') is-invalid @enderror" />
                                @error('assignDate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assignTime" class="form-label">Hora</label>
                                <input type="time" id="assignTime" wire:model="assignTime"
                                    class="form-control @error('assignTime') is-invalid @enderror" />
                                @error('assignTime')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assignDuration" class="form-label">Duración (minutos)</label>
                                <input type="number" id="assignDuration" wire:model="assignDuration"
                                    class="form-control @error('assignDuration') is-invalid @enderror"
                                    min="15" max="480" />
                                @error('assignDuration')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assignRoomId" class="form-label">Consultorio</label>
                                <input type="text" id="assignRoomId" wire:model="assignRoomId"
                                    class="form-control" disabled />
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Los datos están pre-rellenados con las preferencias del paciente</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeAssignModal()">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-success" wire:click="assignAppointment()">
                            <i class="fas fa-check me-2"></i> Asignar Cita
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
