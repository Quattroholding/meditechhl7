<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Seguridad') }}
                @endslot
                @slot('li_1')
                    {{ __('Cambiar Contraseña') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Cambiar Contraseña - Primer Acceso</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Bienvenido al Sistema Meditech</strong><br>
                        Por seguridad, debe cambiar su contraseña temporal antes de continuar.
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('first-login.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Temporal</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                La contraseña debe tener al menos 8 caracteres.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-warning">
                                <strong>Recomendaciones de seguridad:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Use al menos 8 caracteres</li>
                                    <li>Incluya letras mayúsculas y minúsculas</li>
                                    <li>Incluya números y símbolos</li>
                                    <li>No use información personal</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Cambiar Contraseña y Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</x-app-layout>
