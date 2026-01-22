<div>
    <div class="row">
        @if(auth()->user()->hasRole('admin'))
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card inovices-card">
                    <div class="card-body">
                        <div class="inovices-widget-header">
                        <span class="inovices-widget-icon">
                            <i class="fas fa-folder-open"></i>
                        </span>
                            <div class="inovices-dash-count">
                                <div class="inovices-amount">{{ $stats['open'] }}</div>
                            </div>
                        </div>
                        <p class="inovices-all">Tickets Abiertos</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card inovices-card">
                    <div class="card-body">
                        <div class="inovices-widget-header">
                        <span class="inovices-widget-icon">
                            <i class="fas fa-spinner"></i>
                        </span>
                            <div class="inovices-dash-count">
                                <div class="inovices-amount">{{ $stats['in_progress'] }}</div>
                            </div>
                        </div>
                        <p class="inovices-all">En Progreso</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card inovices-card">
                    <div class="card-body">
                        <div class="inovices-widget-header">
                        <span class="inovices-widget-icon">
                            <i class="fas fa-check-circle"></i>
                        </span>
                            <div class="inovices-dash-count">
                                <div class="inovices-amount">{{ $stats['resolved'] }}</div>
                            </div>
                        </div>
                        <p class="inovices-all">Resueltos</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card inovices-card">
                    <div class="card-body">
                        <div class="inovices-widget-header">
                        <span class="inovices-widget-icon">
                            <i class="fas fa-user"></i>
                        </span>
                            <div class="inovices-dash-count">
                                <div class="inovices-amount">{{ $stats['my_tickets'] }}</div>
                            </div>
                        </div>
                        <p class="inovices-all">Mis Tickets</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',['show_create'=>false])
                        @slot('title')

                        @endslot

                        @slot('filters')
                            <!-- Filtros -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="filterStatus">
                                        <option value="">Todos los Estados</option>
                                        <option value="open">Abierto</option>
                                        <option value="in_progress">En Progreso</option>
                                        <option value="on_hold">En Espera</option>
                                        <option value="resolved">Resuelto</option>
                                        <option value="closed">Cerrado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="filterPriority">
                                        <option value="">Todas las Prioridades</option>
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                        <option value="critical">Crítica</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="filterCategory">
                                        <option value="">Todas las Categorías</option>
                                        <option value="bug">Error</option>
                                        <option value="question">Pregunta</option>
                                        <option value="feature_request">Nueva Función</option>
                                        <option value="improvement">Mejora</option>
                                        <option value="technical_support">Soporte Técnico</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>
                                @if(auth()->user()->hasRole(['admin', 'admin client']))
                                    <div class="col-md-3">
                                        <select class="form-select" wire:model.live="filterAssigned">
                                            <option value="">Todos</option>
                                            <option value="me">Asignados a mí</option>
                                            <option value="unassigned">Sin Asignar</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        @endslot
                        @slot('buttons')
                            <button type="button" class="btn btn-primary submit-form add-pluss py-2" onclick="Livewire.dispatch('openTicketModal')">
                                <i class="fas fa-ticket-alt me-2"></i>Crear Ticket de Soporte
                            </button>
                        @endslot
                        @slot('li_1')
                            {{ route('user.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                   <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th wire:click="sortBy('identifier')" style="cursor: pointer;">
                                    Código
                                    @if($sortField === 'identifier')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('title')" style="cursor: pointer;">
                                    Título
                                    @if($sortField === 'title')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Prioridad</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                @if(auth()->user()->hasRole(['admin', 'admin client']))
                                    <th>Asignado a</th>
                                @endif
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Fecha
                                    @if($sortField === 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th class="text-end">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $ticket->identifier }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            {{ Str::limit($ticket->title, 50) }}
                                            @if($ticket->comments->count() > 0)
                                                <span class="badge bg-info ms-1">
                                                        <i class="fas fa-comments"></i> {{ $ticket->comments->count() }}
                                                    </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                            <span class="badge bg" style="background-color: #{{ App\Models\Ticket::priorityColors()[$ticket->priority] }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                    </td>
                                    <td>{{ $ticket->category_label }}</td>
                                    <td>
                                         <span class="cell-content">
                                             <span class="badge bg-{{ $ticket->status->color() }}">
                                                {{ $ticket->status->label() }}
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $ticket->user->full_name ?? 'N/A' }}</td>
                                    @if(auth()->user()->hasRole(['admin', 'admin client']))
                                        <td>
                                            @if($ticket->assignedTo)
                                                {{ $ticket->assignedTo->full_name }}
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>{{ $ticket->created_at }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-info" wire:click="openDetailModal({{ $ticket->id }})" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($ticket->user_id === auth()->id() && $ticket->status === 'open')
                                            <button class="btn btn-sm btn-primary" wire:click="$dispatch('openTicketModal', { ticketId: {{ $ticket->id }} })" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No hay tickets para mostrar</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$tickets])
                </div>
            </div>
        </div>
    </div>
    <!-- Modales -->
    @livewire('ticket-system.modal-save')
    @livewire('ticket-system.detail-modal')
</div>
