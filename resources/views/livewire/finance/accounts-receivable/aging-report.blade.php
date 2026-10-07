<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Módulo Financiero
            @endslot
            @slot('li_1')
                Reporte de Antigüedad de CxC
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header with Filters -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-3 align-items-end">
                                    <div class="input-block local-forms mb-0">
                                        <label>Fecha de Corte</label>
                                        <input type="date" wire:model.live="asOfDate" class="form-control" value="{{ $asOfDate }}" />
                                    </div>
                                    <div class="input-block local-forms mb-0">
                                        <label>Paciente</label>
                                        <input type="text" wire:model.live="patientFilter" placeholder="Filtrar por paciente..." class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted small mb-0">0-30 Días</p>
                                    <p class="h5 mb-0">{{ number_format($summary['0-30'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted small mb-0">31-60 Días</p>
                                    <p class="h5 mb-0">{{ number_format($summary['31-60'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted small mb-0">61-90 Días</p>
                                    <p class="h5 mb-0">{{ number_format($summary['61-90'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger bg-opacity-10">
                                <div class="card-body text-center">
                                    <p class="text-danger small mb-0">Más de 90 Días</p>
                                    <p class="h5 text-danger mb-0">{{ number_format($summary['over_90'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-primary bg-opacity-10">
                                <div class="card-body text-center">
                                    <p class="text-primary small mb-0">Total CxC</p>
                                    <p class="h5 text-primary mb-0">{{ number_format($summary['total'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="patient" data-priority="1">
                                        <span class="font-weight-bold">Paciente</span>
                                    </th>
                                    <th data-column="invoice_number" data-priority="2">
                                        <span class="font-weight-bold">Factura #</span>
                                    </th>
                                    <th data-column="invoice_date" data-priority="3">
                                        <span class="font-weight-bold">Fecha Factura</span>
                                    </th>
                                    <th data-column="due_date" data-priority="4">
                                        <span class="font-weight-bold">Vencimiento</span>
                                    </th>
                                    <th data-column="balance" data-priority="5">
                                        <span class="font-weight-bold">Saldo</span>
                                    </th>
                                    <th data-column="days_overdue" data-priority="6">
                                        <span class="font-weight-bold">Días Vencido</span>
                                    </th>
                                    <th data-column="range" data-priority="7">
                                        <span class="font-weight-bold">Rango</span>
                                    </th>
                                    <th data-column="status" data-priority="8">
                                        <span class="font-weight-bold">Estado</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ageingData as $item)
                                    <tr class="table-row" data-row-id="{{ $item['invoice_number'] }}">
                                        <td data-column="patient" data-priority="1" data-label="Paciente">
                                            <span class="cell-content">{{ $item['patient'] }}</span>
                                        </td>
                                        <td data-column="invoice_number" data-priority="2" data-label="Factura #">
                                            <span class="cell-content">{{ $item['invoice_number'] }}</span>
                                        </td>
                                        <td data-column="invoice_date" data-priority="3" data-label="Fecha Factura">
                                            <span class="cell-content">{{ $item['invoice_date'] }}</span>
                                        </td>
                                        <td data-column="due_date" data-priority="4" data-label="Vencimiento">
                                            <span class="cell-content">{{ $item['due_date'] }}</span>
                                        </td>
                                        <td data-column="balance" data-priority="5" data-label="Saldo">
                                            <span class="cell-content font-weight-bold">{{ number_format($item['balance'], 2) }}</span>
                                        </td>
                                        <td data-column="days_overdue" data-priority="6" data-label="Días Vencido">
                                            <span class="cell-content {{ $item['days_overdue'] > 90 ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $item['days_overdue'] }}
                                            </span>
                                        </td>
                                        <td data-column="range" data-priority="7" data-label="Rango">
                                            <span class="badge me-1
                                                @switch($item['range'])
                                                    @case('0-30') bg-success @break
                                                    @case('31-60') bg-warning @break
                                                    @case('61-90') bg-info @break
                                                    @case('90+') bg-danger @break
                                                @endswitch
                                            ">
                                                {{ $item['range'] }} días
                                            </span>
                                        </td>
                                        <td data-column="status" data-priority="8" data-label="Estado">
                                            <span class="badge me-1
                                                @if($item['status'] === 'overdue') bg-danger
                                                @elseif($item['status'] === 'partial') bg-warning
                                                @elseif($item['status'] === 'paid') bg-success
                                                @else bg-primary
                                                @endif
                                            ">
                                                {{ match($item['status']) {
                                                    'pending' => 'Pendiente',
                                                    'partial' => 'Parcial',
                                                    'paid' => 'Pagada',
                                                    'overdue' => 'Vencida',
                                                    'cancelled' => 'Cancelada',
                                                    default => $item['status']
                                                } }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay cuentas por cobrar para este período
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
