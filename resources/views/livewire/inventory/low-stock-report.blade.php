<div>
    <div class="page-header">
        <h3 class="page-title">Reporte de Stock Bajo</h3>
    </div>

    <div class="card">
        <div class="card-body">
            @if($lowStockItems->count() > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Hay {{ $lowStockItems->count() }} items con stock bajo
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Disponible</th>
                                <th>Punto de Reorden</th>
                                <th>Cantidad a Ordenar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $item->total_available }} {{ $item->unit_of_measure }}
                                        </span>
                                    </td>
                                    <td>{{ $item->reorder_point }}</td>
                                    <td>{{ $item->reorder_quantity }}</td>
                                    <td>
                                        <a href="{{ route('inventory.stock.index', ['item' => $item->id]) }}"
                                           class="btn btn-sm btn-primary">
                                            Reabastecer
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    No hay items con stock bajo
                </div>
            @endif
        </div>
    </div>
</div>
