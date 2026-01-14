<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>false))
                        @slot('title')

                        @endslot
                    @slot('filter')
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" wire:model.live="search" class="form-control"
                                       placeholder="Buscar por nombre o email...">
                            </div>
                        </div>
                    @endslot
                        @slot('li_1')
                            {{ route('user.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    @if ($pendingUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Registro Médico</th>
                                    <th>Fecha Registro</th>
                                    <th>Documentos</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($pendingUsers as $user)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle"
                                                             src="{{ asset('assets/img/profiles/avatar-02.jpg') }}"
                                                             alt="User Image">
                                                    </span>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </h2>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->practitioner->phone ?? 'N/A' }}</td>
                                        <td>{{ $user->practitioner->registry ?? 'N/A' }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if ($user->files->count() >= 2)
                                                <span class="badge bg-success">
                                                        <i class="fa fa-check"></i> Completo
                                                    </span>
                                            @else
                                                <span class="badge bg-warning">
                                                        <i class="fa fa-exclamation-triangle"></i> Incompleto
                                                    </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="viewDocuments({{ $user->id }})"
                                                    class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> Revisar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pendingUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>
                            <p class="mt-3 text-muted">No hay usuarios pendientes de validación</p>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Modal para revisar documentos -->
            @if ($showModal && $selectedUser)
                <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Revisar Documentos</h5>
                                <button type="button" class="btn-close" wire:click="closeModal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Información del Usuario</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Nombre:</strong></td>
                                                <td>{{ $selectedUser->first_name }} {{ $selectedUser->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $selectedUser->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teléfono:</strong></td>
                                                <td>{{ $selectedUser->practitioner->phone ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Identificación:</strong></td>
                                                <td>{{ $selectedUser->practitioner->identifier ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registro Médico:</strong></td>
                                                <td>{{ $selectedUser->practitioner->registry ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Registro:</strong></td>
                                                <td>{{ $selectedUser->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Documentos Subidos</h6>
                                        @if ($documents->count() > 0)
                                            <div class="list-group">
                                                @foreach ($documents as $doc)
                                                    <div class="list-group-item">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fa fa-file-pdf text-danger"></i>
                                                                <strong>{{ ucfirst($doc->type) }}</strong>
                                                            </div>
                                                            <a href="{{ asset('storage/' . $doc->path) }}" target="_blank"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fa fa-eye"></i> Ver
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> No hay documentos disponibles
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-12">
                                        <h6>Acción de Validación</h6>

                                        <div class="mb-3">
                                            <label class="form-label">Motivo de rechazo (solo si rechazas el
                                                registro)</label>
                                            <textarea wire:model="rejectionReason" class="form-control" rows="3"
                                                      placeholder="Escribe el motivo del rechazo si decides rechazar este registro..."></textarea>
                                            @error('rejectionReason')
                                            <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                                <button type="button" class="btn btn-danger" wire:click="reject"
                                        wire:confirm="¿Estás seguro de rechazar este registro? Se enviará un email al usuario.">
                                    <i class="fa fa-times"></i> Rechazar
                                </button>
                                <button type="button" class="btn btn-success" wire:click="approve"
                                        wire:confirm="¿Estás seguro de aprobar este registro? El usuario podrá acceder al sistema.">
                                    <i class="fa fa-check"></i> Aprobar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
