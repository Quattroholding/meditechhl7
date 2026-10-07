<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Reporte de Antigüedad - CxC
                @endslot
            @endcomponent

            <div class="row mb-4">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Filtros</h5>
                            <form method="GET" action="{{ route('finance.receivables.aging-report') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="as_of_date">Fecha de Corte</label>
                                            <input type="date" name="as_of_date" id="as_of_date" class="form-control" value="{{ request('as_of_date', now()->toDateString()) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="customer_id">Cliente</label>
                                            <select name="customer_id" id="customer_id" class="form-control">
                                                <option value="">Todos los clientes</option>
                                                @forelse(\App\Models\Patient::whereHas('clients', fn($q) => $q->where('client_id', auth()->user()->getCurrentClient()->id))->orderBy('name')->get() as $customer)
                                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="currency">Moneda</label>
                                            <select name="currency" id="currency" class="form-control">
                                                <option value="">Todas</option>
                                                <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="DOP" {{ request('currency') == 'DOP' ? 'selected' : '' }}>DOP</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search me-2"></i>Generar Reporte
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">0-30 Días</h6>
                            <h4 class="text-success mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">31-60 Días</h6>
                            <h4 class="text-info mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">61-90 Días</h6>
                            <h4 class="text-warning mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">91+ Días</h6>
                            <h4 class="text-danger mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detalle por Antigüedad</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rango de Antigüedad</th>
                                            <th>Cantidad de Facturas</th>
                                            <th>Monto Total</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0-30 Días</td>
                                            <td>0</td>
                                            <td>$0.00</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr>
                                            <td>31-60 Días</td>
                                            <td>0</td>
                                            <td>$0.00</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr>
                                            <td>61-90 Días</td>
                                            <td>0</td>
                                            <td>$0.00</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr>
                                            <td>91+ Días</td>
                                            <td>0</td>
                                            <td>$0.00</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr class="table-active fw-bold">
                                            <td>Total</td>
                                            <td>0</td>
                                            <td>$0.00</td>
                                            <td>100%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Este reporte muestra la antigüedad de las facturas pendientes de cobro al {{ now()->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
