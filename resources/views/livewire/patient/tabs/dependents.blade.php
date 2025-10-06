<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-users me-2 text-primary "></i>Dependientes y Relaciones Familiares
        </h5>
        <span class="badge bg-info">{{ $relationships->total() }} total</span>
    </div>
    <div class="card-body">
        @if($relationships->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Paciente Relacionado</th>
                            <th>Tipo de Relación</th>
                            <th>Contacto de Emergencia</th>
                            <th>Titular del Seguro</th>
                            <th>Estado</th>
                            <th>Fecha de Relación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relationships as $relationship)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        @php
                                            $relatedPatient = $relationship->patient_id == $patient_id ? $relationship->relatedPatient : $relationship->patient;
                                        @endphp
                                        @if($relatedPatient && $relatedPatient->avatar())
                                            <img src="{{ url('storage/'.$relatedPatient->avatar()->path) }}"
                                                 alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                 style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ $relatedPatient ? substr($relatedPatient->given_name, 0, 1) . substr($relatedPatient->family_name, 0, 1) : '??' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $relatedPatient?->name ?? 'Paciente eliminado' }}</div>
                                        @if($relatedPatient)
                                            <small class="text-muted">{{ $relatedPatient->identifier_type }}: {{ $relatedPatient->identifier }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ __('patient.'.strtolower($relationship->relationship_display)) }}</span>
                            </td>
                            <td>
                                @if($relationship->is_emergency_contact)
                                    <span class="badge bg-warning text-dark">
                                        <i class="feather-phone me-1"></i>Sí
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>
                                @if($relationship->is_insurance_subscriber)
                                    <span class="badge bg-success">
                                        <i class="feather-shield me-1"></i>Sí
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>
                                @if($relationship->is_active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $relationship->effective_date?->format('d/m/Y') ?: 'N/A' }}</div>
                                @if($relationship->end_date)
                                    <small class="text-muted">Hasta: {{ $relationship->end_date->format('d/m/Y') }}</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info and Load More -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $relationships->firstItem() }} a {{ $relationships->lastItem() }}
                        de {{ $relationships->total() }} relaciones
                    </small>
                </div>
                @if($relationships->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>Cargar más (5)
                    </button>
                @endif
            </div>

            <!-- Additional Information -->
            @if($relationships->where('is_emergency_contact', true)->count() > 0)
                <div class="alert alert-info mt-3">
                    <h6><i class="feather-info me-2"></i>Contactos de Emergencia</h6>
                    <div class="row">
                        @foreach($relationships->where('is_emergency_contact', true) as $contact)
                            @php
                                $contactPatient = $contact->patient_id == $patient_id ? $contact->relatedPatient : $contact->patient;
                            @endphp
                            <div class="col-md-6 mb-2">
                                <strong>{{ $contactPatient?->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $contact->relationship_display }}</small>
                                @if($contactPatient)
                                    <br><small>📞 {{ $contactPatient->phone ?: 'Sin teléfono' }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-users text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hay relaciones registradas</h5>
                    <p class="text-muted">Este paciente no tiene dependientes o relaciones familiares registradas en el sistema.</p>
                </div>
            </div>
        @endif
    </div>
</div>
