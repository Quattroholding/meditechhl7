<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación 2FA - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
    </style>
</head>
<body>
    @include('help.sidebar', ['active' => '2fa'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Autenticación de Dos Factores (2FA)</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-shield-alt me-3"></i>Autenticación de Dos Factores (2FA)</h1>
            <p>Añade una capa extra de seguridad a tu cuenta SAMI para proteger tus datos y los de tus pacientes.</p>
        </header>

        <div class="content-section">
            <h2>¿Qué es la Autenticación 2FA?</h2>
            <p>La autenticación de dos factores (2FA) es un proceso de seguridad <strong>obligatorio</strong> en SAMI que requiere que los usuarios proporcionen dos formas diferentes de identificación para acceder a su cuenta. Además de tu contraseña, ahora es necesario un código generado en tu dispositivo móvil para garantizar la máxima protección de la información médica.</p>
            
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
            <h2>Guía de Configuración</h2>

            <!-- Paso 1 (Anteriormente Paso 3) -->
            <div class="step-card">
                <h5><span class="step-number">1</span> Escaneo de Código QR</h5>
                <p>Al iniciar sesión, el sistema te mostrará automáticamente un código QR. Abre tu aplicación de autenticación favorita (Google Authenticator, Microsoft Authenticator o Authy) en tu teléfono y selecciona "Añadir cuenta" o "Escanear código QR". Enfoca la cámara de tu teléfono hacia el código que aparece en la pantalla.</p>
                    <div>
                         <img src="{{ asset('images/tutorial/2fa/2fa-login.png') }}" alt="" style="width: 100%;">
                    </div>
            </div>

            <!-- Paso 2 (Anteriormente Paso 4) -->
            <div class="step-card">
                <h5><span class="step-number">2</span> Confirmación de Vínculo</h5>
                <p>Una vez escaneado, tu aplicación móvil generará un código de 6 dígitos que cambia cada 30 segundos. Ingresa ese código en el campo de confirmación en la pantalla de SAMI para completar la vinculación y acceder al sistema.</p>
                    <div>
                         <img src="{{ asset('images/tutorial/2fa/2fa-authqr.png') }}" alt="" style="width: 100%;">
                    </div>
            </div>

            <!-- Paso 3 (Anteriormente Paso 5) -->
            <div class="step-card">
                <h5><span class="step-number">3</span> Guardar Códigos de Recuperación</h5>
                <p><strong>¡Muy importante!</strong> Tras la confirmación, el sistema te mostrará una lista de códigos de recuperación. Descárgalos o cópialos en un lugar seguro. Estos códigos te permitirán acceder a tu cuenta si alguna vez pierdes acceso a tu teléfono.</p>
                    <div>
                         <img src="{{ asset('images/tutorial/2fa/2fa-logged.png') }}" alt="" style="width: 100%;">
                    </div>
                <div class="info-box border-warning" style="background: #fff3e0; border-left-color: #ff9800;">
                    <p class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i><strong>Atención:</strong> Como el 2FA es obligatorio, si pierdes tu dispositivo y no tienes estos códigos, no podrás acceder a SAMI hasta que un administrador restablezca tu acceso.</p>
                </div>
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
            <a href="{{ route('help.profile') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Volver a Mi Perfil
            </a>
            <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
                Inicio <i class="fas fa-home ms-2"></i>
            </a>
        </div>
    </main>

    <button class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btn = document.getElementById('backToTop');
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        };
        btn.onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        };
    </script>
</body>
</html>
