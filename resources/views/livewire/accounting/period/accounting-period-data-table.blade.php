<div>
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>Períodos Contables</h4>
                    <p class="text-muted">Gestiona los períodos contables y su estado</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#periodCreateModal">
                    <i class="fas fa-plus"></i> Nuevo Período
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="input-block local-forms">
                <label>Buscar</label>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar período...">
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-block local-forms">
                <label>Estado</label>
                <select wire:model.live="statusFilter" class="form-control">
                    <option value="">Todos</option>
                    <option value="open">Abierto</option>
                    <option value="closed">Cerrado</option>
                    <option value="locked">Bloqueado</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-block local-forms">
                <label>Año Fiscal</label>
                <select wire:model.live="yearFilter" class="form-control">
                    @for ($year = now()->year - 2; $year <= now()->year + 2; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Período</th>
                                    <th>Año Fiscal</th>
                                    <th>Rango de Fechas</th>
                                    <th>Estado</th>
                                    <th>Días Restantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periods as $period)
                                    <tr>
                                        <td>
                                            <strong>{{ $period->name }}</strong>
                                        </td>
                                        <td>{{ $period->fiscal_year }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $period->start_date->format('d/m/Y') }} -
                                                {{ $period->end_date->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($period->status === 'open')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Abierto
                                                </span>
                                            @elseif ($period->status === 'closed')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-lock-open"></i> Cerrado
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-lock"></i> Bloqueado
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($period->isOpen())
                                                <small class="text-primary">
                                                    {{ $period->getDaysRemaining() }} días
                                                </small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if ($period->isOpen())
                                                        <li>
                                                            <button type="button" class="dropdown-item"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#periodCloseModal"
                                                                wire:click="$dispatch('edit-period', { id: {{ $period->id }} })">
                                                                <i class="fas fa-times-circle"></i> Cerrar
                                                            </button>
                                                        </li>
                                                    @endif
                                                    @if ($period->isClosed())
                                                        <li>
                                                            <button type="button" class="dropdown-item"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#periodLockModal"
                                                                wire:click="$dispatch('edit-period', { id: {{ $period->id }} })">
                                                                <i class="fas fa-lock"></i> Bloquear
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-eye"></i> Ver Detalles
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay períodos contables
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            {{ $periods->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Período -->
    <livewire:accounting.period.period-create-modal lazy />

    <!-- Modal Cerrar Período -->
    <livewire:accounting.period.period-close-modal lazy />

    <!-- Modal Bloquear Período -->
    <livewire:accounting.period.period-lock-modal lazy />
</div>
