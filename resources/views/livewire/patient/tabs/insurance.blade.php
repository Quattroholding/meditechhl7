<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-shield me-2 text-primary"></i>Pólizas de Seguro
        </h5>
        <span class="badge bg-info">{{ $insurancePolicies->total() }} total</span>
    </div>
    <div class="card-body">
        @if($insurancePolicies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Compañía de Seguro</th>
                            <th>Número de Póliza</th>
                            <th>Prioridad</th>
                            <th>Titular</th>
                            <th>Relación</th>
                            <th>Estado</th>
                            <th>Vigencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insurancePolicies as $policy)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $policy->insuranceCompany?->name ?? 'Sin aseguradora' }}</div>
                                @if($policy->group_number)
                                    <small class="text-muted">Grupo: {{ $policy->group_number }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $policy->policy_number }}</code>
                                @if($policy->subscriber_id)
                                    <br><small class="text-muted">ID: {{ $policy->subscriber_id }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($policy->priority)
                                    @case('primary')
                                        <span class="badge bg-primary">Primario</span>
                                        @break
                                    @case('secondary')
                                        <span class="badge bg-info">Secundario</span>
                                        @break
                                    @case('tertiary')
                                        <span class="badge bg-secondary">Terciario</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ ucfirst($policy->priority) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($policy->subscriberPatient)
                                    <div class="fw-medium">{{ $policy->subscriberPatient->name }}</div>
                                    <small class="text-muted">{{ $policy->subscriberPatient->identifier }}</small>
                                @else
                                    <div class="fw-medium">{{ $policy->subscriber_name ?: 'No especificado' }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($policy->relationship_to_subscriber) }}</span>
                            </td>
                            <td>
                                @if($policy->isActive())
                                    <span class="badge bg-success">Activa</span>
                                @elseif($policy->isExpired())
                                    <span class="badge bg-danger">Expirada</span>
                                @else
                                    <span class="badge bg-warning">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $policy->effective_date->format('d/m/Y') }}</div>
                                @if($policy->expiration_date)
                                    <small class="text-muted">hasta {{ $policy->expiration_date->format('d/m/Y') }}</small>
                                @else
                                    <small class="text-success">Sin expiración</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $insurancePolicies->firstItem() }} a {{ $insurancePolicies->lastItem() }}
                        de {{ $insurancePolicies->total() }} pólizas
                    </small>
                </div>
                @if($insurancePolicies->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>Cargar más (5)
                    </button>
                @endif
            </div>

            <!-- Coverage Summary -->
            @if($insurancePolicies->where('is_active', true)->count() > 0)
                <div class="alert alert-info mt-3">
                    <h6><i class="feather-info me-2"></i>Resumen de Cobertura Activa</h6>
                    <div class="row">
                        @foreach($insurancePolicies->where('is_active', true) as $policy)
                            <div class="col-md-6 mb-2">
                                <strong>{{ $policy->priority === 'primary' ? 'Seguro Primario' : 'Seguro Secundario' }}</strong>
                                <br>Cobertura: {{ $policy->coverage_percentage }}%
                                @if($policy->copay_amount > 0)
                                    <br>Copago: ${{ number_format($policy->copay_amount, 2) }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-shield text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hay seguros registrados</h5>
                    <p class="text-muted">Este paciente no tiene pólizas de seguro médico registradas.</p>
                </div>
            </div>
        @endif
    </div>
</div>
