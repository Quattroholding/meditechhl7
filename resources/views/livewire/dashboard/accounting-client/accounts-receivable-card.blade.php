<div class="card flex-fill dashboard-kpi-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="text-muted mb-1">Cuentas por Cobrar</h6>
                <h2 class="mb-0">${{ number_format($totalReceivable, 2) }}</h2>
            </div>
            <div class="icon-circle bg-warning-light">
                <i class="fas fa-handshake text-warning"></i>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="badge bg-danger me-2">{{ $overdueCount }} vencidas</span>
                <span class="text-muted small">${{ number_format($overdueAmount, 2) }}</span>
            </div>
        </div>

        <div class="mt-3">
            <small class="text-muted">Total pendiente de cobro</small>
        </div>
    </div>
    <style>
        .dashboard-kpi-card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .dashboard-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle i {
            font-size: 24px;
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>
</div>