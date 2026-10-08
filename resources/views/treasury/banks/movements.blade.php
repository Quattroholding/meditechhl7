<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Movimientos del Banco
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>{{ $bank->bank_name }}</h5>
                                    <p class="text-muted mb-0">Cuenta: {{ $bank->account_number }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-0"><span class="text-muted">Saldo Actual:</span></p>
                                    <h5 class="text-success">{{ number_format($bank->balance, 2) }} {{ $bank->currency }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Movimientos</h5>
                                <a href="{{ route('treasury.banks.show', $bank) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($bank->treasuryMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Número</th>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                                <th>Descripción</th>
                                                <th>Referencia</th>
                                                <th>Creado Por</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bank->treasuryMovements()->latest('movement_date')->get() as $movement)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('treasury.movements.show', $movement) }}">
                                                            {{ $movement->movement_number }}
                                                        </a>
                                                    </td>
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
                                                    <td class="fw-bold">
                                                        @if($movement->isWithdrawal())
                                                            <span class="text-danger">-</span>
                                                        @else
                                                            <span class="text-success">+</span>
                                                        @endif
                                                        {{ number_format($movement->amount, 2) }}
                                                    </td>
                                                    <td>{{ Str::limit($movement->description, 40) }}</td>
                                                    <td>{{ $movement->reference_number ?? '-' }}</td>
                                                    <td>{{ $movement->createdBy->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>No hay movimientos registrados para este banco
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
