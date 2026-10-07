<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Flujo de Caja (Mes Actual)</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="d-flex justify-content-between p-2 bg-success-light rounded mb-2">
                    <span>Ingresos</span>
                    <span class="fw-bold text-success">${{ number_format($totalIncome, 2) }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between p-2 bg-danger-light rounded mb-2">
                    <span>Egresos</span>
                    <span class="fw-bold text-danger">${{ number_format($totalExpense, 2) }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between p-2 rounded mb-2" style="background-color: {{ $netCashFlow > 0 ? 'rgba(40, 167, 69, 0.1)' : 'rgba(220, 53, 69, 0.1)' }}">
                    <span>Flujo Neto</span>
                    <span class="fw-bold" style="color: {{ $netCashFlow > 0 ? '#28a745' : '#dc3545' }}">${{ number_format($netCashFlow, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="progress" style="height: 30px;">
            @php
                $total = $totalIncome + $totalExpense;
                $incomePct = $total > 0 ? ($totalIncome / $total) * 100 : 0;
                $expensePct = $total > 0 ? ($totalExpense / $total) * 100 : 0;
            @endphp
            <div class="progress-bar bg-success" style="width: {{ $incomePct }}%;" title="Ingresos">
                @if($incomePct > 10)
                    <small>Ingresos {{ number_format($incomePct, 0) }}%%</small>
                @endif
            </div>
            <div class="progress-bar bg-danger" style="width: {{ $expensePct }}%;" title="Egresos">
                @if($expensePct > 10)
                    <small>Egresos {{ number_format($expensePct, 0) }}%%</small>
                @endif
            </div>
        </div>
    </div>
</div>