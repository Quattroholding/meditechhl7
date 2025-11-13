<div class="card top-departments">
    <div class="card-header">
        <h4 class="card-title mb-0" style="color: #fff;">{{__('Citas por Sede')}}</h4>
    </div>
    <div class="card-body">
    @if($appointments->isEmpty())
        <p class="px-4 pt-3">No se encontraron registros de citas</p>
    @else
        @foreach($appointments as $appointment)
            <div class="activity-top">
                <div class="condition-item mb-3" style="width: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="condition-info">
                            <span class="condition-name">{{ ucfirst(strtolower($appointment->name)) }}</span>
                            <small class="text-muted">({{ $appointment->address }})</small>
                        </div>
                        <div class="condition-stats-text">
                            <span class="condition-count">{{ $appointment->total_appointments }}</span>
                        </div>
                    </div> 
                   
                </div>
            </div>
        @endforeach
    @endif
    </div>
</div>

