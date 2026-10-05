<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Reporte de Flujo de Caja
            @endslot
            @slot('li_1')
                Módulo de Tesorería
            @endslot
        @endcomponent

        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="input-block local-forms">
                                <label for="startDate">Fecha Inicial</label>
                                <input
                                    type="date"
                                    id="startDate"
                                    wire:model.live="startDate"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-block local-forms">
                                <label for="endDate">Fecha Final</label>
                                <input
                                    type="date"
                                    id="endDate"
                                    wire:model.live="endDate"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-block local-forms">
                                <label for="bankId">Banco (Opcional)</label>
                                <select
                                    id="bankId"
                                    wire:model.live="bankId"
                                    class="form-control form-select"
                                >
                                    <option value="">Todos los Bancos</option>
                                    @forelse($banks as $bank)
                                        <option value="{{ $bank['id'] }}">{{ $bank['bank_name'] }}</option>
                                    @empty
                                        <option disabled>No hay bancos disponibles</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button wire:click="generateReport" class="btn btn-primary w-100">
                                <i class="feather-refresh-cw"></i> Generar Reporte
                            </button>
                        </div>
                    </div>

                    <!-- Mostrar errores de validación -->
                    @if ($errors->has('dates'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> {{ $errors->first('dates') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumen de Flujo de Caja -->
            <div class="row">
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-content d-flex align-items-center justify-content-between">
                                <div class="stat-text">
                                    <p class="mb-1">Saldo Inicial</p>
                                    <h4 class="text-primary">
                                        {{ number_format($reportData['initial_balance'], 2, ',', '.') }}
                                    </h4>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-arrow-down-circle text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-content d-flex align-items-center justify-content-between">
                                <div class="stat-text">
                                    <p class="mb-1">Ingresos</p>
                                    <h4 class="text-success">
                                        {{ number_format($reportData['income'], 2, ',', '.') }}
                                    </h4>
                                    @if (isset($summary['income_percentage']))
                                        <small class="text-muted">{{ number_format($summary['income_percentage'], 1) }}%</small>
                                    @endif
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-arrow-up-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-content d-flex align-items-center justify-content-between">
                                <div class="stat-text">
                                    <p class="mb-1">Egresos</p>
                                    <h4 class="text-danger">
                                        {{ number_format($reportData['expense'], 2, ',', '.') }}
                                    </h4>
                                    @if (isset($summary['expense_percentage']))
                                        <small class="text-muted">{{ number_format($summary['expense_percentage'], 1) }}%</small>
                                    @endif
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-arrow-down-circle text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-content d-flex align-items-center justify-content-between">
                                <div class="stat-text">
                                    <p class="mb-1">Flujo Neto</p>
                                    <h4 class="{{ $reportData['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($reportData['net_flow'], 2, ',', '.') }}
                                    </h4>
                                </div>
                                <div class="stat-icon">
                                    <i class="feather-trending-{{ $reportData['net_flow'] >= 0 ? 'up' : 'down' }} {{ $reportData['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saldo Final -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Saldo Final</h5>
                                <h3 class="{{ $reportData['final_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($reportData['final_balance'], 2, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Ingresos -->
            @if (!empty($reportData['income_summary']))
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Ingresos por Tipo</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reportData['income_summary'] as $type => $amount)
                                                <tr>
                                                    <td>{{ $type }}</td>
                                                    <td class="text-end text-success">{{ number_format($amount, 2, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">Sin ingresos</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Egresos -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Egresos por Tipo</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reportData['expense_summary'] as $type => $amount)
                                                <tr>
                                                    <td>{{ $type }}</td>
                                                    <td class="text-end text-danger">{{ number_format($amount, 2, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">Sin egresos</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabla de Transacciones Detalladas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table show-entire">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Transacciones Detalladas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table border-0 custom-table comman-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Referencia</th>
                                            <th class="text-right">Ingreso</th>
                                            <th class="text-right">Egreso</th>
                                            <th class="text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['transactions'] as $transaction)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-light">{{ $transaction['date'] }}</span>
                                                </td>
                                                <td>
                                                    {{ $transaction['description'] }}
                                                </td>
                                                <td>
                                                    @if ($transaction['reference'])
                                                        <small class="text-muted">{{ $transaction['reference'] }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($transaction['income'] > 0)
                                                        <span class="text-success">{{ number_format($transaction['income'], 2, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($transaction['expense'] > 0)
                                                        <span class="text-danger">{{ number_format($transaction['expense'], 2, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <strong class="{{ $transaction['running_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($transaction['running_balance'], 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    No hay transacciones para el período seleccionado
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
        font-size: 2.5rem;
        opacity: 0.2;
    }

    .stat-text h4 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0.5rem 0 0;
    }

    .badge-light {
        background-color: #f5f7fb;
        color: #3a4e5c;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }
</style>
