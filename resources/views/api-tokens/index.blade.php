<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                   APi tokens
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} Api tokens
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-key"></i> Tokens API
            </h1>
            <p class="text-muted mb-0">Gestiona los tokens de acceso a la API del sistema</p>
        </div>
        <a href="{{ route('api-tokens.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Token
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($tokens->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>IPs Permitidas</th>
                                <th>Permisos</th>
                                <th>Último Uso</th>
                                <th>Expira</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tokens as $token)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $token->name }}</strong>
                                            @if($token->description)
                                                <br><small class="text-muted">{{ Str::limit($token->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($token->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Activo
                                            </span>
                                        @elseif($token->status === 'expired')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-clock"></i> Expirado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-ban"></i> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array('*', $token->allowed_ips))
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-globe"></i> Todas las IPs
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                {{ count($token->allowed_ips) }} IP(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array('*', $token->scopes))
                                            <span class="badge bg-primary">
                                                <i class="fas fa-star"></i> Acceso Total
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ count($token->scopes) }} Permiso(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->last_used_at)
                                            <small>
                                                {{ $token->last_used_at->diffForHumans() }}
                                                @if($token->last_used_ip)
                                                    <br><code class="small">{{ $token->last_used_ip }}</code>
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">Nunca usado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($token->expires_at)
                                            @if($token->expires_at->isPast())
                                                <span class="text-danger small">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    {{ $token->expires_at->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="small">
                                                    {{ $token->expires_at->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin expiración</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('api-tokens.show', $token) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('api-tokens.edit', $token) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($token->active)
                                                <form action="{{ route('api-tokens.toggle', $token) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Desactivar"
                                                            onclick="return confirm('¿Desactivar este token?')">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('api-tokens.toggle', $token) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="Activar">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('api-tokens.destroy', $token) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar este token permanentemente?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $tokens->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay tokens API</h4>
                    <p class="text-muted mb-4">Los tokens API permiten acceso programático al sistema sin usuarios tradicionales.</p>
                    <a href="{{ route('api-tokens.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Primer Token
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información sobre Tokens API</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">¿Qué son los Tokens API?</h6>
                    <p class="small text-muted">
                        Los tokens API permiten acceso programático al sistema sin necesidad de usuarios tradicionales.
                        Son ideales para integraciones externas, sistemas de monitoreo y APIs de terceros.
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Características de Seguridad</h6>
                    <ul class="small text-muted">
                        <li>Restricciones por IP y rangos CIDR</li>
                        <li>Permisos granulares por recurso</li>
                        <li>Fechas de expiración opcionales</li>
                        <li>Auditoría completa de uso</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</x-app-layout>>
