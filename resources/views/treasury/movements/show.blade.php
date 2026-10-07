<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Detalle de Movimiento
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-md-4">
                    <!-- Información General -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información del Movimiento</h5>
                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Movimiento</label>
                                <p class="mb-0 fw-bold">{{ $movement->movement_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Tipo</label>
                                <p class="mb-0">
                                    @if($movement->isDeposit())
                                        <span class="badge bg-success">Depósito</span>
                                    @elseif($movement->isWithdrawal())
                                        <span class="badge bg-danger">Retiro</span>
                                    @elseif($movement->isTransfer())
                                        <span class="badge bg-info">Transferencia</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($movement->movement_type) }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha</label>
                                <p class="mb-0">{{ $movement->movement_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Monto</label>
                                <p class="mb-0 fw-bold text-success">{{ number_format($movement->amount, 2) }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Referencia</label>
                                <p class="mb-0">{{ $movement->reference_number ?? 'N/A' }}</p>
                            </div>
                            <hr class="my-3">
                            <div class="mb-3">
                                <label class="form-label text-muted">Creado Por</label>
                                <p class="mb-0">{{ $movement->createdBy->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Creación</label>
                                <p class="mb-0">{{ $movement->created_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones</h5>
                            <div class="d-grid gap-2">
                                @can('treasury.movements.create')
                                    <a href="{{ route('treasury.movements.edit', $movement) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                @endcan
                                <a href="{{ route('treasury.movements.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Información Detallada -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Descripción del Movimiento</h5>
                            <p class="mb-0">{{ $movement->description }}</p>
                        </div>
                    </div>

                    <!-- Cuentas Asociadas -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Cuentas Asociadas</h5>
                            <div class="row">
                                @if($movement->bank_id)
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Banco</label>
                                        <p class="mb-0">
                                            <a href="{{ route('treasury.banks.show', $movement->bank) }}">
                                                {{ $movement->bank->bank_name }} - {{ $movement->bank->account_number }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                                @if($movement->cash_register_id)
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Caja</label>
                                        <p class="mb-0">
                                            <a href="{{ route('treasury.cash-registers.show', $movement->cashRegister) }}">
                                                {{ $movement->cashRegister->name }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Información Contable -->
                    @if($movement->journalEntry)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Asiento Contable Asociado</h5>
                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Asiento</label>
                                <p class="mb-0">
                                    <a href="{{ route('accounting.journal-entries.show', $movement->journalEntry) }}">
                                        {{ $movement->journalEntry->entry_number }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Asiento</label>
                                <p class="mb-0">{{ $movement->journalEntry->entry_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
