<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    APi tokens
                @endslot
                @slot('li_1')
                        Editar Token API -  {{$apiToken->name}}
                @endslot
            @endcomponent

            <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit"></i> Editar Token API
            </h1>
            <p class="text-muted mb-0">Actualizar configuración del token "{{ $apiToken->name }}"</p>
        </div>
        <div>
            <a href="{{ route('api-tokens.show', $apiToken) }}" class="btn btn-outline-info">
                <i class="fas fa-eye"></i> Ver
            </a>
            <a href="{{ route('api-tokens.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('api-tokens.update', $apiToken) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i> Nombre del Token <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $apiToken->name) }}"
                                   placeholder="Ej: Sistema Hospital XYZ"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-info-circle"></i> Descripción
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Descripción del propósito de este token...">{{ old('description', $apiToken->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="practitioner_id" class="form-label">
                                <i class="fas fa-user-md"></i> Practitioner (Opcional)
                            </label>
                            <select class="form-control select2 @error('practitioner_id') is-invalid @enderror"
                                    id="practitioner_id"
                                    name="practitioner_id">
                                <option value="">Ninguno (Token genérico)</option>
                                @foreach(\App\Models\Practitioner::withoutGlobalScopes()->orderBy('name')->get() as $practitioner)
                                    <option value="{{ $practitioner->id }}" {{ old('practitioner_id', $apiToken->practitioner_id) == $practitioner->id ? 'selected' : '' }}>
                                        {{ $practitioner->name }} - {{ $practitioner->identifier }}
                                    </option>
                                @endforeach
                            </select>
                            @error('practitioner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Asociar este token a un practitioner específico (útil para integraciones como HemoScreen)
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="allowed_ips" class="form-label">
                                <i class="fas fa-network-wired"></i> IPs Permitidas <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('allowed_ips') is-invalid @enderror"
                                   id="allowed_ips"
                                   name="allowed_ips"
                                   value="{{ old('allowed_ips', implode(', ', $apiToken->allowed_ips)) }}"
                                   placeholder="*, 192.168.1.100, 10.0.0.0/24"
                                   required>
                            @error('allowed_ips')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Separar múltiples IPs con comas. Usar * para todas las IPs, rangos CIDR son soportados
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="scopes" class="form-label">
                                <i class="fas fa-shield-alt"></i> Permisos (Scopes) <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('scopes') is-invalid @enderror"
                                   id="scopes"
                                   name="scopes"
                                   value="{{ old('scopes', implode(', ', $apiToken->scopes)) }}"
                                   placeholder="*, read:*, read:appointments, write:practitioners"
                                   required>
                            @error('scopes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Separar múltiples permisos con comas. Usar * para acceso total
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="expires_at" class="form-label">
                                <i class="fas fa-calendar-alt"></i> Fecha de Expiración
                            </label>
                            <input type="date"
                                   class="form-control @error('expires_at') is-invalid @enderror"
                                   id="expires_at"
                                   name="expires_at"
                                   value="{{ old('expires_at', $apiToken->expires_at?->format('Y-m-d')) }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Opcional. Deja vacío para que nunca expire
                            </small>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="1"
                                       id="active"
                                       name="active"
                                       {{ old('active', $apiToken->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    <i class="fas fa-power-off"></i> Token activo
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Los tokens inactivos no pueden ser utilizados para acceder a la API
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('api-tokens.show', $apiToken) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Token
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información Actual -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información Actual</h6>
                </div>
                <div class="card-body">
                    <dl class="small">
                        <dt>Estado:</dt>
                        <dd>
                            @if($apiToken->status === 'active')
                                <span class="badge bg-success">Activo</span>
                            @elseif($apiToken->status === 'expired')
                                <span class="badge bg-danger">Expirado</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </dd>

                        <dt>Creado:</dt>
                        <dd>{{ $apiToken->created_at->format('d/m/Y H:i') }}</dd>

                        <dt>Último uso:</dt>
                        <dd>
                            @if($apiToken->last_used_at)
                                {{ $apiToken->last_used_at->diffForHumans() }}
                            @else
                                <span class="text-muted">Nunca usado</span>
                            @endif
                        </dd>

                        <dt>Token ID:</dt>
                        <dd><code class="small">{{ substr($apiToken->token, 0, 10) }}...{{ substr($apiToken->token, -6) }}</code></dd>
                    </dl>
                </div>
            </div>

            <!-- Nota Importante -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Importante</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>El token actual no se modificará</li>
                        <li>Los cambios se aplicarán inmediatamente</li>
                        <li>Si desactivas el token, dejará de funcionar</li>
                        <li>Para generar un nuevo token, usa "Regenerar" en la vista de detalles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-formatear IPs mientras escribe
                const ipInput = document.getElementById('allowed_ips');
                ipInput.addEventListener('blur', function() {
                    // Limpiar espacios extra
                    this.value = this.value.split(',').map(ip => ip.trim()).join(', ');
                });

                // Auto-formatear scopes mientras escribe
                const scopeInput = document.getElementById('scopes');
                scopeInput.addEventListener('blur', function() {
                    // Limpiar espacios extra
                    this.value = this.value.split(',').map(scope => scope.trim()).join(', ');
                });
            });
            </script>
        </div>
    </div>
</x-app-layout>
