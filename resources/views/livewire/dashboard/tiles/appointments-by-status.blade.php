<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit mb-3">
            <h4>
                <i class="fe fe-pie-chart me-2"></i>
                Citas por Estado - Hoy
            </h4>
            <div>
                <span class="badge bg-secondary">Total: {{ $total }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-warning me-2"></i>
                        <span>Pendientes</span>
                    </div>
                    <span class="badge bg-warning">{{ $pending }}</span>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-success me-2"></i>
                        <span>Reservadas</span>
                    </div>
                    <span class="badge bg-success">{{ $booked }}</span>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-primary me-2"></i>
                        <span>Confirmadas</span>
                    </div>
                    <span class="badge bg-primary">{{ $confirmed }}</span>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-info me-2"></i>
                        <span>Llegaron</span>
                    </div>
                    <span class="badge bg-info">{{ $arrived }}</span>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-primary me-2"></i>
                        <span>Completadas</span>
                    </div>
                    <span class="badge bg-primary">{{ $fulfilled }}</span>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <i class="fa fa-circle text-danger me-2"></i>
                        <span>Canceladas</span>
                    </div>
                    <span class="badge bg-danger">{{ $cancelled }}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-2 text-muted text-end">
            <small>Actualizado automáticamente cada 2 minutos</small>
        </div>
    </div>
</div>
