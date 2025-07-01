<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="color: #fff;">
            <i class="fas fa-file-medical me-2"></i>Resumen Médico
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Active Medical Conditions -->
            <div class="col-md-6 mb-4">
                <div class="h6 mb-3">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Condiciones Médicas Activas
                </div>

                @if($this->activeMedicalConditions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($this->activeMedicalConditions as $condition)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $condition->icd10Code->code }}</h6>
                                        @if($condition->icd10Code)
                                            <p class="mb-1 text-muted small">{{ Str::limit($condition->icd10Code->description_es, 60) }}</p>
                                        @endif
                                        <small class="text-muted">
                                            Desde: {{ $condition->onset_date ? $condition->onset_date->format('m/Y') : 'No especificado' }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge
                                            @switch($condition->severity)
                                                @case('high')
                                                    bg-danger
                                                    @break
                                                @case('moderate')
                                                    bg-warning text-dark
                                                    @break
                                                @case('mild')
                                                    bg-success
                                                    @break
                                                @default
                                                    bg-secondary
                                            @endswitch
                                        ">
                                            {{ ucfirst($condition->severity ?? 'No especificado') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">No hay condiciones médicas activas registradas</p>
                    </div>
                @endif
            </div>

            <!-- Allergies -->
            <div class="col-md-6 mb-4">
                <div class="h6 mb-3">
                    <i class="fas fa-allergies text-danger me-2"></i>
                    Alergias
                </div>

                @if($this->allergies->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($this->allergies as $allergy)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $allergy->condition_name }}</h6>
                                        @if($allergy->notes)
                                            <small class="text-muted">{{ Str::limit($allergy->notes, 50) }}</small>
                                        @endif
                                    </div>
                                    <span class="badge bg-danger">
                                        {{ ucfirst($allergy->severity ?? 'Moderada') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">No hay alergias registradas</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Current Medications -->
            <div class="col-md-6 mb-4">
                <div class="h6 mb-3">
                    <i class="fas fa-pills text-primary me-2"></i>
                    Medicamentos Recientes
                </div>

                @if($this->currentMedications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($this->currentMedications as $encounter)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">{{ $encounter->encounter_date->format('d/m/Y') }}</small>
                                        <div class="mt-1">
                                            {{ Str::limit($encounter->prescription_notes, 80) }}
                                        </div>
                                    </div>
                                    <a href="{{ route('encounter.show', $encounter->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-prescription fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay medicamentos recientes</p>
                    </div>
                @endif
            </div>

            <!-- Last Vital Signs -->
            <div class="col-md-6 mb-4">
                <div class="h6 mb-3">
                    <i class="fas fa-heartbeat text-danger me-2"></i>
                    Últimos Signos Vitales
                </div>

                @if($this->lastVitalSigns)
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block mb-2">
                            {{ $this->lastVitalSigns['date']->format('d/m/Y') }}
                        </small>

                        <div class="row g-2">
                            @foreach($this->lastVitalSigns['vital_signs'] as $vitalSign)
                                <div class="col-6">
                                    <div class="text-center p-2 bg-white rounded">
                                        <small class="text-muted d-block">{{ $vitalSign->observationType?->name ?? 'Signo Vital' }}</small>
                                        <strong class="
                                            @switch($vitalSign->observationType?->code ?? '')
                                                @case('8480-6')
                                                @case('8462-4')
                                                    text-danger
                                                    @break
                                                @case('8867-4')
                                                    text-success
                                                    @break
                                                @case('8310-5')
                                                    text-warning
                                                    @break
                                                @case('29463-7')
                                                    text-info
                                                    @break
                                                @default
                                                    text-primary
                                            @endswitch
                                        ">{{ $vitalSign->value }} {{ $vitalSign->unit ?? '' }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay signos vitales registrados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('patient.medical_history',auth()->user()->patient->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-history me-1"></i>Historial Médico Completo
                    </a>
                    {{--}}
                    <a href="{{ route('patient.allergies') }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-allergies me-1"></i>Gestionar Alergias
                    </a>
                    <a href="{{ route('patient.medications') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-pills me-1"></i>Mis Medicamentos
                    </a>
                    <a href="{{ route('patient.vital-signs') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-heartbeat me-1"></i>Signos Vitales
                    </a>
                    {{--}}
                </div>
            </div>
        </div>
    </div>
</div>
