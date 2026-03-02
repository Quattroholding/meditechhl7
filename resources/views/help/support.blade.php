<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte y Contacto - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --support-color: #198754;
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
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            border-bottom: 2px solid #e9f7ef;
            padding-bottom: 10px;
            color: var(--support-color);
        }

        .contact-card {
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
            height: 100%;
            background: white;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: var(--support-color);
        }

        .contact-icon {
            font-size: 2.5rem;
            color: var(--support-color);
            margin-bottom: 15px;
        }

        .step-card {
            background: linear-gradient(135deg, #f1f8f4 0%, #fff 100%);
            border-left: 4px solid var(--support-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #e9f7ef 0%, #d1e7dd 100%);
            border: 2px dashed var(--support-color);
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
            color: var(--support-color);
            margin-bottom: 15px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--support-color);
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
    @include('help.sidebar', ['active' => 'support'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Soporte y Contacto</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-headset me-3"></i>Soporte y Contacto</h1>
            <p>¿Necesitas ayuda adicional? Nuestro equipo técnico está aquí para asistirte.</p>
        </header>

        <div class="content-section">
            <h2>Canales de Atención</h2>
            <p>Elige el medio que prefieras para comunicarte con nosotros. Estamos disponibles de Lunes a Viernes de 8:00 AM a 5:00 PM.</p>
            
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <i class="fab fa-whatsapp contact-icon" style="color: #25d366;"></i>
                        <h4>WhatsApp</h4>
                        <p>Atención rápida para dudas puntuales y emergencias menores.</p>
                        <a href="#" class="btn btn-success btn-sm">Chatea ahora</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <i class="fas fa-envelope contact-icon" style="color: #ea4335;"></i>
                        <h4>Correo Electrónico</h4>
                        <p>Para solicitudes detalladas, reportes y asuntos administrativos.</p>
                        <a href="mailto:soporte@meditech.com" class="btn btn-danger btn-sm">Enviar Email</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <i class="fas fa-phone-alt contact-icon" style="color: #34a853;"></i>
                        <h4>Llamada Técnica</h4>
                        <p>Asistencia directa y guiada por nuestros especialistas.</p>
                        <a href="tel:+123456789" class="btn btn-success btn-sm">Llamar</a>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="content-section">
            <h2>Reportar un Problema</h2>
            <p>Si encuentras algo que no funciona como debería, por favor repórtalo para que podamos solucionarlo.</p>
            
            <div class="step-card">
                <div class="step-title">Cómo reportar un Bug</div>
                <div class="step-content">
                    <ol>
                        <li>Describe los pasos para reproducir el error.</li>
                        <li>Adjunta capturas de pantalla si es posible.</li>
                        <li>Indica qué esperabas que sucediera y qué sucedió realmente.</li>
                    </ol>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-bug"></i>
                    <p>Captura: Formulario de Soporte / Reporte</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Asistencia Remota</h2>
            <p>En casos complejos, nuestro equipo puede solicitar acceso remoto a tu equipo para solucionar el inconveniente en vivo.</p>
            
            <div class="step-card">
                <div class="step-title">Preparación para Soporte Remoto</div>
                <div class="step-content">
                    <p>Utilizamos herramientas seguras de conexión remota. Asegúrate de tener instalado el software autorizado por SAMI antes de la cita técnica.</p>
                </div>
            </div>
        </div>-->

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.profile') }}" class="btn btn-outline-success btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Perfil de Usuario
            </a>
            <a href="{{ route('help.index') }}" class="btn btn-success btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
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
