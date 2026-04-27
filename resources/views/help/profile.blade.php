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

        <!-- 1. Encabezado de Perfil -->
        <div class="content-section">
            <h2>Encabezado de Perfil</h2>
            <p>El cintillo azul en la parte superior muestra un resumen rápido de tu identidad y actividad en la plataforma.</p>
            <div class="step-card">
                <div class="step-content">
                    <ul>
                        <li><strong>Información Personal:</strong> Muestra tu nombre completo, tipo y número de documento, correo electrónico y teléfono.</li>
                        <li><strong>Estadísticas:</strong> Indica el total de citas agendadas y consultas finalizadas a la fecha.</li>
                        <li><strong>Agregar Especialidad:</strong> Botón directo para gestionar tus cualificaciones médicas.</li>
                    </ul>
                </div>
                    <div>
                         <img src="{{ asset('images/tutorial/profile/profile_header.png') }}" alt="" style="width: 100%;">
                    </div>
            </div>
        </div>

        <div class="row">
            <!-- COLUMNA IZQUIERDA: Acerca de mí y Especialidades -->
            <div class="col-lg-4">
                <!-- 2. Acerca de mí -->
                <div class="content-section">
                    <h2>Acerca de mí</h2>
                    <p>Información demográfica y de registro profesional.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <ul class="mb-0">
                                <li><strong>Género y Nacimiento:</strong> Datos básicos de identificación.</li>
                                <li><strong>Registro y Licencia:</strong> Tus códigos de identificación profesional.</li>
                                <li><strong>Último ingreso:</strong> Registro de tu última sesión.</li>
                            </ul>
                        </div>
                    <div>
                         <img src="{{ asset('images/tutorial/profile/profile_aboutme.png') }}" alt="" style="width: 100%;">
                    </div>
                    </div>
                </div>

                <!-- 3. Especialidades -->
                <div class="content-section">
                    <h2>Especialidades</h2>
                    <p>Gestión de tus áreas de especialización médica.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <ul class="mb-0">
                                <li><strong>Listado:</strong> Visualiza tus especialidades y su periodo de vigencia.</li>
                                <li><strong>Especialidad Principal:</strong> Elige cuál aparecerá destacada en las búsquedas de pacientes.</li>
                            </ul>
                        </div>
                    <div>
                         <img src="{{ asset('images/tutorial/profile/profile_specialities.png') }}" alt="" style="width: 100%;">
                    </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Configuración de Perfil y Acceso -->
            <div class="col-lg-8">
                <!-- 4. Configuración de Perfil -->
                <div class="content-section">
                    <h2>Configuración de Perfil</h2>
                    <p>Actualiza tus datos básicos que aparecen en los documentos generados.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <p>Modifica tu nombre, teléfono y dirección. Asegúrate de mantener estos datos actualizados.</p>
                            <p class="text-primary"><strong><i class="fas fa-save"></i> Importante:</strong> Debes hacer clic en <strong>“ACTUALIZAR”</strong> para guardar los cambios.</p>
                        </div>
                        <div>
                            <img src="{{ asset('images/tutorial/profile/profile_data.png') }}" alt="Datos de perfil" class="img-fluid rounded border shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- 5. Configuración de acceso -->
                <div class="content-section">
                    <h2>Configuración de acceso</h2>
                    <p>Cambia tu contraseña periódicamente para mantener tu cuenta segura.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <p>Introduce tu clave actual y la nueva contraseña (dos veces).</p>
                            <div class="info-box py-2 my-2">
                                <p class="mb-0 small"><i class="fas fa-shield-alt me-2"></i><strong>Requisito:</strong> Mínimo <strong>8 caracteres</strong>.</p>
                            </div>
                            <p class="text-primary"><strong><i class="fas fa-save"></i> Importante:</strong> Haz clic en <strong>“ACTUALIZAR”</strong> al finalizar.</p>
                        </div>
                        <div>
                            <img src="{{ asset('images/tutorial/profile/profile_pw.png') }}" alt="Seguridad" class="img-fluid rounded border shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 6. Firma y Sello Digital -->
            <div class="col-lg-7">
                <div class="content-section h-100">
                    <h2>Firma y Sello Digital</h2>
                    <p>Esencial para la validez legal de recetas, incapacidades e <strong>HISTORIA CLÍNICA</strong>.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <p>Sube imágenes (JPG, PNG, GIF, SVG) de hasta 2MB.</p>
                            <ul>
                                <li><strong>Carga:</strong> Haz clic en <strong>“Subir Firma”</strong> o <strong>“Subir Sello”</strong> tras elegir el archivo.</li>
                                <li><strong>Gestión:</strong> Usa <strong>"Eliminar"</strong> para quitar o selecciona un nuevo archivo para <strong>"Reemplazar"</strong>.</li>
                            </ul>
                        </div>
                        <div>
                            <img src="{{ asset('images/tutorial/profile/profile_sign.png') }}" alt="Firma y sello" class="img-fluid rounded border shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Código de Referidos -->
            <div class="col-lg-5">
                <div class="content-section h-100">
                    <h2>Tu Código de Referido</h2>
                    <p>Gana beneficios invitando a otros colegas a unirse a SAMI.</p>
                    <div class="step-card">
                        <div class="step-content">
                            <p>Gana crédito en tu próxima factura por cada nuevo cliente registrado con tu código.</p>
                            <ul>
                                <li><strong>Código y Enlace:</strong> Copia y comparte tu identificador único.</li>
                                <li><strong>QR Imprimible:</strong> Descarga un PDF con código QR para tu consultorio.</li>
                                <li><strong>Seguimiento:</strong> Revisa tu total de referidos exitosos.</li>
                            </ul>
                        </div>
                    <div>
                         <img src="{{ asset('images/tutorial/profile/profile_referralcode.png') }}" alt="" style="width: 100%;">
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title"><strong><i class="fas fa-lightbulb"></i> Tip de Seguridad</strong></div>
            <p class="mb-0">Nunca compartas tus credenciales de acceso. SAMI registra todas las acciones por usuario para garantizar la trazabilidad clínica y administrativa.</p>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.settings') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Configuraciones
            </a>
            <a href="{{ route('help.subscriptions') }}" class="btn btn-primary btn-lg">
                Suscripciones <i class="fas fa-arrow-right ms-2"></i>
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
