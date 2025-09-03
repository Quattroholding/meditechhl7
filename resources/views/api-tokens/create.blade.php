<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    APi tokens
                @endslot
                @slot('li_1')
                        Crear Token API
                @endslot
            @endcomponent
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-plus"></i> Crear Token API
                        </h1>
                        <p class="text-muted mb-0">Genera un nuevo token de acceso para la API</p>
                    </div>
                    <a href="{{ route('api-tokens.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form action="{{ route('api-tokens.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-tag"></i> Nombre del Token <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="Ej: Sistema Hospital XYZ"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Un nombre descriptivo para identificar este token
                                        </small>
                                    </div>

                                    <div class="mb-4">
                                        <label for="description" class="form-label">
                                            <i class="fas fa-info-circle"></i> Descripción
                                        </label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description"
                                                  name="description"
                                                  rows="3"
                                                  placeholder="Descripción del propósito de este token...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="allowed_ips" class="form-label">
                                            <i class="fas fa-network-wired"></i> IPs Permitidas <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('allowed_ips') is-invalid @enderror"
                                               id="allowed_ips"
                                               name="allowed_ips"
                                               value="{{ old('allowed_ips', '*') }}"
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
                                               value="{{ old('scopes', '*') }}"
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
                                               value="{{ old('expires_at') }}"
                                               min="{{ now()->addDay()->format('Y-m-d') }}">
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Opcional. Deja vacío para que nunca expire
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('api-tokens.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Generar Token
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Ejemplos de IPs -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-network-wired"></i> Ejemplos de IPs</h6>
                            </div>
                            <div class="card-body">
                                <dl class="small">
                                    <dt>Todas las IPs:</dt>
                                    <dd><code>*</code></dd>

                                    <dt>IP específica:</dt>
                                    <dd><code>192.168.1.100</code></dd>

                                    <dt>Múltiples IPs:</dt>
                                    <dd><code>10.0.0.1, 10.0.0.2</code></dd>

                                    <dt>Rango CIDR:</dt>
                                    <dd><code>192.168.1.0/24</code></dd>

                                    <dt>Mixto:</dt>
                                    <dd><code>192.168.1.100, 10.0.0.0/24</code></dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Ejemplos de Permisos -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Ejemplos de Permisos</h6>
                            </div>
                            <div class="card-body">
                                <dl class="small">
                                    <dt>Acceso total:</dt>
                                    <dd><code>*</code></dd>

                                    <dt>Solo lectura:</dt>
                                    <dd><code>read:*</code></dd>

                                    <dt>Solo escritura:</dt>
                                    <dd><code>write:*</code></dd>

                                    <dt>Recurso específico:</dt>
                                    <dd><code>read:appointments, write:appointments</code></dd>

                                    <dt>Mixto:</dt>
                                    <dd><code>read:*, write:appointments</code></dd>
                                </dl>
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
