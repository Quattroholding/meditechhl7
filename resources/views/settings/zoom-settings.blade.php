@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-video me-2"></i>
                        Configuración de Cuenta Zoom
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5 class="mb-3">Estado de Conexión</h5>
                        @php
                            $zoomProfile = auth()->user()->practitioner->zoomProfile;
                        @endphp

                        @if ($zoomProfile && $zoomProfile->isConfigured())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Cuenta Conectada</strong>
                                <p class="mb-2 mt-2">
                                    <strong>Email Zoom:</strong> {{ $zoomProfile->zoom_email }}<br>
                                    <strong>User ID:</strong> <code>{{ $zoomProfile->zoom_user_id }}</code><br>
                                    <strong>Conectado desde:</strong> {{ $zoomProfile->verified_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <form action="{{ route('zoom.disconnect') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Desconectar tu cuenta de Zoom?')">
                                    <i class="fas fa-unlink me-2"></i>
                                    Desconectar Zoom
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Cuenta No Conectada</strong>
                                <p class="mb-0 mt-2">
                                    No tienes una cuenta de Zoom conectada. Debes conectar tu cuenta para poder crear citas virtuales.
                                </p>
                            </div>

                            <a href="{{ route('zoom.authorize') }}" class="btn btn-primary">
                                <i class="fab fa-zoom me-2"></i>
                                Conectar mi Cuenta Zoom
                            </a>
                        @endif
                    </div>

                    <hr>

                    <div>
                        <h5 class="mb-3">¿Cómo funciona?</h5>
                        <ol>
                            <li>
                                <strong>Conecta tu cuenta Zoom:</strong>
                                Haz clic en el botón de arriba para autorizar a SAMI a usar tu cuenta.
                            </li>
                            <li>
                                <strong>Crea citas virtuales:</strong>
                                Una vez conectada, podrás crear citas virtuales que se ejecutarán en tu cuenta de Zoom.
                            </li>
                            <li>
                                <strong>Gestiona tu capacidad:</strong>
                                Tus citas no entrarán en conflicto con otros doctores porque cada uno usa su propia cuenta.
                            </li>
                        </ol>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Debes tener una cuenta de Zoom activa (gratuita o paga).
                            Los costos de tu suscripción a Zoom son tu responsabilidad.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="mb-0">Información</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>¿Por qué necesito conectar mi Zoom?</strong>
                    </p>
                    <p class="small">
                        Para evitar conflictos cuando múltiples doctores crean citas virtuales simultáneamente,
                        cada doctor debe usar su propia cuenta de Zoom.
                    </p>

                    <hr>

                    <p>
                        <strong>¿Qué acceso tendrá SAMI?</strong>
                    </p>
                    <p class="small">
                        SAMI solo podrá crear reuniones en tu cuenta. No tendrá acceso a tus conversaciones
                        o grabaciones personales.
                    </p>

                    <hr>

                    <p>
                        <strong>¿Puedo desconectar en cualquier momento?</strong>
                    </p>
                    <p class="small">
                        Sí, puedes desconectar en cualquier momento. Sin embargo, no podrás crear nuevas
                        citas virtuales hasta que reconectes tu cuenta.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
