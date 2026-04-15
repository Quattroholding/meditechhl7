@extends('layouts.public')

@section('content')
<style>
    .success-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 50px 40px;
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
    }

    .success-icon {
        font-size: 5rem;
        color: #27ae60;
        margin-bottom: 20px;
        animation: checkmark 0.6s ease-in-out;
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .success-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .success-message {
        font-size: 1.1rem;
        color: #7f8c8d;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #3498db;
        padding: 20px;
        border-radius: 8px;
        text-align: left;
        margin-bottom: 30px;
    }

    .info-box h6 {
        color: #3498db;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .info-box p {
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .info-box .label {
        font-weight: 600;
        color: #34495e;
    }

    .btn-login {
        background: #3498db;
        border: none;
        padding: 12px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
        text-decoration: none;
        color: white;
        display: inline-block;
    }

    .btn-login:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        color: white;
    }

    .instructions {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-top: 25px;
    }

    .instructions h6 {
        color: #856404;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .instructions ul {
        text-align: left;
        color: #856404;
        margin-bottom: 0;
        padding-left: 20px;
    }

    .instructions li {
        margin-bottom: 8px;
    }
</style>

<div class="success-card">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>

    <h1 class="success-title">¡Registro Exitoso!</h1>

    <p class="success-message">
        Tu registro en <strong>{{ $clientName }}</strong> ha sido completado exitosamente.
        Hemos enviado tus credenciales de acceso a tu correo electrónico.
    </p>

    <div class="info-box">
        <h6><i class="fas fa-envelope me-2"></i>Credenciales Enviadas</h6>
        <p>
            <span class="label">Correo:</span> {{ $email }}
        </p>
        <p>
            <span class="label">Contraseña:</span> Se ha enviado a tu correo electrónico
        </p>
        <p class="mb-0">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Por favor revisa tu bandeja de entrada y spam. El correo puede tardar unos minutos en llegar.
            </small>
        </p>
    </div>

    <a href="{{ route('login') }}" class="btn btn-login">
        <i class="fas fa-sign-in-alt me-2"></i>Ir al Login
    </a>

    <div class="instructions">
        <h6><i class="fas fa-lightbulb me-2"></i>Próximos Pasos</h6>
        <ul>
            <li>Revisa tu correo electrónico para obtener tu contraseña temporal</li>
            <li>Inicia sesión con tu correo y la contraseña recibida</li>
            <li>Te recomendamos cambiar tu contraseña en tu primer inicio de sesión</li>
            <li>Completa tu perfil con información adicional si es necesario</li>
            <li>¡Ya puedes agendar citas y acceder a tus registros médicos!</li>
        </ul>
    </div>

    <div class="mt-4">
        <p class="text-muted mb-0">
            <small>
                ¿Problemas con tu registro? Contacta a <strong>{{ $clientName }}</strong> para asistencia.
            </small>
        </p>
    </div>
</div>
@endsection
