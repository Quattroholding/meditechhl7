<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="color: #fff;">
            <i class="fas fa-calendar-alt me-2"></i>Próximas Citas
        </h5>

    </div>
    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                @for($i = 0; $i < 3; $i++)
                    <div class="skeleton-appointment-item mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="skeleton-calendar-badge"></div>
                                    <div class="skeleton-date mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="skeleton-doctor-name mb-2"></div>
                                <div class="skeleton-specialty mb-1"></div>
                                <div class="skeleton-time"></div>
                                <div class="skeleton-location mt-1"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="skeleton-status-badge"></div>
                                <div class="skeleton-reason mt-2"></div>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="skeleton-actions"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @elseif($upcomingAppointments->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($upcomingAppointments as $appointment)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="badge bg-primary p-3 rounded-circle">
                                        <i class="fas fa-calendar-day fa-lg"></i>
                                    </div>
                                    <div class="mt-2">
                                        <strong>{{ $appointment->start->format('M') }}</strong>
                                        <div class="h4 mb-0">{{ $appointment->start->format('d') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-1">
                                    {{ $appointment->practitioner->name ?? 'Doctor asignado' }}
                                </h6>
                                <p class="mb-1 text-muted">
                                    {{ $appointment->medicalSpeciality->name ?? 'Especialidad' }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $appointment->start->format('h:i') ?? 'Hora por confirmar' }}
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
                                        {{--}}
                                        <li>
                                            <a class="dropdown-item" href="{{ route('appointment.show', $appointment->id) }}">
                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                            </a>
                                        </li>
                                        {{--}}
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

            @if($upcomingAppointments->count() >= $limit)
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
                <button  wire:click="openModal" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Programar Cita
                </button>
            </div>
        @endif
    </div>
    <livewire:appointment.modal-save wire:model="showModal" :title="$modalTitle"
                                     :appointment_date="$appointment_date" :appointment_time="$appointment_time" />

    <style>
        /* Loading skeleton styles for appointments */
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-appointment-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .skeleton-calendar-badge {
            width: 60px;
            height: 60px;
            background: #e9ecef;
            border-radius: 50%;
            margin: 0 auto;
        }

        .skeleton-date {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 40px;
            margin: 0 auto;
        }

        .skeleton-doctor-name {
            height: 18px;
            background: #e9ecef;
            border-radius: 4px;
            width: 160px;
        }

        .skeleton-specialty {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 120px;
        }

        .skeleton-time {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80px;
        }

        .skeleton-location {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 100px;
        }

        .skeleton-status-badge {
            height: 24px;
            background: #e9ecef;
            border-radius: 12px;
            width: 80px;
        }

        .skeleton-reason {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 90px;
        }

        .skeleton-actions {
            width: 30px;
            height: 30px;
            background: #e9ecef;
            border-radius: 4px;
            margin-left: auto;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</div>
