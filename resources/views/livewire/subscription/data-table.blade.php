<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',['show_create'=>false])
                        @slot('title')

                        @endslot
                            @slot('filters')
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-group mb-0">
                                        <select class="form-control" wire:model.live="statusFilter">
                                            <option value="">Todos los estados</option>
                                            <option value="pending_activation">Pendiente de Activación</option>
                                            <option value="trial">Período de Prueba</option>
                                            <option value="active">Activa</option>
                                            <option value="past_due">Pago Vencido</option>
                                            <option value="suspended">Suspendida</option>
                                            <option value="cancelled">Cancelada</option>
                                            <option value="expired">Expirada</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <select class="form-control" wire:model.live="pagination">
                                            <option value="10">10 por página</option>
                                            <option value="15">15 por página</option>
                                            <option value="25">25 por página</option>
                                            <option value="50">50 por página</option>
                                            <option value="100">100 por página</option>
                                        </select>
                                    </div>
                                </div>
                            @endslot
                        @slot('li_1')

                        @endslot
                    @endcomponent

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th>
                                    <x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th>
                                    <x-table-sort-button title="Cliente" columnName="client_id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th>Paquete</th>
                                <th>
                                    <x-table-sort-button title="Estado" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th>Período Actual</th>
                                <th>
                                    <x-table-sort-button title="Próxima Facturación" columnName="next_billing_date" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th>Factura Pendiente</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        <strong>{{ $subscription->client->name }}</strong><br>
                                        <small class="text-muted">{{ $subscription->client->identifier }}</small>
                                    </td>
                                    <td>
                                        {{ $subscription->package->name }}<br>
                                        <small class="text-muted">
                                            ${{ number_format($subscription->package->base_price, 2) }}
                                            @if($subscription->extra_doctors_count > 0)
                                                + {{ $subscription->extra_doctors_count }} doctor(es) extra
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                <span class="badge bg-{{ $subscription->status->color() }}">
                                    {{ $subscription->status->label() }}
                                </span>
                                        @if($subscription->status->value === 'past_due' && $subscription->isInGracePeriod())
                                            <br><small class="text-warning">⏰ En período de gracia</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->current_period_starts_at && $subscription->current_period_ends_at)
                                            <small>
                                                {{ $subscription->current_period_starts_at->format('d/m/Y') }}<br>
                                                al {{ $subscription->current_period_ends_at->format('d/m/Y') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->next_billing_date)
                                            {{ $subscription->next_billing_date->format('d/m/Y') }}
                                            @if($subscription->days_until_renewal !== null)
                                                <br>
                                                <small class="text-muted">
                                                    @if($subscription->days_until_renewal > 0)
                                                        En {{ $subscription->days_until_renewal }} días
                                                    @elseif($subscription->days_until_renewal === 0)
                                                        Hoy
                                                    @else
                                                        Vencida hace {{ abs($subscription->days_until_renewal) }} días
                                                    @endif
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->currentInvoice)
                                            <span class="badge bg-warning">
                                        ${{ number_format($subscription->currentInvoice->total, 2) }}
                                    </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="showHistory({{ $subscription->id }})"
                                                    class="btn btn-info btn-sm"
                                                    title="Ver Historial de Estados">
                                                <i class="fa-solid fa-history"></i>
                                            </button>
                                            @can('subscriptions.view')
                                                <a href="{{ route('suscriptions.show', $subscription->id) }}"
                                                   class="btn btn-primary btn-sm"
                                                   title="Ver Detalles">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('subscriptions.edit')
                                                <a href="{{ route('suscriptions.edit', $subscription->id) }}"
                                                   class="btn btn-success btn-sm"
                                                   title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No se encontraron suscripciones</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$subscriptions])
                </div>
            </div>
            <!-- Modal de Historial de Estados -->
            @if($showHistoryModal && $selectedSubscription)
                <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-history me-2"></i>
                                    Historial de Estados - {{ $selectedSubscription->client->name }}
                                </h5>
                                <button type="button" class="btn-close" wire:click="closeHistoryModal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Información de la suscripción -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Cliente:</strong> {{ $selectedSubscription->client->name }}</p>
                                                <p class="mb-1"><strong>Paquete:</strong> {{ $selectedSubscription->package->name }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <strong>Estado Actual:</strong>
                                                    <span class="badge bg-{{ $selectedSubscription->status->color() }}">
                                                {{ $selectedSubscription->status->label() }}
                                            </span>
                                                </p>
                                                <p class="mb-1"><strong>Creada:</strong> {{ $selectedSubscription->created_at }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline de cambios de estado -->
                                <div class="timeline-wrapper">
                                    @forelse($statusHistory as $history)
                                        <div class="timeline-item mb-3">
                                            <div class="d-flex align-items-start">
                                                <div class="timeline-icon me-3">
                                                    <i class="fas fa-circle text-{{ $history->new_status === 'active' ? 'success' : ($history->new_status === 'suspended' ? 'danger' : 'warning') }}"></i>
                                                </div>
                                                <div class="timeline-content flex-grow-1">
                                                    <div class="card">
                                                        <div class="card-body py-2">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-1">
                                                                        @if($history->old_status)
                                                                            <span class="badge bg-secondary">{{ ucfirst($history->old_status) }}</span>
                                                                            <i class="fas fa-arrow-right mx-2"></i>
                                                                        @endif
                                                                        <span class="badge bg-{{ $history->new_status === 'active' ? 'success' : ($history->new_status === 'suspended' ? 'danger' : 'warning') }}">
                                                                    {{ ucfirst($history->new_status) }}
                                                                </span>
                                                                    </h6>
                                                                    <p class="mb-0 text-muted small">
                                                                        <i class="fas fa-user me-1"></i>
                                                                        {{ $history->user->full_name ?? 'Sistema' }}
                                                                    </p>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ $history->created_at->format('d/m/Y') }}<br>
                                                                        {{ $history->created_at->format('H:i:s') }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">No hay cambios de estado registrados</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeHistoryModal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <style>
                .timeline-wrapper {
                    position: relative;
                    padding-left: 20px;
                }

                .timeline-wrapper::before {
                    content: '';
                    position: absolute;
                    left: 5px;
                    top: 0;
                    bottom: 0;
                    width: 2px;
                    background: #e0e0e0;
                }

                .timeline-icon {
                    position: relative;
                    z-index: 1;
                    background: white;
                    width: 12px;
                    height: 12px;
                }

                .timeline-icon i {
                    font-size: 12px;
                }
            </style>
        </div>
    </div>
</div>

