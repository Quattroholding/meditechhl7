<div class="card top-departments">
    <div class="card-header">
        <h4 class="card-title mb-0" style="color: #fff;">{{__('Citas por Especialidades')}}</h4>
    </div>
    <div class="card-body">
        @foreach($app_specialties as $speciality)
            <div class="activity-top">
                <div class="condition-item mb-3" style="width: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="condition-info">
                            <span class="condition-name">{{ ucfirst(strtolower($speciality->name)) }}</span>
                            <small class="text-muted">({{ $speciality->quantity }})</small>
                        </div>
                        <div class="condition-stats-text">
                            <span class="condition-count">{{(round($speciality->percentage,2))}}</span>
                            <small class="text-muted">%</small>
                        </div>
                    </div> 
                    <!-- Barra de progreso -->
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: {{ round($speciality->percentage,2) }}%; background-color: #3498db;"
                            aria-valuenow="{{ round($speciality->percentage,2) }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                   
                </div>
            </div>
        @endforeach
    </div>
</div>
