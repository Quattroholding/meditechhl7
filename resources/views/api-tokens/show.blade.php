<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    APi tokens
                @endslot
                @slot('li_1')
                    Token API -  {{$apiToken->name}}
                @endslot
            @endcomponent
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-key"></i> {{ $apiToken->name }}
                        </h1>
                        <p class="text-muted mb-0">Detalles del token API</p>
                    </div>
                    <div>
                        <a href="{{ route('api-tokens.edit', $apiToken) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('api-tokens.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('new_token'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡IMPORTANTE! Guarda este token ahora, no podrás verlo nuevamente:</strong>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control me-2" id="newTokenValue" value="{{ session('new_token') }}" readonly>
                            <button class="btn btn-outline-primary" onclick="copyToken()" title="Copiar">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Información General -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl>
                                            <dt>Nombre:</dt>
                                            <dd>{{ $apiToken->name }}</dd>

                                            <dt>Estado:</dt>
                                            <dd>
                                                @if($apiToken->status === 'active')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Activo
                                                    </span>
                                                @elseif($apiToken->status === 'expired')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-clock"></i> Expirado
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-ban"></i> Inactivo
                                                    </span>
                                                @endif
                                            </dd>

                                            <dt>Token ID:</dt>
                                            <dd><code>{{ substr($apiToken->token, 0, 10) }}...{{ substr($apiToken->token, -10) }}</code></dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl>
                                            <dt>Creado por:</dt>
                                            <dd>{{ $apiToken->created_by ?? 'Sistema' }}</dd>

                                            <dt>Fecha de creación:</dt>
                                            <dd>{{ $apiToken->created_at }}</dd>

                                            <dt>Fecha de expiración:</dt>
                                            <dd>
                                                @if($apiToken->expires_at)
                                                    @if($apiToken->expires_at->isPast())
                                                        <span class="text-danger">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ $apiToken->expires_at->format('d/m/Y H:i:s') }}
                                                            (Expirado {{ $apiToken->expires_at->diffForHumans() }})
                                                        </span>
                                                    @else
                                                        {{ $apiToken->expires_at->format('d/m/Y H:i:s') }}
                                                        ({{ $apiToken->expires_at->diffForHumans() }})
                                                    @endif
                                                @else
                                                    <span class="text-muted">Sin expiración</span>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>

                                @if($apiToken->description)
                                    <hr>
                                    <dt>Descripción:</dt>
                                    <dd>{{ $apiToken->description }}</dd>
                                @endif

                                @if($apiToken->practitioner)
                                    <hr>
                                    <dt><i class="fas fa-user-md"></i> Practitioner Asociado:</dt>
                                    <dd>
                                        <span class="badge bg-info">
                                            {{ $apiToken->practitioner->name }} ({{ $apiToken->practitioner->identifier }})
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            Este token está asociado a un practitioner específico para integraciones como HemoScreen
                                        </small>
                                    </dd>
                                @endif
                            </div>
                        </div>

                        <!-- Información de Uso -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Información de Uso</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dt>Último uso:</dt>
                                        <dd>
                                            @if($apiToken->last_used_at)
                                                {{ $apiToken->last_used_at->format('d/m/Y H:i:s') }}
                                                <br><small class="text-muted">({{ $apiToken->last_used_at->diffForHumans() }})</small>
                                            @else
                                                <span class="text-muted">Nunca usado</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="col-md-6">
                                        <dt>Última IP:</dt>
                                        <dd>
                                            @if($apiToken->last_used_ip)
                                                <code>{{ $apiToken->last_used_ip }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Acciones -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-cog"></i> Acciones</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    @if($apiToken->active)
                                        <form action="{{ route('api-tokens.toggle', $apiToken) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('¿Desactivar este token?')">
                                                <i class="fas fa-pause"></i> Desactivar Token
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('api-tokens.toggle', $apiToken) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-play"></i> Activar Token
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('api-tokens.regenerate', $apiToken) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-info w-100" onclick="return confirm('¿Regenerar el token? El token actual dejará de funcionar.')">
                                            <i class="fas fa-refresh"></i> Regenerar Token
                                        </button>
                                    </form>

                                    <hr>

                                    <form action="{{ route('api-tokens.destroy', $apiToken) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Eliminar este token permanentemente?')">
                                            <i class="fas fa-trash"></i> Eliminar Token
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- IPs Permitidas -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-network-wired"></i> IPs Permitidas</h6>
                            </div>
                            <div class="card-body">
                                @if(in_array('*', $apiToken->allowed_ips))
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-globe"></i> Todas las IPs
                                    </span>
                                    <p class="small text-muted mt-2">Este token puede ser usado desde cualquier dirección IP.</p>
                                @else
                                    @foreach($apiToken->allowed_ips as $ip)
                                        <div class="mb-2">
                                            <code class="bg-light p-1 rounded">{{ $ip }}</code>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Permisos -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Permisos</h6>
                            </div>
                            <div class="card-body">
                                @if(in_array('*', $apiToken->scopes))
                                    <span class="badge bg-primary mb-2">
                                        <i class="fas fa-star"></i> Acceso Total
                                    </span>
                                    <p class="small text-muted">Este token tiene acceso completo a todos los recursos de la API.</p>
                                @else
                                    @foreach($apiToken->scopes as $scope)
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">{{ $scope }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ejemplos de Uso -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-code"></i> Ejemplos de Uso</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Bearer Token (Recomendado)</h6>
                                <pre class="bg-light p-2 rounded small"><code>Authorization: Bearer mdt_[TOKEN]</code></pre>
                            </div>
                            <div class="col-md-4">
                                <h6>Header Personalizado</h6>
                                <pre class="bg-light p-2 rounded small"><code>X-API-Token: mdt_[TOKEN]</code></pre>
                            </div>
                            <div class="col-md-4">
                                <h6>Query Parameter</h6>
                                <pre class="bg-light p-2 rounded small"><code>?api_token=mdt_[TOKEN]</code></pre>
                            </div>
                        </div>

                        <hr>

                        <h6>Ejemplo cURL</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>curl -H "Authorization: Bearer mdt_[TOKEN]" \
                 {{ url('/api/v1/practitioners') }}</code></pre>
                    </div>
                </div>
            </div>
            <script>
            function copyToken() {
                const tokenInput = document.getElementById('newTokenValue');
                tokenInput.select();
                tokenInput.setSelectionRange(0, 99999); // Para móviles

                try {
                    document.execCommand('copy');

                    // Cambiar el icono temporalmente
                    const button = event.target;
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.classList.remove('btn-outline-primary');
                    button.classList.add('btn-success');

                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-outline-primary');
                    }, 2000);

                } catch (err) {
                    console.error('Error copiando token:', err);
                    alert('Error copiando el token');
                }
            }
            </script>
        </div>
    </div>
</x-app-layout>
