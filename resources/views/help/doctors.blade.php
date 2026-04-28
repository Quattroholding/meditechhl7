<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctores - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --doctor-color: #55acee;
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
            background: linear-gradient(135deg, #55acee 0%, #007bb5 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(85, 172, 238, 0.3);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            border-bottom: 2px solid #e3f2fd;
            padding-bottom: 10px;
            color: var(--doctor-color);
        }

        .step-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
            border-left: 4px solid var(--doctor-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px dashed var(--doctor-color);
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
            color: var(--doctor-color);
            margin-bottom: 15px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--doctor-color);
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
    @include('help.sidebar', ['active' => 'doctors'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Doctores</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-user-md me-3"></i>Módulo de Doctores</h1>
            <p>Gestión y administración del personal médico registrado en el sistema.</p>
        </header>

        <div class="content-section">
            <h2>Lista de Doctores</h2>
            <p>Este módulo permite visualizar y gestionar a todos los profesionales de la salud vinculados a tu organización.</p>
            
            <div class="step-card">
                <div class="step-title"><strong>Visualización de Personal</strong></div>
                <div class="step-content">
                    <p>En la tabla principal podrás ver:</p>
                    <ul>
                        <li>Nombre y Especialidad del doctor.</li>
                        <li>Información de contacto (correo y teléfono).</li>
                        <li>Sucursales y consultorios asignados.</li>
                        <li>Estado de actividad en el sistema.</li>
                    </ul>
                </div>
                    <div>
                         <img src="{{ asset('images/tutorial/practitioner/practitioner_list.png') }}" alt="" style="width: 100%;">
                    </div>
            </div>

            <div class="step-card">
                <div class="step-title"><strong>Acciones Administrativas</strong></div>
                <div class="step-content">
                    <p>Dependiendo de tus permisos, podrás realizar las siguientes acciones:</p>
                    <ul>
                        <li><strong>Ver Perfil:</strong> Accede al detalle completo del doctor, incluyendo su firma y sello.</li>
                        <li><strong>Editar:</strong> Modificar datos básicos, especialidades y asignaciones de sucursales.</li>
                        <li><strong>Firma y Sello:</strong> Gestionar los elementos necesarios para la validez de recetas y órdenes.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.consulting-rooms') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Consultorios
            </a>
            <a href="{{ route('help.patients') }}" class="btn btn-primary btn-lg">
                Pacientes <i class="fas fa-arrow-right ms-2"></i>
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
