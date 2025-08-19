<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-activity me-2 text-primary"></i>Condiciones Médicas
        </h5>
        <span class="badge bg-info">{{ $conditions->total() }} total</span>
    </div>
    <div class="card-body">
        @if($conditions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Diagnóstico</th>
                            <th>Código ICD-10</th>
                            <th>Estado</th>
                            <th>Médico</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conditions as $condition)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $condition->diagnosis_name }}</div>
                                @if($condition->notes)
                                    <small class="text-muted">{{ Str::limit($condition->notes, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($condition->icd10Code)
                                    <code>{{ $condition->icd10Code->code }}</code>
                                    <br><small class="text-muted">{{ Str::limit($condition->icd10Code->description, 30) }}</small>
                                @else
                                    <span class="text-muted">Sin código</span>
                                @endif
                            </td>
                            <td>
                                @switch($condition->clinical_status)
                                    @case('active')
                                        <span class="badge bg-danger">Activa</span>
                                        @break
                                    @case('resolved')
                                        <span class="badge bg-success">Resuelta</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge bg-secondary">Inactiva</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning">{{ ucfirst($condition->clinical_status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($condition->practitioner)
                                    <div class="fw-medium">{{ $condition->practitioner->name }}</div>
                                    <small class="text-muted">{{ $condition->practitioner->specialty ?? 'Médico General' }}</small>
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $condition->onset_date?->format('d/m/Y') ?: $condition->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $condition->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $conditions->firstItem() }} a {{ $conditions->lastItem() }}
                        de {{ $conditions->total() }} condiciones
                    </small>
                </div>
                @if($conditions->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>Cargar más (5)
                    </button>
                @endif
            </div>
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-activity text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hay condiciones registradas</h5>
                    <p class="text-muted">Este paciente no tiene diagnósticos o condiciones médicas registradas.</p>
                </div>
            </div>
        @endif
    </div>
</div>
