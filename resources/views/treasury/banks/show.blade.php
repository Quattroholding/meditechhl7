<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Detalle de Banco
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-md-4">
                    <!-- Información General -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información General</h5>
                            <div class="mb-3">
                                <label class="form-label text-muted">Banco</label>
                                <p class="mb-0 fw-bold">{{ $bank->bank_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Cuenta</label>
                                <p class="mb-0 fw-bold">{{ $bank->account_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Tipo de Cuenta</label>
                                <p class="mb-0">
                                    @if($bank->isChecking())
                                        <span class="badge bg-info">Cuenta Corriente</span>
                                    @else
                                        <span class="badge bg-success">Cuenta de Ahorros</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Moneda</label>
                                <p class="mb-0 fw-bold">{{ $bank->currency }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Saldo Actual</label>
                                <p class="mb-0 fw-bold text-success">{{ number_format($bank->balance, 2) }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="mb-0">
                                    @if($bank->isActive())
                                        <span class="badge bg-success">Activo</span>
                                    @elseif($bank->isSuspended())
                                        <span class="badge bg-warning">Suspendido</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                            <hr class="my-3">
                            <div class="mb-3">
                                <label class="form-label text-muted">Creado Por</label>
                                <p class="mb-0">{{ $bank->createdBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Creación</label>
                                <p class="mb-0">{{ $bank->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones</h5>
                            <div class="d-grid gap-2">
                                @can('treasury.banks.manage')
                                    <a href="{{ route('treasury.banks.edit', $bank) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                    <a href="{{ route('treasury.banks.movements', $bank) }}" class="btn btn-info">
                                        <i class="fas fa-exchange-alt me-2"></i>Ver Movimientos
                                    </a>
                                    <a href="{{ route('treasury.banks.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Información Contable -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información Contable</h5>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label text-muted">Cuenta Contable Asociada</label>
                                    @if($bank->accountingAccount)
                                        <p class="mb-0 fw-bold">
                                            {{ $bank->accountingAccount->account_number }} - {{ $bank->accountingAccount->account_name }}
                                        </p>
                                    @else
                                        <p class="mb-0 text-muted">No hay cuenta contable asociada</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Últimos Movimientos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Últimos Movimientos</h5>
                        </div>
                        <div class="card-body">
                            @if($bank->treasuryMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bank->treasuryMovements()->latest()->limit(10)->get() as $movement)
                                                <tr>
                                                    <td>{{ $movement->movement_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($movement->isDeposit())
                                                            <span class="badge bg-success">Depósito</span>
                                                        @elseif($movement->isWithdrawal())
                                                            <span class="badge bg-danger">Retiro</span>
                                                        @elseif($movement->isTransfer())
                                                            <span class="badge bg-info">Transferencia</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($movement->movement_type) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold">{{ number_format($movement->amount, 2) }}</td>
                                                    <td>{{ Str::limit($movement->description, 40) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('treasury.banks.movements', $bank) }}" class="btn btn-sm btn-outline-primary">
                                        Ver todos los movimientos
                                    </a>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No hay movimientos registrados</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
