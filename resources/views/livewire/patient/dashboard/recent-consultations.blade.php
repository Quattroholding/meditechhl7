<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="color: #fff;">
            <i class="fas fa-stethoscope me-2"></i>Consultas Recientes
        </h5>
        {{--}}
        <a href="{{ route('consultation.index') }}" class="btn btn-outline-primary btn-sm">
            Ver Todas
        </a>
        {{--}}
    </div>
    <div class="card-body">
        @if($this->recentConsultations->count() > 0)
            <div class="timeline">
                @foreach($this->recentConsultations as $consultation)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        {{ $consultation->practitioner->full_name ?? 'Doctor' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $consultation->practitioner->specialty ?? 'Medicina General' }}
                                    </small>
                                </div>
                                <small class="text-muted">
                                    {{ $consultation->start->format('d/m/Y') }}
                                </small>
                            </div>

                            @if($consultation->chief_complaint)
                                <div class="mb-2">
                                    <strong class="text-primary">Motivo de consulta:</strong>
                                    <p class="mb-1">{{ Str::limit($consultation->chief_complaint, 100) }}</p>
                                </div>
                            @endif

                            @if($consultation->diagnosis)
                                <div class="mb-2">
                                    <strong class="text-success">Diagnóstico:</strong>
                                    <p class="mb-1">{{ Str::limit($consultation->diagnosis, 100) }}</p>
                                </div>
                            @endif

                            @if($consultation->treatment_plan)
                                <div class="mb-2">
                                    <strong class="text-info">Plan de tratamiento:</strong>
                                    <p class="mb-1">{{ Str::limit($consultation->treatment_plan, 100) }}</p>
                                </div>
                            @endif

                            <!-- Vital Signs Summary -->
                            @if($consultation->vitalSigns && $consultation->vitalSigns->count() > 0)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <small class="text-muted"><strong>Signos vitales:</strong></small>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @foreach($consultation->vitalSigns->take(4) as $vitalSign)
                                                <span class="badge bg-light text-dark">
                                                    {{ $vitalSign->observationType?->name ?? 'Signo' }}: {{ $vitalSign->value }} {{ $vitalSign->unit ?? '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Status and Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                {!! $consultation->status !!}
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('consultation.view', $consultation->id) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Ver Detalles
                                    </a>

                                    @if($consultation->prescription_notes)
                                        <a href="{{ route('patient.prescription', $consultation->id) }}"
                                           class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-prescription me-1"></i>Receta
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($this->recentConsultations->count() >= $limit)
                <div class="text-center mt-3">
                    <a href="{{ route('consultation.index') }}" class="btn btn-outline-primary btn-sm">
                        Ver Historial Completo
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No hay consultas registradas</h6>
                <p class="text-muted mb-3">Tus consultas médicas aparecerán aquí</p>
                <a href="{{ route('appointment.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Programar Consulta
                </a>
            </div>
        @endif
    </div>
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
    </style>
</div>


