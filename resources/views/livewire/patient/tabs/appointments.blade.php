<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-calendar me-2 text-primary"></i>Historial de Citas
        </h5>
        <span class="badge bg-info">{{ $appointments->total() }} total</span>
    </div>
    <div class="card-body">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Médico</th>
                            <th>Especialidad</th>
                            <th>Consultorio</th>
                            <th>Estado</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $appointment->appointment_date->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $appointment->start_time }} - {{ $appointment->end_time }}</small>
                            </td>
                            <td>
                                @if($appointment->practitioner)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($appointment->practitioner->avatar())
                                                <img src="{{ url('storage/'.$appointment->practitioner->avatar()->path) }}"
                                                     alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                     style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ substr($appointment->practitioner->given_name, 0, 1) }}{{ substr($appointment->practitioner->family_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $appointment->practitioner->name }}</div>
                                            <small class="text-muted">{{ $appointment->practitioner->identifier }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $appointment->practitioner?->specialty ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                @if($appointment->consultingRoom)
                                    <div class="fw-medium">{{ $appointment->consultingRoom->name }}</div>
                                    <small class="text-muted">{{ $appointment->consultingRoom->location ?? 'Sin ubicación' }}</small>
                                @else
                                    <span class="text-muted">Sin consultorio</span>
                                @endif
                            </td>
                            <td>
                                @switch($appointment->status)
                                    @case('scheduled')
                                        <span class="badge bg-primary">Programada</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-info">Confirmada</span>
                                        @break
                                    @case('in-progress')
                                        <span class="badge bg-warning">En Curso</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Completada</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Cancelada</span>
                                        @break
                                    @case('no-show')
                                        <span class="badge bg-dark">No Asistió</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($appointment->appointment_type)
                                    @case('consultation')
                                        <span class="badge bg-light text-dark">Consulta</span>
                                        @break
                                    @case('follow-up')
                                        <span class="badge bg-secondary">Seguimiento</span>
                                        @break
                                    @case('emergency')
                                        <span class="badge bg-danger">Emergencia</span>
                                        @break
                                    @case('routine')
                                        <span class="badge bg-info">Rutina</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ ucfirst($appointment->appointment_type ?? 'General') }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $appointments->firstItem() }} a {{ $appointments->lastItem() }}
                        de {{ $appointments->total() }} citas
                    </small>
                </div>
                @if($appointments->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>Cargar más (5)
                    </button>
                @endif
            </div>

            <!-- Quick Stats -->
            @php
                $completed = $appointments->where('status', 'completed')->count();
                $cancelled = $appointments->where('status', 'cancelled')->count();
                $noShow = $appointments->where('status', 'no-show')->count();
            @endphp
            @if($completed > 0 || $cancelled > 0 || $noShow > 0)
                <div class="alert alert-light mt-3">
                    <h6><i class="feather-bar-chart me-2"></i>Estadísticas Rápidas</h6>
                    <div class="row text-center">
                        @if($completed > 0)
                            <div class="col-md-4">
                                <div class="text-success">
                                    <strong>{{ $completed }}</strong>
                                    <br><small>Completadas</small>
                                </div>
                            </div>
                        @endif
                        @if($cancelled > 0)
                            <div class="col-md-4">
                                <div class="text-danger">
                                    <strong>{{ $cancelled }}</strong>
                                    <br><small>Canceladas</small>
                                </div>
                            </div>
                        @endif
                        @if($noShow > 0)
                            <div class="col-md-4">
                                <div class="text-warning">
                                    <strong>{{ $noShow }}</strong>
                                    <br><small>No Asistió</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-calendar text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hay citas registradas</h5>
                    <p class="text-muted">Este paciente no tiene citas médicas registradas en el sistema.</p>
                </div>
            </div>
        @endif
    </div>
</div>
