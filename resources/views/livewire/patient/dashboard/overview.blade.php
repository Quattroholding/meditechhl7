<div>
    <div class="row">
        <!-- Health Stats Cards -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $this->stats['appointments']['upcoming'] }}</h4>
                            <small>Próximas Citas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-md fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $this->stats['consultations']['total'] }}</h4>
                            <small>Total Consultas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $this->stats['invoices']['outstanding'] }}</h4>
                            <small>Facturas Pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-heartbeat fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">{{ $this->stats['medical_conditions']['active'] }}</h4>
                            <small>Condiciones Activas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Next Appointment -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #fff;">
                        <i class="fas fa-calendar-alt me-2"></i>Próxima Cita
                    </h5>
                </div>
                <div class="card-body">
                    @if($this->nextAppointment)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $this->nextAppointment->practitioner->full_name ?? 'Doctor asignado' }}</h6>
                                <p class="mb-0 text-muted">{{ $this->nextAppointment->practitioner->specialty ?? 'Especialidad' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <strong>Fecha:</strong>
                                <p class="mb-0">{{ $this->nextAppointment->appointment_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Hora:</strong>
                                <p class="mb-0">{{ $this->nextAppointment->appointment_time ?? 'Por confirmar' }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <strong>Motivo:</strong>
                            <p class="mb-0">{{ $this->nextAppointment->reason_for_visit ?? 'Consulta general' }}</p>
                        </div>
                        <div class="mt-3">
                            <span class="badge
                                @switch($this->nextAppointment->status)
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
                                {{ ucfirst(__('appointment.status.' . $this->nextAppointment->status)) }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes citas programadas próximamente.</p>
                            <a href="{{ route('appointment.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Programar Cita
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vital Signs -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #fff;">
                        <i class="fas fa-heartbeat me-2"></i>Últimos Signos Vitales
                    </h5>
                </div>
                <div class="card-body">
                    @if($this->vitalSigns)
                        <div class="row">
                            @foreach($this->vitalSigns as $vitalSign)
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="
                                                @switch($vitalSign->observationType?->code ?? '')
                                                    @case('8480-6')
                                                    @case('8462-4')
                                                        bg-danger
                                                        @break
                                                    @case('8867-4')
                                                        bg-success
                                                        @break
                                                    @case('8310-5')
                                                        bg-warning
                                                        @break
                                                    @case('29463-7')
                                                        bg-info
                                                        @break
                                                    @default
                                                        bg-secondary
                                                @endswitch
                                                text-white rounded-circle p-2">
                                                <i class="fas
                                                    @switch($vitalSign->observationType?->code ?? '')
                                                        @case('8480-6')
                                                        @case('8462-4')
                                                            fa-tint
                                                            @break
                                                        @case('8867-4')
                                                            fa-heartbeat
                                                            @break
                                                        @case('8310-5')
                                                            fa-thermometer-half
                                                            @break
                                                        @case('29463-7')
                                                            fa-weight
                                                            @break
                                                        @default
                                                            fa-chart-line
                                                    @endswitch
                                                "></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <small class="text-muted">{{ $vitalSign->observationType?->name ?? 'Signo Vital' }}</small>
                                            <h6 class="mb-0">{{ $vitalSign->value }} {{ $vitalSign->unit ?? '' }}</h6>
                                            <small class="text-muted">{{ $vitalSign->effective_date->format('d/m') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay signos vitales registrados recientemente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Outstanding Invoices Alert -->

    </div>

    @if($this->stats['invoices']['outstanding'] > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Facturas Pendientes de Pago</h6>
                            <p class="mb-0">
                                Tienes {{ $this->stats['invoices']['outstanding'] }} facturas pendientes
                                por un total de ${{ number_format($this->stats['invoices']['total_debt'], 2) }}.
                                <a href="{{ route('invoice.index') }}" class="alert-link">Ver facturas</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
