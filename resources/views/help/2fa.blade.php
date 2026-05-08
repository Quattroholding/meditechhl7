@extends('help.layout')
@section('style')
    :root {
    --primary-color: #0d6efd;
    --security-color: #1a237e;
    --light-bg: #f8f9fa;
    --dark-text: #212529;
    }

    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    color: var(--dark-text);
    }

    .help-content {
    margin-left: 280px;
    padding: 30px;
    min-height: 100vh;
    }

    .help-breadcrumb {
    background: #fff;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .help-header {
    background: linear-gradient(135deg, #1a237e 0%, #3f51b5 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
    }

    .content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .content-section h2 {
    border-bottom: 2px solid #e8eaf6;
    padding-bottom: 10px;
    color: var(--security-color);
    }

    .step-card {
    background: linear-gradient(135deg, #f5f5f5 0%, #fff 100%);
    border-left: 4px solid var(--security-color);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
    }

    .step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--security-color);
    color: white;
    border-radius: 50%;
    margin-right: 10px;
    font-weight: bold;
    }

    .screenshot-placeholder {
    background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
    border: 2px dashed var(--security-color);
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 20px 0;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    }

    .screenshot-placeholder i {
    font-size: 3rem;
    color: var(--security-color);
    margin-bottom: 15px;
    }

    .info-box {
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    }

    .back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: var(--security-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
    }

    .back-to-top.visible {
    opacity: 1;
    visibility: visible;
    }

    @media (max-width: 992px) {
    .help-content { margin-left: 0; }
    }

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => '2fa'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Autenticación de Dos Factores (2FA)</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <header class="help-header">
        <h1><i class="fas fa-shield-alt me-3"></i>Autenticación de Dos Factores (2FA)</h1>
        <p>Añade una capa extra de seguridad a tu cuenta SAMI para proteger tus datos y los de tus pacientes.</p>
    </header>
@stop
@section('table-content')
    <div class="content-section">
        <h2>¿Qué es la Autenticación 2FA?</h2>
        <p>La autenticación de dos factores (2FA) es un proceso de seguridad <strong>obligatorio</strong> en SAMI que requiere que los usuarios proporcionen dos formas diferentes de identificación para acceder a su cuenta. Además de tu contraseña, ahora es necesario un código generado en tu dispositivo móvil para garantizar la máxima protección de la información médica.</p>

        <div class="info-box border-primary" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <div class="d-flex">
                <i class="fas fa-mobile-alt fa-2x me-3 text-primary"></i>
                <div>
                    <strong>Requisito Previo</strong>
                    <p class="mb-0">Antes de comenzar, debes descargar en tu teléfono móvil una aplicación de autenticación (como <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong> o <strong>Authy</strong>) disponible en App Store o Google Play.</p>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="d-flex">
                <i class="fas fa-user-lock fa-2x me-3 text-primary"></i>
                <div>
                    <strong>Seguridad Obligatoria</strong>
                    <p class="mb-0">Para cumplir con los estándares de protección de datos sensibles, el sistema te solicitará configurar el 2FA automáticamente en tu próximo inicio de sesión si aún no lo has hecho.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2>Guía de Configuración Paso a Paso</h2>

        <!-- Paso 1 -->
        <div class="step-card">
            <h5><span class="step-number">1</span> Inicio de Sesión</h5>
            <p>Inicie sesión en SAMI utilizando sus credenciales registradas (usuario y contraseña).</p>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-clogin.png') }}" alt="Escaneo QR" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 2 -->
        <div class="step-card">
            <h5><span class="step-number">2</span> Mensaje de Configuración</h5>
            <p>El sistema mostrará automáticamente un mensaje de configuración de autenticación de dos factores que describe los beneficios de esta capa extra de seguridad. Para continuar, el sistema le solicitará ingresar su <strong>contraseña actual</strong>.</p>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-login1.png') }}" alt="Escaneo QR" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 3 -->
        <div class="step-card">
            <h5><span class="step-number">3</span> Activar 2FA</h5>
            <p>Una vez ingresada su contraseña, haga clic en el botón <strong>“Activar 2FA”</strong>.</p>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-login2.png') }}" alt="Escaneo QR" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 4 -->
        <div class="step-card">
            <h5><span class="step-number">4</span> Escaneo de Código QR</h5>
            <p>El sistema le mostrará un código QR en pantalla. Abra la aplicación de autenticación en su teléfono y escanee este código QR para vincular su cuenta de SAMI.</p>
            <div class="info-box border-info mb-3" style="background: #e1f5fe; border-left-color: #0288d1; padding: 15px;">
                <p class="mb-0 small"><i class="fas fa-info-circle me-2 text-info"></i><strong>Nota:</strong> El escaneo del código QR se realiza <strong>solo la primera vez</strong> que configura el sistema.</p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-authqr.png') }}" alt="Escaneo QR" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 5 -->
        <div class="step-card">
            <h5><span class="step-number">5</span> Código de Verificación</h5>
            <p>Una vez escaneado, la aplicación en su móvil le mostrará un código de 6 dígitos. Ingrese este código en el sistema SAMI, en el campo denominado <strong>“Código de verificación”</strong>.</p>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-gauth.png') }}" alt="Confirmación de código" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 6 -->
        <div class="step-card">
            <h5><span class="step-number">6</span> Confirmar</h5>
            <p>Haga clic en el botón <strong>“Confirmar”</strong> para validar el vínculo entre su teléfono y su cuenta.</p>
            <div class="text-center">
                <img src="{{ asset('images/tutorial/2fa/2fa-code.png') }}" alt="Confirmación de código" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 7 -->
        <div class="step-card">
            <h5><span class="step-number">7</span> Códigos de Recuperación</h5>
            <p>Seguidamente, el sistema le mostrará <strong>8 códigos de recuperación</strong>. Estos códigos son vitales en caso de que pierda acceso a su teléfono o aplicación de autenticación. Descárguelos utilizando el botón <strong>“Descargar códigos”</strong> y guárdelos en un lugar seguro.</p>
            <div class="text-center mb-3">
                <img src="{{ asset('images/tutorial/2fa/2fa-logged.png') }}" alt="Códigos de recuperación" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <!-- Paso 8 -->
        <div class="step-card">
            <h5><span class="step-number">8</span> Finalización</h5>
            <p>Para terminar el proceso, haga clic en el botón <strong>“Ya los guardé”</strong>. Con este paso, su cuenta ya habrá quedado correctamente autenticada.</p>
            <div class="text-center mb-3">
                <img src="{{ asset('images/tutorial/2fa/2fa-logged2.png') }}" alt="Códigos de recuperación" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>

        <div class="text-center">
            <img src="{{ asset('images/tutorial/2fa/2fa-newloggin.png') }}" alt="Escaneo QR" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        </div>
        <div class="info-box border-warning" style="background: #fffef2; border-left: 4px solid #fbc02d; padding: 20px; border-radius: 10px;">
            <p class="mb-0"><strong><i class="fas fa-sign-in-alt me-2 text-warning"></i>Nota sobre futuros ingresos:</strong></p>
            <p class="mb-0 mt-2">En los siguientes ingresos a SAMI, debe proporcionar sus credenciales registradas. Al hacer clic en <strong>“Ingresar”</strong>, el sistema le mostrará un campo de <strong>VERIFICACION 2FA</strong> donde debe ingresar el código de 6 dígitos que le genere en ese momento su aplicación de autenticación móvil. Finalmente, haga clic en <strong>“Verificar”</strong> para acceder a la pantalla principal de su cuenta.</p>
        </div>
    </div>

    <div class="content-section">
        <h2>Preguntas Frecuentes</h2>
        <div class="accordion" id="faq2FA">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        ¿Qué pasa si pierdo mi teléfono?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faq2FA">
                    <div class="accordion-body">
                        Deberás usar uno de tus códigos de recuperación guardados en el paso 3. Si tampoco tienes los códigos, deberás contactar a soporte técnico de tu institución o al administrador de SAMI.
                    </div>
                </div>
            </div>
            <!--<div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ¿Puedo desactivar el 2FA después de activarlo?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faq2FA">
                    <div class="accordion-body">
                        Sí, puedes desactivarlo desde la misma sección de seguridad, aunque no es recomendable ya que reduce la protección de tu cuenta.
                    </div>
                </div>
            </div>-->
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.registration') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Volver a Registro
        </a>
        <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
            Inicio <i class="fas fa-home ms-2"></i>
        </a>
    </div>
@stop
