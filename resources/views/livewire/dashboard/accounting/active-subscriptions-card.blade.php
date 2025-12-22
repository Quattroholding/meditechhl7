<div class="card flex-fill dashboard-kpi-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="text-muted mb-1">Suscripciones Activas</h6>
                <h2 class="mb-0">{{ $activeCount }}</h2>
            </div>
            <div class="icon-circle bg-primary-light">
                <i class="fas fa-users text-primary"></i>
            </div>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Total suscripciones:</span>
                <span class="fw-bold">{{ $totalCount }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">MRR (Ingresos Recurrentes):</span>
                <span class="fw-bold text-primary">${{ number_format($monthlyRecurringRevenue, 2) }}</span>
            </div>
        </div>

        @can('suscriptions.index')
            <div class="mt-3">
                <a href="{{ route('suscriptions.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fas fa-list me-1"></i> Ver Suscripciones
                </a>
            </div>
        @endcan
    </div>
    <style>
        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }
    </style>
</div>

