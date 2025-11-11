<div class="card top-departments">
    <div class="card-header">
        <h4 class="card-title mb-0" style="color: #fff;">{{__('Ingresos por Sede')}}</h4>
    </div>
    <div class="card-body">
    @if($invoices->isEmpty())
        <p class="px-4 pt-3">No se encontraron registros de ingresos</p>
    @else
        @foreach($invoices as $invoice)
            <div class="activity-top">
                <div class="condition-item mb-3" style="width: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="condition-info">
                            <span class="condition-name">{{ ucfirst(strtolower($invoice->name)) }}</span>
                            <small class="text-muted">({{ $invoice->address }})</small>
                        </div>
                        <div class="condition-stats-text">
                            <span class="condition-count">${{$invoice->total_invoices }}</span>
                        </div>
                    </div> 
                   
                </div>
            </div>
        @endforeach
    @endif
    </div>
</div>


