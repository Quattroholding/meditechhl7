<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Resumen de Balance</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span>Activos Totales</span>
                    <span class="fw-bold">${{ number_format($totalAssets, 2) }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span>Pasivos Totales</span>
                    <span class="fw-bold">${{ number_format($totalLiabilities, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span>Patrimonio</span>
                    <span class="fw-bold">${{ number_format($totalEquity, 2) }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span>Ecuación</span>
                    @if($isBalanced)
                        <span class="badge bg-success">Balanceado ✓</span>
                    @else
                        <span class="badge bg-danger">Desbalanceado ✗</span>
                    @endif
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-12">
                <small class="text-muted">Activos = Pasivos + Patrimonio</small><br>
                <small>${{ number_format($totalAssets, 2) }} = ${{ number_format($totalLiabilities + $totalEquity, 2) }}</small>
            </div>
        </div>
    </div>
</div>