<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Tesorería</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Bancos</h6>
                    <h4 class="mb-0">${{ number_format($totalBankBalance, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Cajas</h6>
                    <h4 class="mb-0">${{ number_format($totalCashBalance, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 bg-success-light rounded">
                    <h6 class="text-muted mb-2">Total</h6>
                    <h4 class="mb-0">${{ number_format($totalTreasury, 2) }}</h4>
                </div>
            </div>
        </div>

        @if(count($banks) > 0)
            <h6 class="mb-2">Bancos</h6>
            @foreach($banks as $bank)
                <div class="d-flex justify-content-between mb-1">
                    <small>{{ $bank['name'] }} ({{ $bank['account'] }})</small>
                    <small class="fw-bold">${{ number_format($bank['balance'], 2) }}</small>
                </div>
            @endforeach
        @endif

        @if(count($cashRegisters) > 0)
            <hr>
            <h6 class="mb-2">Cajas</h6>
            @foreach($cashRegisters as $cash)
                <div class="d-flex justify-content-between mb-1">
                    <small>{{ $cash['name'] }}</small>
                    <small class="fw-bold">${{ number_format($cash['balance'], 2) }}</small>
                </div>
            @endforeach
        @endif
    </div>
</div>