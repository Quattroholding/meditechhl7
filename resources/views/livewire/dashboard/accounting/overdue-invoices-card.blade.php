<div class="card flex-fill dashboard-kpi-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="text-muted mb-1">Facturas Vencidas</h6>
                <h2 class="mb-0">{{ $count }}</h2>
            </div>
            <div class="icon-circle bg-danger-light">
                <i class="fas fa-exclamation-triangle text-danger"></i>
            </div>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Monto total vencido:</span>
                <span class="fw-bold text-danger">${{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>

        @can('suscriptions.invoices.index')
            <div class="mt-3">
                <a href="{{ route('suscriptions.invoices.index', ['statusFilter' => 'overdue']) }}" class="btn btn-sm btn-outline-danger w-100">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Ver Facturas
                </a>
            </div>
        @endcan
    </div>
    <style>
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
</div>


