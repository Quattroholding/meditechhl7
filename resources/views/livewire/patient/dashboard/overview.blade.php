<div>
    @if($isLoading)
        <div class="row">
            <!-- Loading Skeleton for Health Stats Cards -->
            @for($i = 0; $i < 4; $i++)
                <div class="col-md-3">
                    <div class="card skeleton-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="skeleton-icon"></div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="skeleton-number"></div>
                                    <div class="skeleton-text"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="row mt-4">
            <!-- Loading Skeleton for Next Appointment and Vital Signs -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="skeleton-title"></div>
                    </div>
                    <div class="card-body">
                        <div class="skeleton-content-large"></div>
                        <div class="skeleton-content-medium mt-2"></div>
                        <div class="skeleton-content-small mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="skeleton-title"></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-vital-icon"></div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="skeleton-vital-label"></div>
                                            <div class="skeleton-vital-value"></div>
                                            <div class="skeleton-vital-date"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
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
                                <h4 class="mb-0">{{ $stats['appointments']['upcoming'] ?? 0 }}</h4>
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
                                <h4 class="mb-0">{{ $stats['consultations']['total'] ?? 0 }}</h4>
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
                                <h4 class="mb-0">{{ $stats['invoices']['outstanding'] ?? 0 }}</h4>
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
                                <h4 class="mb-0">{{ $stats['medical_conditions']['active'] ?? 0 }}</h4>
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
                    @if($nextAppointment)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $nextAppointment->practitioner->name ?? 'Doctor asignado' }}</h6>
                                <p class="mb-0 text-muted">{{ $nextAppointment->medicalSpeciality->name ?? 'Especialidad' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <strong>Fecha:</strong>
                                <p class="mb-0">{{ $nextAppointment->start->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Hora:</strong>
                                <p class="mb-0">{{ $nextAppointment->start->format('h:i') ?? 'Por confirmar' }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <strong>Tipo Servicio:</strong>
                            <p class="mb-0">{{ $nextAppointment->service_type ?? 'Consulta general' }}</p>
                        </div>
                        <div class="mt-3">
                            <span class="badge
                                @switch($nextAppointment->status)
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
                                {{ ucfirst(__('appointment.status.' . $nextAppointment->status)) }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes citas programadas próximamente.</p>

                            <button  wire:click="openModal" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Programar Cita
                            </button>
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
                    @if($vitalSigns)
                        <div class="row">
                            @foreach($vitalSigns as $vitalSign)
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

        @if(($stats['invoices']['outstanding'] ?? 0) > 0)
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
                                Tienes {{ $stats['invoices']['outstanding'] ?? 0 }} facturas pendientes
                                por un total de ${{ number_format($stats['invoices']['total_debt'] ?? 0, 2) }}.
                                <a href="{{ route('invoice.index') }}" class="alert-link">Ver facturas</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    <style>
        /* Loading skeleton styles */
        .skeleton-card {
            background: #f8f9fa;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-icon {
            width: 48px;
            height: 48px;
            background: #e9ecef;
            border-radius: 4px;
        }

        .skeleton-number {
            height: 32px;
            background: #e9ecef;
            border-radius: 4px;
            width: 60px;
            margin-bottom: 8px;
        }

        .skeleton-text {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 120px;
        }

        .skeleton-title {
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
            width: 150px;
        }

        .skeleton-content-large {
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
            width: 100%;
        }

        .skeleton-content-medium {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80%;
        }

        .skeleton-content-small {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 60%;
        }

        .skeleton-vital-icon {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .skeleton-vital-label {
            height: 12px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80px;
            margin-bottom: 4px;
        }

        .skeleton-vital-value {
            height: 16px;
            background: #e9ecef;
            border-radius: 4px;
            width: 60px;
            margin-bottom: 4px;
        }

        .skeleton-vital-date {
            height: 10px;
            background: #e9ecef;
            border-radius: 4px;
            width: 40px;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</div>
