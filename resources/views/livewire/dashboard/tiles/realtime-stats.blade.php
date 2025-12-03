<div class="card">
    <div class="card-body">
        <div class="dash-widget-header">
            <span class="dash-widget-icon text-primary border-primary">
                <i class="fe fe-activity"></i>
            </span>
            <div class="dash-count">
                <h3>Estadísticas en Tiempo Real</h3>
                <p class="text-muted">Actualizado automáticamente cada 60 segundos</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-3 col-sm-6">
                <div class="dash-widget dash-widget-bg-1">
                    <div class="dash-widget-info text-left d-inline-block">
                        <span>Citas Hoy</span>
                        <h3>{{ $appointments_today }}</h3>
                    </div>
                    <span class="dash-widget-icon bg-1 float-right">
                        <i class="fe fe-calendar"></i>
                    </span>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="dash-widget dash-widget-bg-2">
                    <div class="dash-widget-info text-left d-inline-block">
                        <span>Pacientes Nuevos</span>
                        <h3>{{ $new_patients_today }}</h3>
                    </div>
                    <span class="dash-widget-icon bg-2 float-right">
                        <i class="fe fe-user-plus"></i>
                    </span>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="dash-widget dash-widget-bg-3">
                    <div class="dash-widget-info text-left d-inline-block">
                        <span>Encuentros Activos</span>
                        <h3>{{ $active_encounters }}</h3>
                    </div>
                    <span class="dash-widget-icon bg-3 float-right">
                        <i class="fe fe-clipboard"></i>
                    </span>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="dash-widget dash-widget-bg-4">
                    <div class="dash-widget-info text-left d-inline-block">
                        <span>Citas Pendientes</span>
                        <h3>{{ $pending_appointments }}</h3>
                    </div>
                    <span class="dash-widget-icon bg-4 float-right">
                        <i class="fe fe-clock"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
