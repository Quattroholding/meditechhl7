<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles y Permisos - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #673ab7;
            --roles-color: #673ab7;
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
            background: linear-gradient(135deg, #673ab7 0%, #9575cd 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(103, 58, 183, 0.3);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            border-bottom: 2px solid #ede7f6;
            padding-bottom: 10px;
            color: var(--roles-color);
        }

        .step-card {
            background: linear-gradient(135deg, #f3e5f5 0%, #fff 100%);
            border-left: 4px solid var(--roles-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            background: #f3e5f5;
            border-left: 4px solid #673ab7;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .badge-admin { background: #fee2e2; color: #991b1b; }
        .badge-doctor { background: #dcfce7; color: #166534; }
        .badge-assistant { background: #dbeafe; color: #1e40af; }
        .badge-receptionist { background: #fef9c3; color: #854d0e; }
        .badge-accounting { background: #f3e8ff; color: #6b21a8; }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--roles-color);
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
    @include('help.sidebar', ['active' => 'roles'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Roles y Permisos</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-user-shield me-3"></i>Roles y Permisos</h1>
            <p>Gestiona los niveles de acceso y responsabilidades de cada usuario en tu sistema.</p>
        </header>

        <div class="content-section">
            <h2>Conceptos Básicos</h2>
            <p>El sistema SAMI utiliza un modelo de control de acceso basado en roles (RBAC). Esto significa que los permisos no se asignan directamente a los usuarios, sino a roles, y luego los usuarios se vinculan a uno o más roles.</p>
            
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5><i class="fas fa-id-badge text-primary me-2"></i>¿Qué es un Rol?</h5>
                            <p class="small mb-0">Un rol es una etiqueta que define un conjunto de responsabilidades. Ejemplo: "Doctor", "Asistente", "Contador".</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5><i class="fas fa-key text-success me-2"></i>¿Qué es un Permiso?</h5>
                            <p class="small mb-0">Un permiso es la capacidad técnica de realizar una acción específica. Ejemplo: "Crear Cita", "Ver Facturas".</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Roles Predeterminados</h2>
            <p>SAMI incluye varios roles preconfigurados para facilitar la puesta en marcha:</p>
            
            <div class="list-group mb-4">
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-admin">Admin Client</span>
                    <p class="small mb-0 text-muted">Acceso total a la configuración de la cuenta, sucursales y gestión de usuarios.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-doctor">Doctor</span>
                    <p class="small mb-0 text-muted">Gestión de su agenda, atención de consultas y acceso a historias clínicas de sus pacientes.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-receptionist">Recepcionista</span>
                    <p class="small mb-0 text-muted">Agendamiento de citas, registro de nuevos pacientes y cobros básicos.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-assistant">Asistente Médico</span>
                    <p class="small mb-0 text-muted">Preparación de pacientes (signos vitales) y soporte en la agenda del doctor.</p>
                </div>
                <div class="list-group-item border-0">
                    <span class="role-badge badge-accounting">Contabilidad</span>
                    <p class="small mb-0 text-muted">Acceso exclusivo a facturación, reportes financieros y estados de pago.</p>
                </div>
            </div>
        </div>

        <!--<div class="content-section">
            <h2>Gestión de Roles Personalizados</h2>
            <p>Puedes crear roles nuevos para adaptar el sistema a las necesidades específicas de tu centro médico.</p>
            
            <div class="step-card">
                <div class="step-title">1. Crear un Nuevo Rol</div>
                <div class="step-content">
                    <p>Dirígete a <strong>Configuraciones > Roles y Permisos</strong>. Haz clic en "Nuevo Rol", asígnale un nombre descriptivo y selecciona los permisos deseados agrupados por módulos.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">2. Asignar Permisos por Módulo</div>
                <div class="step-content">
                    <p>Los permisos están organizados por áreas de trabajo (Citas, Pacientes, Facturas, etc.). Puedes usar el botón "Seleccionar todos" de un módulo si el rol debe tener control total sobre esa sección.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">3. Vincular Usuario a Rol</div>
                <div class="step-content">
                    <p>Una vez creado el rol, ve a la sección de <strong>Usuarios</strong>, selecciona un usuario y en su perfil asigna el rol que acabas de crear. Los cambios se aplicarán en su próximo inicio de sesión.</p>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title"><strong><i class="fas fa-shield-alt"></i> Recomendación de Seguridad</strong></div>
            <p class="mb-0">Aplica el "Principio de Menor Privilegio": otorga a los usuarios solo los permisos estrictamente necesarios para realizar su trabajo. Esto protege la privacidad de los datos de tus pacientes.</p>
        </div>-->

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
