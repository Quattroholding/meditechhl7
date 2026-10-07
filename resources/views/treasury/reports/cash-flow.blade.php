<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Flujo de Caja
                @endslot
            @endcomponent

            <div class="row mb-4">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Filtros</h5>
                            <form method="GET" action="{{ route('treasury.reports.cash-flow') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="date_from">Fecha Inicial</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="date_to">Fecha Final</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', now()->toDateString()) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="bank_id">Banco</label>
                                            <select name="bank_id" id="bank_id" class="form-control">
                                                <option value="">Todos los bancos</option>
                                                @forelse(\App\Models\Treasury\Bank::where('client_id', auth()->user()->client_id)->get() as $bank)
                                                    <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->bank_name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-block local-forms">
                                            <label for="cash_register_id">Caja</label>
                                            <select name="cash_register_id" id="cash_register_id" class="form-control">
                                                <option value="">Todas las cajas</option>
                                                @forelse(\App\Models\Treasury\CashRegister::where('client_id', auth()->user()->client_id)->get() as $cash)
                                                    <option value="{{ $cash->id }}" {{ request('cash_register_id') == $cash->id ? 'selected' : '' }}>
                                                        {{ $cash->name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Generar Reporte
                                        </button>
                                        <a href="{{ route('treasury.reports.cash-flow') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-2"></i>Limpiar Filtros
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Saldo Inicial</h6>
                            <h4 class="mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Ingresos</h6>
                            <h4 class="mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Egresos</h6>
                            <h4 class="mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Saldo Final</h6>
                            <h4 class="mb-0">$0.00</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detalle del Flujo de Caja</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Para generar el reporte, selecciona los filtros arriba y haz clic en "Generar Reporte"
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
