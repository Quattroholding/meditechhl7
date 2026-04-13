<div>
    <!-- Filtros y búsqueda -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" wire:model.live="search"
                   placeholder="Buscar por número, empresa o RUC...">
        </div>
        <div class="col-md-3">
            <select class="form-control" wire:model.live="statusFilter">
                <option value="all">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="sent">Enviada</option>
                <option value="accepted">Aceptada</option>
                <option value="rejected">Rechazada</option>
                <option value="expired">Expirada</option>
                <option value="cancelled">Cancelada</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-control" wire:model.live="pagination">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
            </select>
        </div>
        <div class="col-md-3 text-right">
            @can('quotations.create')
            <button type="button" class="btn btn-primary"
                    wire:click="openCreateModal">
                <i class="fa fa-plus"></i> Nueva Cotización
            </button>
            @endcan
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th wire:click="sortBy('quotation_number')" style="cursor: pointer;">
                        Número
                        @if($sortField === 'quotation_number')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('client_company_name')" style="cursor: pointer;">
                        Empresa
                        @if($sortField === 'client_company_name')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>RUC</th>
                    <th wire:click="sortBy('issue_date')" style="cursor: pointer;">
                        Fecha Emisión
                        @if($sortField === 'issue_date')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('expiration_date')" style="cursor: pointer;">
                        Vencimiento
                        @if($sortField === 'expiration_date')
                            <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-right">Total</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $quotation)
                <tr wire:key="quotation-{{ $quotation->id }}">
                    <td><strong>{{ $quotation->quotation_number }}</strong></td>
                    <td>{{ $quotation->client_company_name }}</td>
                    <td>{{ $quotation->client_ruc }}</td>
                    <td>{{ $quotation->issue_date->format('d/m/Y') }}</td>
                    <td>
                        {{ $quotation->expiration_date->format('d/m/Y') }}
                        @if($quotation->isExpired() && $quotation->status !== 'accepted')
                            <span class="badge bg-warning">Expirada</span>
                        @endif
                    </td>
                    <td class="text-right">${{ $quotation->formatted_total }}</td>
                    <td>{!! $quotation->status_badge !!}</td>
                    <td class="text-center">

                                <a  href="{{ route('quotations.pdf.stream', $quotation->id) }}" class="btn btn-info btn-sm" title="Ver"
                                   target="_blank">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a  href="{{ route('quotations.pdf.download', $quotation->id) }}" class="btn btn-warning btn-sm" title="Descargar">
                                    <i class="fa fa-download"></i>
                                </a>
                                @can('quotations.edit')
                                    @if($quotation->canBeEdited())
                                    <a  href="javascript:void(0);"
                                       wire:click="openEditModal({{ $quotation->id }})"  class="btn btn-primary btn-sm" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endif
                                @endcan
                                @can('quotations.delete')
                                    @if($quotation->canBeEdited())
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                       onclick="confirmDelete({{ $quotation->id }})" title="eliminar">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    @endif
                                @endcan

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay cotizaciones para mostrar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $data->links() }}
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteQuotation', id);
                }
            });
        }
    </script>
    @endpush
</div>
