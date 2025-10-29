<div class="card top-departments">
    <div class="card-header">
        <h4 class="card-title mb-0" style="color: #fff;">{{__('Diagnósticos por Grupo Etario')}}</h4>
    </div>
    <div class="card-body">
        @foreach($results as $speciality)
            <div class="activity-top">
                <div class="condition-item mb-3" style="width: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="condition-info">
                            <span class="condition-name">
                                {{ ucfirst(strtolower($speciality->onset_info)) }}
                            </span>
                            {{--}}<small class="text-muted">
                                ({{ $speciality->specialty }})
                            </small>{{--}}
                        </div>
                        <div class="condition-stats-text">
                            <span class="condition-count">
                                {{ round($speciality->percentage, 2) }}
                            </span>
                            <small class="text-muted">%</small>
                        </div>
                    </div>
                    <!-- Barra de progreso -->
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: {{ round($speciality->percentage, 2) }}%; background-color: #3498db;"
                            aria-valuenow="{{ round($speciality->percentage, 2) }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                      <!-- Detalles adicionales -->
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">{{__('Especialidad')}}</small>
                            <span class="fw-bold">{{ $speciality->specialty }}</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">{{__('Total')}}</small>
                            <span class="fw-bold">{{ $speciality->total }}</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">{{__('Grupo Etario')}}</small>
                            <span class="fw-bold">{{ $speciality->age_group }}</span>
                        </div>
                        {{--}}<div class="col-4">
                            <small class="text-muted d-block">{{__('Porcentaje')}}</small>
                            <span class="fw-bold">{{ round($speciality->percentage, 2) }}%</span>
                        </div>{{--}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
