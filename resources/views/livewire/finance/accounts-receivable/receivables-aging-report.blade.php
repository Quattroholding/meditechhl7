<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Reporte de Antigüedad - Cuentas por Cobrar
            @endslot
            @slot('li_1')
                Módulo Financiero
            @endslot
        @endcomponent

        <div class="col-sm-12">
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-block local-forms">
                                <label for="asOfDate">Fecha de Corte</label>
                                <input
                                    type="date"
                                    id="asOfDate"
                                    wire:model.live="asOfDate"
                                    class="form-control"
                                    value="{{ $asOfDate }}"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-block local-forms">
                                <label for="patientFilter">Filtro por Paciente</label>
                                <input
                                    type="text"
                                    id="patientFilter"
                                    wire:model.live="patientFilter"
                                    placeholder="Nombre del paciente..."
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-block local-forms">
                                <label for="limit">Registros por Página</label>
                                <select
                                    id="limit"
                                    wire:model.live="limit"
                                    class="form-control form-select"
                                >
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de Resumen -->
            <div class="row mb-4">
                <!-- 0-30 Días -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-sm mb-1">0-30 Días</p>
                                    <h4 class="text-success mb-2">
                                        {{ number_format($summary['0-30'], 2, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ number_format($this->calculatePercentage('0-30'), 1) }}% del total
                                    </small>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-check-circle text-success"></i>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div
                                    class="progress-bar bg-success"
                                    style="width: {{ $this->calculatePercentage('0-30') }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 31-60 Días -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-sm mb-1">31-60 Días</p>
                                    <h4 class="text-warning mb-2">
                                        {{ number_format($summary['31-60'], 2, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ number_format($this->calculatePercentage('31-60'), 1) }}% del total
                                    </small>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-alert-circle text-warning"></i>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div
                                    class="progress-bar bg-warning"
                                    style="width: {{ $this->calculatePercentage('31-60') }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 61-90 Días -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-sm mb-1">61-90 Días</p>
                                    <h4 class="text-danger mb-2">
                                        {{ number_format($summary['61-90'], 2, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ number_format($this->calculatePercentage('61-90'), 1) }}% del total
                                    </small>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-alert-triangle text-danger"></i>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div
                                    class="progress-bar bg-danger"
                                    style="width: {{ $this->calculatePercentage('61-90') }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Más de 90 Días -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-sm mb-1">Más de 90 Días</p>
                                    <h4 class="text-dark mb-2">
                                        {{ number_format($summary['90+'], 2, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ number_format($this->calculatePercentage('90+'), 1) }}% del total
                                    </small>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-x-circle text-dark"></i>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div
                                    class="progress-bar bg-dark"
                                    style="width: {{ $this->calculatePercentage('90+') }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Indicadores Críticos -->
            <div class="row mb-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card bg-danger bg-opacity-10 border-danger border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Vencido hace 30+ días</p>
                                    <h3 class="text-danger mb-0">
                                        {{ number_format($summary['overdue_30'], 2, ',', '.') }}
                                    </h3>
                                </div>
                                <div>
                                    <i class="feather-alert-circle text-danger" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card bg-dark bg-opacity-10 border-dark border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Vencido hace 60+ días</p>
                                    <h3 class="text-dark mb-0">
                                        {{ number_format($summary['overdue_60'], 2, ',', '.') }}
                                    </h3>
                                </div>
                                <div>
                                    <i class="feather-x-circle text-dark" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card bg-primary bg-opacity-10 border-primary border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total CxC</p>
                                    <h3 class="text-primary mb-0">
                                        {{ number_format($summary['total'], 2, ',', '.') }}
                                    </h3>
                                </div>
                                <div>
                                    <i class="feather-dollar-sign text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Detalle -->
            <div class="card card-table show-entire">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalle de Cuentas por Cobrar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Factura #</th>
                                    <th>Fecha Factura</th>
                                    <th>Vencimiento</th>
                                    <th class="text-right">Saldo</th>
                                    <th class="text-center">Días Vencido</th>
                                    <th class="text-center">Rango</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ageingData as $item)
                                    <tr class="table-row">
                                        <td>
                                            <strong>{{ $item['patient'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $item['invoice_number'] }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $item['invoice_date'] }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $item['due_date'] }}</small>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($item['balance'], 2, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="{{ $item['days_overdue'] > 90 ? 'fw-bold text-danger' : '' }}">
                                                {{ $item['days_overdue'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $this->getAgeingBadgeClass($item['range']) }}">
                                                {{ $item['range'] }} días
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge
                                                @switch($item['status'])
                                                    @case('overdue') bg-danger @break
                                                    @case('partial') bg-warning @break
                                                    @case('paid') bg-success @break
                                                    @default bg-primary
                                                @endswitch
                                            ">
                                                {{ match($item['status']) {
                                                    'pending' => 'Pendiente',
                                                    'partial' => 'Parcial',
                                                    'paid' => 'Pagada',
                                                    'overdue' => 'Vencida',
                                                    'cancelled' => 'Cancelada',
                                                    default => ucfirst($item['status'])
                                                } }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="feather-inbox mb-3" style="font-size: 2rem;"></i>
                                            <p>No hay cuentas por cobrar para este período</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.3;
    }

    .badge-light-primary {
        background-color: #e7f1ff;
        color: #0052cc;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-light {
        background-color: #f5f7fb;
        color: #3a4e5c;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
