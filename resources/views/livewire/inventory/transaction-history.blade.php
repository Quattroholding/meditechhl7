<div>
    <div class="page-header">
        <h3 class="page-title">Historial de Transacciones</h3>
    </div>

    <div class="card">
        <div class="">
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="input-block local-forms">
                        <x-input-label for="itemFilter" value="Filtrar por Item"/>
                        <select wire:model.live="itemFilter" class="form-select" id="itemFilter">
                            <option value="">Todos los items</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-block local-forms">
                        <x-input-label for="typeFilter" value="Tipo de Transacción"/>
                        <select wire:model.live="typeFilter" class="form-select" id="typeFilter">
                            <option value="">Todos los tipos</option>
                            <option value="purchase">Compra</option>
                            <option value="adjustment">Ajuste</option>
                            <option value="transfer">Transferencia</option>
                            <option value="supply_delivery">Dispensación</option>
                            <option value="disposal">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-block local-forms">
                        <x-input-label for="dateFrom" value="Desde"/>
                        <x-text-input wire:model.live="dateFrom" class="block mt-1 w-full" type="date" name="dateFrom" id="dateFrom"/>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="input-block local-forms">
                        <x-input-label for="dateTo" value="Hasta"/>
                        <x-text-input wire:model.live="dateTo" class="block mt-1 w-full" type="date" name="dateTo" id="dateTo"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Item</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                            <th>Paciente</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trans)
                            <tr>
                                <td>{{ $trans->transaction_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $badges = [
                                            'purchase' => 'success',
                                            'adjustment' => 'warning',
                                            'transfer' => 'info',
                                            'supply_delivery' => 'primary',
                                            'disposal' => 'danger'
                                        ];
                                        $badge = $badges[$trans->transaction_type->value] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($trans->transaction_type->value) }}</span>
                                </td>
                                <td>{{ $trans->inventoryItem->name }}</td>
                                <td>
                                    @if($trans->quantity_change > 0)
                                        <span class="text-success">+{{ $trans->quantity_change }}</span>
                                    @elseif($trans->quantity_change < 0)
                                        <span class="text-danger">{{ $trans->quantity_change }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>{{ $trans->performedByUser->full_name }}</td>
                                <td>{{ $trans->patient?->name ?? '-' }}</td>
                                <td>{{ Str::limit($trans->reason, 50) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No hay transacciones para mostrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
