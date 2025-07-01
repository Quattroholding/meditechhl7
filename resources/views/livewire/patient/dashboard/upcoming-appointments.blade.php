<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="color: #fff;">
            <i class="fas fa-calendar-alt me-2"></i>Próximas Citas
        </h5>
        <a href="{{ route('appointment.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nueva Cita
        </a>
    </div>
    <div class="card-body">
        @if($this->upcomingAppointments->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($this->upcomingAppointments as $appointment)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="badge bg-primary p-3 rounded-circle">
                                        <i class="fas fa-calendar-day fa-lg"></i>
                                    </div>
                                    <div class="mt-2">
                                        <strong>{{ $appointment->appointment_date->format('M') }}</strong>
                                        <div class="h4 mb-0">{{ $appointment->appointment_date->format('d') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-1">
                                    {{ $appointment->practitioner->full_name ?? 'Doctor asignado' }}
                                </h6>
                                <p class="mb-1 text-muted">
                                    {{ $appointment->practitioner->specialty ?? 'Especialidad' }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $appointment->appointment_time ?? 'Hora por confirmar' }}
                                </small>
                                @if($appointment->consultingRoom)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $appointment->consultingRoom->name }}
                                        @if($appointment->consultingRoom->branch)
                                            - {{ $appointment->consultingRoom->branch->name }}
                                        @endif
                                    </small>
                                @endif
                            </div>

                            <div class="col-md-2">
                                <span class="badge
                                    @switch($appointment->status)
                                        @case('scheduled')
                                            badge-success
                                            @break
                                        @case('confirmed')
                                            badge-primary
                                            @break
                                        @case('pending')
                                            badge-warning
                                            @break
                                        @default
                                            badge-secondary
                                    @endswitch
                                ">
                                    {{ ucfirst(__('appointment.status.' . $appointment->status)) }}
                                </span>

                                @if($appointment->reason_for_visit)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Motivo:</strong> {{ Str::limit($appointment->reason_for_visit, 50) }}
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-2 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('appointment.show', $appointment->id) }}">
                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                            </a>
                                        </li>
                                        @if($appointment->appointment_date > now()->addHours(24))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('appointment.edit', $appointment->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger"
                                                   href="javascript:;"
                                                   wire:click="cancelAppointment({{ $appointment->id }})"
                                                   onclick="return confirm('¿Está seguro de cancelar esta cita?')">
                                                    <i class="fas fa-times me-2"></i>Cancelar
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($this->upcomingAppointments->count() >= $limit)
                <div class="text-center mt-3">
                    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary btn-sm">
                        Ver Todas las Citas
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No tienes citas programadas</h6>
                <p class="text-muted mb-3">Programa tu próxima cita médica</p>
                <a href="{{ route('appointment.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Programar Cita
                </a>
            </div>
        @endif
    </div>
</div>
