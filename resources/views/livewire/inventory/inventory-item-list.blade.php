

<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>auth()->user()->can('inventory.create'),'title'=>'','li_1'=>route('inventory.items.create')))
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                               placeholder="Buscar por nombre, SKU o código de barras...">
                                    </div>
                                    <div class="col-md-3">
                                        <select wire:model.live="statusFilter" class="form-select">
                                            <option value="">Todos los estados</option>
                                            <option value="active">Activo</option>
                                            <option value="inactive">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select wire:model.live="itemTypeFilter" class="form-select">
                                            <option value="">Todos los tipos</option>
                                            <option value="supply">Suministro</option>
                                            <option value="consumable">Consumible</option>
                                            <option value="equipment">Equipo</option>
                                            <option value="medication">Medicamento</option>
                                            <option value="device">Dispositivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Nombre
                                    @if($sortField === 'name')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('sku')" style="cursor: pointer;">
                                    SKU
                                    @if($sortField === 'sku')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Tipo</th>
                                <th>Precio</th>
                                <th>Stock Total</th>
                                <th>Disponible</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->sku }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->item_type->label() }}</span>
                                    </td>
                                    <td>${{ number_format($item->base_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->total_stock }} {{ $item->unit_of_measure }}</span>
                                    </td>
                                    <td>
                                        @if($item->total_available <= 0)
                                            <span class="badge bg-danger">Sin stock</span>
                                        @elseif($item->reorder_point && $item->total_available <= $item->reorder_point)
                                            <span class="badge bg-warning">{{ $item->total_available }} (Bajo)</span>
                                        @else
                                            <span class="badge bg-success">{{ $item->total_available }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status->value === 'active')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status->value) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('inventory.items.edit', $item->id) }}"
                                               class="btn btn-sm btn-info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('inventory.stock.index', ['item' => $item->id]) }}"
                                               class="btn btn-sm btn-primary" title="Gestionar Stock">
                                                <i class="fas fa-boxes"></i>
                                            </a>
                                            <button wire:click="delete({{ $item->id }})"
                                                    wire:confirm="¿Está seguro de eliminar este item?"
                                                    class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No se encontraron items de inventario.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$items])
                </div>
            </div>
        </div>
    </div>
</div>
