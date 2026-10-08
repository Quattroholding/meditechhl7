<div class="card flex-fill dashboard-kpi-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="text-muted mb-1">Pasivos Totales</h6>
                <h2 class="mb-0">${{ number_format($totalLiabilities, 2) }}</h2>
            </div>
            <div class="icon-circle bg-danger-light">
                <i class="fas fa-credit-card text-danger"></i>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                @if($percentageChange > 0)
                    <i class="fas fa-arrow-up text-danger me-1"></i>
                    <span class="text-danger fw-bold">{{ number_format($percentageChange, 1) }}%</span>
                @elseif($percentageChange < 0)
                    <i class="fas fa-arrow-down text-success me-1"></i>
                    <span class="text-success fw-bold">{{ number_format(abs($percentageChange), 1) }}%</span>
                @else
                    <i class="fas fa-minus text-muted me-1"></i>
                    <span class="text-muted fw-bold">0%</span>
                @endif
                <span class="text-muted ms-2 small">vs mes anterior</span>
            </div>
        </div>

        <div class="mt-3">
            <small class="text-muted">Mes anterior: ${{ number_format($previousMonthLiabilities, 2) }}</small>
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

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
</div>