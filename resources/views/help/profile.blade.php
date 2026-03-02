<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --profile-color: #0288d1;
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
            background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(2, 136, 209, 0.3);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 10px;
            color: var(--profile-color);
        }

        .step-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
            border-left: 4px solid var(--profile-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
            border: 2px dashed var(--profile-color);
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
            color: var(--profile-color);
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
            background: var(--profile-color);
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
    @include('help.sidebar', ['active' => 'profile'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Perfil de Usuario</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-user-circle me-3"></i>Perfil de Usuario</h1>
            <p>Gestiona tu información personal, seguridad y preferencias de SAMI.</p>
        </header>

        <div class="content-section">
            <h2>Información Personal</h2>
            <p>En esta sección puedes actualizar tus datos básicos que aparecen en el sistema y en los documentos generados.</p>
            
            <div class="step-card">
                <div class="step-title">Actualizar Datos</div>
                <div class="step-content">
                    <p>Puedes modificar tu nombre, correo electrónico, teléfono y dirección. Asegúrate de que esta información esté siempre al día ya que es la que se utiliza en las cabeceras de tus documentos médicos.</p>
                </div>
                <div>
                    <img src="{{ asset('images/tutorial/profile/profile_data.png') }}" alt="" style="width: 100%;">
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Seguridad y Contraseña</h2>
            <p>Mantén tu cuenta segura cambiando tu contraseña periódicamente.</p>
            
            <div class="step-card">
                <div class="step-title">Cambio de Contraseña</div>
                <div class="step-content">
                    <p>Para cambiar tu contraseña, introduce tu clave actual y luego la nueva contraseña dos veces para confirmar. Recuerda usar una combinación segura de letras, números y símbolos.</p>
                </div>
                <div>
                    <img src="{{ asset('images/tutorial/profile/profile_pw.png') }}" alt="" style="width: 100%;">
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Firma Digital</h2>
            <p>Tu firma es esencial para la validez legal de las recetas, incapacidades y órdenes médicas.</p>
            
            <div class="step-card">
                <div class="step-title">Gestión de Firma</div>
                <div class="step-content">
                    <p>Puedes subir una imagen de tu firma escaneada o utilizar la herramienta de dibujo para crear una firma digital directamente en el sistema.</p>
                </div>
                <div>
                    <img src="{{ asset('images/tutorial/profile/profile_sign.png') }}" alt="" style="width: 100%;">
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title"><strong><i class="fas fa-lightbulb"></i> Tip de Seguridad</strong></div>
            <p class="mb-0">Nunca compartas tus credenciales de acceso. SAMI registra todas las acciones por usuario para garantizar la trazabilidad clínica y administrativa.</p>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
            <a href="{{ route('help.support') }}" class="btn btn-primary btn-lg">
                Soporte y Contacto <i class="fas fa-arrow-right ms-2"></i>
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
