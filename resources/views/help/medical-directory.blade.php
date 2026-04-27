<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio Médico - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --directory-color: #1a237e;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
        }

        html {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-text);
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .help-content {
            margin-left: 280px;
            padding: 30px;
            max-width: calc(100vw - 280px);
            overflow-x: hidden;
            box-sizing: border-box;
        }

        @media (max-width: 992px) {
            .help-content {
                margin-left: 0;
                max-width: 100%;
            }
        }

        .module-header {
            background: linear-gradient(135deg, var(--directory-color) 0%, #3949ab 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
            position: relative;
            overflow: hidden;
        }

        .module-header h1 {
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .module-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .help-breadcrumb {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            color: var(--directory-color);
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
        }

        .doctor-card-demo {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background: #f9f9f9;
            max-width: 400px;
            margin: 20px auto;
        }

        .info-box.tip {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--directory-color);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(26, 35, 126, 0.4);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 9999;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
            border: 2px dashed var(--directory-color);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: var(--directory-color);
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #1a237e;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: var(--directory-color);
        }
    </style>
</head>
<body>

    @include('help.sidebar', ['active' => 'medical-directory'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Directorio Médico</li>
            </ol>
        </nav>

        <div class="module-header">
            <h1><i class="fas fa-address-book me-3"></i>Directorio Médico</h1>
            <p>Encuentre información detallada sobre los profesionales de la salud registrados en el sistema y gestione sus citas.</p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>Información de la Tarjeta Médica</h2>
            <p>Cada médico en el directorio se presenta en una tarjeta que contiene la siguiente información detallada:</p>
            
            <div>
                <img src="{{ asset('images/tutorial/medical_directory/medd-list.png') }}" alt="" style="width: 100%;">
            </div>

            <ul>
                <li><strong>Nombre del Profesional:</strong> Nombre completo y título del médico.</li>
                <li><strong>ID / Identificación:</strong> Número de identificación profesional o registro médico.</li>
                <li><strong>Estado:</strong> Indica si el profesional está actualmente <span class="badge bg-success">Activo</span> para recibir citas.</li>
                <li><strong>Avatar / Foto:</strong> Imagen de perfil del profesional para facilitar su identificación.</li>
                <li><strong>Información de Contacto:</strong> Teléfono y correo electrónico (si el profesional ha autorizado su visualización).</li>
                <li><strong>Horario Laboral:</strong> Detalle de los días y horas de atención, organizado por sedes o sucursales.</li>
                <li><strong>Especialidades:</strong> Lista de especialidades médicas avaladas por el profesional.</li>
            </ul>

            <div class="info-box tip">
                <p class="mb-0"><i class="fas fa-magic me-2"></i><strong>Dato importante:</strong> Todos los doctores que se registran como clientes activos en la plataforma <strong>SAMI</strong> aparecen automáticamente en este directorio para ser consultados por los pacientes.</p>
            </div>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-search me-2"></i>Búsqueda y Filtros</h2>
            <p>Para facilitar la localización de un profesional, el directorio ofrece potentes herramientas de filtrado:</p>
            <ul>
                <li><strong>Búsqueda por Texto:</strong> Filtre por nombre, apellido o número de cédula.</li>
                <li><strong>Filtro por Especialidad:</strong> Seleccione una especialidad específica para ver solo a los expertos en esa área.</li>
                <li><strong>Limpiar Filtros:</strong> Restablezca todos los criterios de búsqueda con un solo clic.</li>
            </ul>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-calendar-alt me-2"></i>Solicitud de Citas</h2>
            <p>Si usted ha ingresado al sistema con el rol de <strong>Paciente</strong>, verá un botón de acción en cada tarjeta para solicitar una cita directamente con el profesional seleccionado, facilitando el acceso a la salud.</p>
        </div>
    </main>

    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
