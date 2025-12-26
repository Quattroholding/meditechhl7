<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="page-table-header mb-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <div class="top-nav-search table-search-blk">
                                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Buscar notificaciones...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <div class="btn-group mb-2" role="group">
                                    <button type="button" wire:click="$set('filterStatus', 'all')" class="btn btn-sm {{ $filterStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        Todas
                                    </button>
                                    <button type="button" wire:click="$set('filterStatus', 'unread')" class="btn btn-sm {{ $filterStatus === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        Sin leer
                                    </button>
                                    <button type="button" wire:click="$set('filterStatus', 'read')" class="btn btn-sm {{ $filterStatus === 'read' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        Leídas
                                    </button>
                                </div>
                                <button type="button" wire:click="markAllAsRead" class="btn btn-sm btn-success">
                                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>
                                        <x-table-sort-button title="Título" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    </th>
                                    <th>Mensaje</th>
                                    <th style="width: 150px;">
                                        <x-table-sort-button title="Fecha" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    </th>
                                    <th style="width: 100px;">Estado</th>
                                    <th style="width: 120px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notifications as $notification)
                                    <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                                        <td>
                                            @php
                                                $icon = $notification->data['icon'] ?? 'fas fa-bell';
                                                $priority = $notification->data['priority'] ?? 'normal';
                                                $priorityColor = $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'info');
                                            @endphp
                                            <span class="avatar avatar-sm bg-{{ $priorityColor }}">
                                                <i class="{{ $icon }}"></i>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $notification->read_at ? '' : 'fw-bold' }}">
                                                {{ $notification->data['title'] ?? 'Notificación' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $message = $notification->data['message'] ?? '';
                                                $truncated = strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message;
                                            @endphp
                                            {{ $truncated }}
                                            @if(isset($notification->data['steps']) && count($notification->data['steps']) > 0)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-list"></i> {{ count($notification->data['steps']) }} detalles
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $notification->created_at->format('d/m/Y H:i') }}</small><br>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($notification->read_at)
                                                <span class="badge bg-success">Leída</span>
                                            @else
                                                <span class="badge bg-warning">Sin leer</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if(!$notification->read_at)
                                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="btn btn-success btn-sm" title="Marcar como leída">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                @if(isset($notification->data['action']) && $notification->data['action'])
                                                    <a href="{{ $notification->data['action']['url'] ?? '#' }}" class="btn btn-primary btn-sm" title="{{ $notification->data['action']['text'] ?? 'Ver' }}">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay notificaciones</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
