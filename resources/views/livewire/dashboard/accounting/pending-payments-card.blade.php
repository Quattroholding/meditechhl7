<div class="card flex-fill dashboard-kpi-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="text-muted mb-1">Pagos Pendientes</h6>
                <h2 class="mb-0">{{ $count }}</h2>
            </div>
            <div class="icon-circle bg-warning-light">
                <i class="fas fa-clock text-warning"></i>
            </div>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Monto total:</span>
                <span class="fw-bold text-warning">${{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>

        @can('suscriptions.payments.verify')
            <div class="mt-3">
                <a href="{{ route('suscriptions.payments.index', ['statusFilter' => 'pending']) }}" class="btn btn-sm btn-outline-warning w-100">
                    <i class="fas fa-check-circle me-1"></i> Verificar Pagos
                </a>
            </div>
        @endcan
    </div>
    <style>
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>
</div>


