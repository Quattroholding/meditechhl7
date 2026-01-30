<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Agendamiento de Citas - Centro de Ayuda SAMI</title>
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
            --calendar-color: #e65100;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-text);
        }

        /* Sidebar */
        .help-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .help-sidebar .logo {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .help-sidebar .logo img {
            max-width: 150px;
        }

        .help-sidebar .logo h4 {
            color: #fff;
            margin-top: 10px;
            font-weight: 600;
        }

        .help-sidebar .nav-section {
            padding: 10px 20px;
        }

        .help-sidebar .nav-section-title {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-left: 10px;
        }

        .help-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .help-sidebar .nav-link:hover,
        .help-sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }

        .help-sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        .help-sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
        }

        /* Main Content */
        .help-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

         /* Breadcrumb */
        .help-breadcrumb {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Header */
        .module-header{
            background: linear-gradient(135deg, #e65100 0%, #ff9800 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(230, 81, 0, 0.3);
        }

         .module-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .module-header h1 {
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
        }

        .module-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
        }

        .help-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .help-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .help-header .breadcrumb {
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .help-header .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
        }

        .help-header .breadcrumb-item.active {
            color: #fff;
        }

        /* Content Sections */
        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            color: #e65100;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #fff3e0;
        }

        .content-section h3 {
            color: #333;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .content-section h4 {
            color: #555;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Table of Contents */
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #01579b;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e1f5fe;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list li {
            margin-bottom: 10px;
        }

        .toc-list a {
            color: #0288d1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #e1f5fe;
            color: #01579b;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }

        /* Steps */
        .step-card {
            background: linear-gradient(135deg, #fff9f5 0%, #fff 100%);
            border-left: 4px solid #e65100;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: #e65100;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 15px;
        }

        .step-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border: 2px dashed #ff9800;
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
            color: #e65100;
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #bf360c;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: #e65100;
        }

        /* Info Boxes */
        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .info-box.note {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
        }

        .info-box.warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }

        .info-box.tip {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .info-box.danger {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }

        .info-box-title {
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Table of Contents */
        .toc {
            background: #fff3e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .toc h5 {
            color: #e65100;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list li {
            margin-bottom: 8px;
        }

        .toc-list a {
            color: #bf360c;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: rgba(230, 81, 0, 0.1);
            color: #e65100;
        }

        /* Field Tables */
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .field-table th {
            background: #e65100;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .field-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .field-table tr:hover {
            background: #fff9f5;
        }

        .required-badge {
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        .optional-badge {
            background: #6c757d;
            color: white;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 3px;
        }

        .status-proposed { background: #e0e0e0; color: #424242; }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-booked { background: #e3f2fd; color: #1565c0; }
        .status-confirm { background: #e8f5e9; color: #2e7d32; }
        .status-arrived { background: #f3e5f5; color: #7b1fa2; }
        .status-fulfilled { background: #c8e6c9; color: #1b5e20; }
        .status-checked-in { background: #b3e5fc; color: #0277bd; }
        .status-cancelled { background: #ffcdd2; color: #c62828; }
        .status-noshow { background: #ffccbc; color: #bf360c; }
        .status-waitlist { background: #ffe0b2; color: #ef6c00; }

        /* Calendar Views */
        .view-card {
            background: #fff;
            border: 2px solid #fff3e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .view-card:hover {
            border-color: #ff9800;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.2);
        }

        .view-card i {
            font-size: 2.5rem;
            color: #e65100;
            margin-bottom: 15px;
        }

        .view-card h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .view-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Workflow diagram */
        .workflow-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 10px;
            margin: 20px 0;
        }

        .workflow-step {
            text-align: center;
            padding: 10px 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .workflow-arrow {
            color: #e65100;
            font-size: 1.5rem;
        }

        /* Type cards */
        .type-card {
            background: linear-gradient(135deg, #fff 0%, #fff9f5 100%);
            border: 1px solid #ffe0b2;
            border-radius: 12px;
            padding: 25px;
            height: 100%;
        }

        .type-card .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .type-card .icon-circle.presencial {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .type-card .icon-circle.virtual {
            background: #e3f2fd;
            color: #1565c0;
        }

        .type-card h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .type-card ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .type-card li {
            margin-bottom: 5px;
            color: #666;
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #e65100;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(230, 81, 0, 0.4);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background: #bf360c;
            color: white;
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .help-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .help-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
  @include('help.sidebar', ['active' => 'appointments'])

    <!-- Main Content -->
    <main class="help-content">
       

         <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Guia de Agendamiento de Citas</a></li>
                <li class="breadcrumb-item active">Agendamiento de Citas</li>
            </ol>
        </nav>

          <!-- Module Header -->
        <div class="module-header">
             <h1><i class="fas fa-calendar-check me-3"></i>Guía de Agendamiento de Citas</h1>
            <p>Aprende a gestionar citas médicas, usar el calendario y administrar el flujo de atención de pacientes.</p>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                        {{--}}<li><a href="#tipos-cita"><i class="fas fa-list"></i> 2. Tipos de Cita</a></li>{{--}}
                        <li><a href="#crear-cita"><i class="fas fa-plus-circle"></i> 2. Crear Nueva Cita</a></li>
                        <li><a href="#calendario"><i class="fas fa-calendar-alt"></i> 3. Vista Calendario</a></li>
                        <li><a href="#estados"><i class="fas fa-exchange-alt"></i> 4. Estados de Cita</a></li>
                        <li><a href="#gestion"><i class="fas fa-tasks"></i> 5. Gestión de Citas</a></li>
                        <li><a href="#whatsapp"><i class="fab fa-whatsapp"></i> 6. Integración WhatsApp</a></li>
                        <li><a href="#listado"><i class="fas fa-table"></i> 7. Listado de Citas</a></li>
                        <li><a href="#tips"><i class="fas fa-lightbulb"></i> 8. Tips y Mejores Prácticas</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">

        <!-- Section 1: Introduction -->
        <section id="introduccion" class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Módulo de Citas</h2>

            <p>El módulo de <strong>Agendamiento de Citas</strong> es el centro de control para la programación y gestión de consultas médicas en SAMI. Permite coordinar eficientemente el tiempo de los profesionales de salud con las necesidades de los pacientes.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Características Principales
                        </div>
                        <ul class="mb-0">
                            <li>Calendario visual interactivo</li>
                            <li>Múltiples vistas (mes, semana, día)</li>
                            <li>Gestión de estados de citas</li>
                            <li>Soporte para citas presenciales y virtuales</li>
                            <li>Integración con WhatsApp</li>
                            <li>Notificaciones automáticas</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box note">
                        <div class="info-box-title">
                            <i class="fas fa-lightbulb text-primary"></i>
                            Requisitos Previos
                        </div>
                        <ul class="mb-0">
                            <li>Tener al menos una <a href="{{ route('help.branches') }}">sucursal</a> creada</li>
                            <li>Tener al menos un <a href="{{ route('help.consulting-rooms') }}">consultorio</a> configurado</li>
                            <li>Tener <a href="{{ route('help.patients') }}">pacientes</a> registrados</li>
                            <li>Doctor(es) con horarios configurados</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-btn.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 2: Appointment Types
        <section id="tipos-cita" class="content-section">
            <h2><i class="fas fa-list me-2"></i>2. Tipos de Cita</h2>

            <p>SAMI soporta dos modalidades de atención para adaptarse a las necesidades de su práctica médica:</p>

            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <div class="type-card">
                        <div class="icon-circle presencial">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h5>Cita Presencial</h5>
                        <p class="text-muted mb-3">Consultas que requieren la presencia física del paciente en el consultorio.</p>
                        <ul>
                            <li>Exámenes físicos</li>
                            <li>Procedimientos médicos</li>
                            <li>Consultas de primera vez</li>
                            <li>Evaluaciones completas</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="type-card">
                        <div class="icon-circle virtual">
                            <i class="fas fa-video"></i>
                        </div>
                        <h5>Cita Virtual (Telemedicina)</h5>
                        <p class="text-muted mb-3">Consultas realizadas de forma remota mediante videollamada.</p>
                        <ul>
                            <li>Seguimiento de tratamientos</li>
                            <li>Consultas de control</li>
                            <li>Revisión de resultados</li>
                            <li>Asesorías médicas</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Sobre las Citas Virtuales
                </div>
                <p class="mb-0">Las citas virtuales utilizan la plataforma <strong>Jitsi Meet</strong> integrada en SAMI. Al crear una cita virtual, el sistema genera automáticamente un enlace único para la videollamada que se comparte con el paciente.</p>
            </div>

            <div class="screenshot-placeholder">
                <i class="fas fa-image"></i>
                <p>Captura de pantalla: Selector de tipo de cita en el formulario de creación</p>
                <small>Recomendado: 600x300px - Mostrar el campo "Tipo de Cita" con las opciones desplegadas</small>
            </div>
        </section> -->

        <!-- Section 3: Create Appointment -->
        <section id="crear-cita" class="content-section">
            <h2><i class="fas fa-plus-circle me-2"></i>2. Crear Nueva Cita</h2>

            <p>Siga estos pasos para agendar una nueva cita médica en el sistema:</p>

            <!-- Step 1 -->
            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Acceder al Módulo de Citas</span></h4>
                <p>Navegue a <strong>Citas</strong> desde el menú lateral izquierdo. Puede acceder a través de:</p>
                <ul>
                    <li><strong>Vista Calendario:</strong> Menú → Citas → Calendario</li>
                    <!--<li><strong>Vista Lista:</strong> Menú → Citas → Listado</li>-->
                </ul>

                <div>
                    <img src="{{ asset('images/tutorial/appointments/appointment-list.png') }}" alt="" style="width: 100%;">
                </div>
            </div>

            <!-- Step 2 -->
            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Iniciar Creación de Cita</span></h4>
                <p>Hay varias formas de iniciar la creación de una nueva cita:</p>
                <ul>
                    <li><strong>Botón "Nueva Cita":</strong> Click en el botón superior derecho</li>
                    <li><strong>Click en calendario:</strong> Haga click en cualquier espacio disponible del calendario</li>
                    <li><strong>Arrastrar y soltar:</strong> Arrastre sobre un rango de tiempo para preseleccionar la hora</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-new.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 3 -->
            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Completar Formulario de Cita</span></h4>
                <p>Se abrirá el modal de creación de cita. Complete los siguientes campos:</p>

                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Requerido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Paciente</strong></td>
                            <td>Busque y seleccione el paciente por nombre o documento de identidad</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Doctor</strong></td>
                            <td>Seleccione el profesional de salud que atenderá la cita</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Sucursal</strong></td>
                            <td>Seleccione la sucursal donde se realizará la consulta</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Consultorio</strong></td>
                            <td>Consultorio específico dentro de la sucursal seleccionada</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha</strong></td>
                            <td>Fecha programada para la cita</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Hora Inicio</strong></td>
                            <td>Hora de inicio de la consulta</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Hora Fin</strong></td>
                            <td>Hora estimada de finalización</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Cita</strong></td>
                            <td>Presencial o Virtual</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Motivo de Consulta</strong></td>
                            <td>Descripción breve del motivo de la visita</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>Notas</strong></td>
                            <td>Información adicional o instrucciones especiales</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                    </tbody>
                </table>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-modal.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 4 -->
            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Búsqueda de Paciente</span></h4>
                <p>El campo de paciente cuenta con búsqueda inteligente:</p>
                <ul>
                    <li>Escriba el nombre del paciente (al menos 2 caracteres)</li>
                    <li>O escriba el número de documento de identidad</li>
                    <li>Seleccione el paciente de la lista desplegable</li>
                </ul>

                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-lightbulb text-success"></i>
                        Consejo
                    </div>
                    <p class="mb-0">Si el paciente no existe, puede crear uno nuevo desde el módulo de <a href="{{ route('help.patients') }}">Pacientes</a> y luego regresar a crear la cita.</p>
                </div>

                <div>
                    <img src="{{ asset('images/tutorial/appointments/appointment-patient.png') }}" alt="" style="width: 100%;">
                </div>
            </div>

            <!-- Step 5 -->
            <div class="step-card">
                <h4><span class="step-number">5</span><span class="step-title">Selección de Horario Disponible</span></h4>
                <p>El sistema valida automáticamente la disponibilidad:</p>
                <ul>
                    <li>Se verifican los horarios de trabajo del doctor seleccionado</li>
                    <li>Se comprueba que no existan conflictos con otras citas</li>
                    <li>Se valida la disponibilidad del consultorio</li>
                </ul>

                <div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Importante
                    </div>
                    <p class="mb-0">Si intenta agendar fuera del horario de trabajo del doctor o en un horario ya ocupado, el sistema mostrará un mensaje de error indicando el conflicto.</p>
                </div>

                <div>
                    <img src="{{ asset('images/tutorial/appointments/appointment-schedule.png') }}" alt="" style="width: 100%;">
                </div>
            </div>

            <!-- Step 6 -->
            <div class="step-card">
                <h4><span class="step-number">6</span><span class="step-title">Guardar la Cita</span></h4>
                <p>Una vez completados todos los campos requeridos:</p>
                <ol>
                    <li>Revise que todos los datos sean correctos</li>
                    <li>Click en el botón <strong>"Guardar"</strong> o <strong>"Crear Cita"</strong></li>
                    <li>El sistema creará la cita con estado inicial <span class="status-badge status-pending">Pendiente</span></li>
                    <li>La cita aparecerá inmediatamente en el calendario</li>
                </ol>

                <div class="info-box note">
                    <div class="info-box-title">
                        <i class="fas fa-bell text-primary"></i>
                        Notificaciones
                    </div>
                    <p class="mb-0">Al crear una cita, el sistema puede enviar notificaciones automáticas al paciente por WhatsApp o correo electrónico, dependiendo de la configuración del sistema.</p>
                </div>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-created.png') }}" alt="" style="width: 100%;">
            </div>
            </div>
        </section>

        <!-- Section 4: Calendar View -->
        <section id="calendario" class="content-section">
            <h2><i class="fas fa-calendar-alt me-2"></i>3. Vista Calendario</h2>

            <p>El calendario interactivo es la herramienta principal para visualizar y gestionar las citas. SAMI ofrece tres vistas diferentes:</p>

            <div class="row mt-4 mb-4">
                <div class="col-md-4 mb-3">
                    <div class="view-card">
                        <i class="fas fa-calendar"></i>
                        <h5>Vista Mensual</h5>
                        <p>Panorama general del mes completo. Ideal para planificación a largo plazo.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="view-card">
                        <i class="fas fa-calendar-week"></i>
                        <h5>Vista Semanal</h5>
                        <p>Detalle de 7 días. Perfecta para gestión de la agenda semanal.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="view-card">
                        <i class="fas fa-calendar-day"></i>
                        <h5>Vista Diaria</h5>
                        <p>Detalle hora por hora del día. Óptima para control del día actual.</p>
                    </div>
                </div>
            </div>

            <h3>Características del Calendario</h3>

            <div class="row">
                <div class="col-md-6">
                    <h4><i class="fas fa-filter text-primary me-2"></i>Filtros Disponibles</h4>
                    <ul>
                        <li><strong>Por Doctor:</strong> Ver citas de un médico específico</li>
                        <li><strong>Por Sucursal:</strong> Filtrar por ubicación</li>
                        <li><strong>Por Consultorio:</strong> Ver agenda de un consultorio</li>
                        <li><strong>Por Estado:</strong> Filtrar por estado de la cita</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4><i class="fas fa-mouse-pointer text-primary me-2"></i>Interacciones</h4>
                    <ul>
                        <li><strong>Click en cita:</strong> Ver detalles y opciones</li>
                        <li><strong>Arrastrar cita:</strong> Reprogramar fecha/hora</li>
                        <li><strong>Redimensionar:</strong> Ajustar duración</li>
                        <li><strong>Doble click:</strong> Editar cita</li>
                    </ul>
                </div>
            </div>

             <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-weekly.png') }}" alt="" style="width: 100%;">
            </div>

            <h3>Código de Colores</h3>
            <p>Las citas se muestran con diferentes colores según su estado para facilitar la identificación visual:</p>

            <div class="row mt-3">
                <div class="col-md-6">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Color</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#ffc107;border-radius:4px;"></span></td>
                                <td>Pendiente</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#2196f3;border-radius:4px;"></span></td>
                                <td>Reservada</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#4caf50;border-radius:4px;"></span></td>
                                <td>Confirmada</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#9c27b0;border-radius:4px;"></span></td>
                                <td>En Sala de Espera</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#00bcd4;border-radius:4px;"></span></td>
                                <td>En Consulta</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Color</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#8bc34a;border-radius:4px;"></span></td>
                                <td>Completada</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#f44336;border-radius:4px;"></span></td>
                                <td>Cancelada</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#ff5722;border-radius:4px;"></span></td>
                                <td>No Asistió</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#ff9800;border-radius:4px;"></span></td>
                                <td>Lista de Espera</td>
                            </tr>
                            <tr>
                                <td><span style="display:inline-block;width:20px;height:20px;background:#9e9e9e;border-radius:4px;"></span></td>
                                <td>Propuesta</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-colors.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 5: Appointment States -->
        <section id="estados" class="content-section">
            <h2><i class="fas fa-exchange-alt me-2"></i>4. Estados de Cita</h2>

            <p>Las citas en SAMI siguen un flujo de trabajo definido. Cada estado representa una etapa en el proceso de atención:</p>

            <div class="row mt-4">
                <div class="col-12">
                    <h4>Estados Disponibles</h4>
                    <div class="mb-4">
                        <span class="status-badge status-proposed">Propuesta</span>
                        <span class="status-badge status-pending">Pendiente</span>
                        <span class="status-badge status-booked">Reservada</span>
                        <span class="status-badge status-confirm">Confirmada</span>
                        <span class="status-badge status-arrived">En Sala</span>
                        <span class="status-badge status-checked-in">En Consulta</span>
                        <span class="status-badge status-fulfilled">Completada</span>
                        <span class="status-badge status-cancelled">Cancelada</span>
                        <span class="status-badge status-noshow">No Asistió</span>
                        <span class="status-badge status-waitlist">Lista de Espera</span>
                    </div>
                </div>
            </div>

            <h3>Flujo Típico de una Cita</h3>

            <div class="workflow-container">
                <div class="workflow-step">
                    <span class="status-badge status-pending">Pendiente</span>
                    <small class="d-block mt-1">Cita creada</small>
                </div>
                <i class="fas fa-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="status-badge status-confirm">Confirmada</span>
                    <small class="d-block mt-1">Paciente confirma</small>
                </div>
                <i class="fas fa-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="status-badge status-arrived">En Sala</span>
                    <small class="d-block mt-1">Paciente llega</small>
                </div>
                <i class="fas fa-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="status-badge status-checked-in">En Consulta</span>
                    <small class="d-block mt-1">Inicia atención</small>
                </div>
                <i class="fas fa-arrow-right workflow-arrow"></i>
                <div class="workflow-step">
                    <span class="status-badge status-fulfilled">Completada</span>
                    <small class="d-block mt-1">Finaliza consulta</small>
                </div>
            </div>

            <h3>Descripción de Estados</h3>

            <table class="field-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Descripción</th>
                        <th>Acciones Disponibles</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="status-badge status-proposed">Propuesta</span></td>
                        <td>Cita sugerida, pendiente de aceptación por el paciente</td>
                        <td>Confirmar, Cancelar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-pending">Pendiente</span></td>
                        <td>Cita creada, esperando confirmación</td>
                        <td>Confirmar, Cancelar, Editar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-booked">Reservada</span></td>
                        <td>Cita reservada en el sistema</td>
                        <td>Confirmar, Cancelar, Editar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-confirm">Confirmada</span></td>
                        <td>El paciente ha confirmado su asistencia</td>
                        <td>Marcar llegada, Cancelar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-arrived">En Sala</span></td>
                        <td>Paciente está en la sala de espera</td>
                        <td>Iniciar consulta, No asistió</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-checked-in">En Consulta</span></td>
                        <td>La consulta está en progreso</td>
                        <td>Completar consulta</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-fulfilled">Completada</span></td>
                        <td>La consulta ha finalizado exitosamente</td>
                        <td>Ver detalles, Ver historia</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-cancelled">Cancelada</span></td>
                        <td>La cita fue cancelada</td>
                        <td>Ver motivo, Reagendar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-noshow">No Asistió</span></td>
                        <td>El paciente no se presentó a la cita</td>
                        <td>Reagendar, Contactar</td>
                    </tr>
                    <tr>
                        <td><span class="status-badge status-waitlist">Lista de Espera</span></td>
                        <td>Paciente en espera de disponibilidad</td>
                        <td>Asignar horario, Cancelar</td>
                    </tr>
                </tbody>
            </table>

                        <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-status.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 6: Appointment Management -->
        <section id="gestion" class="content-section">
            <h2><i class="fas fa-tasks me-2"></i>5. Gestión de Citas</h2>

            <h3>Ver Detalles de una Cita</h3>
            <p>Al hacer click en una cita del calendario, se muestra un panel con información detallada:</p>

            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li>Nombre completo del paciente</li>
                        <li>Documento de identidad</li>
                        <li>Teléfono de contacto</li>
                        <li>Doctor asignado</li>
                        <li>Fecha y hora programada</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul>
                        <li>Sucursal y consultorio</li>
                        <li>Tipo de cita (presencial/virtual)</li>
                        <li>Estado actual</li>
                        <li>Motivo de consulta</li>
                        <li>Notas adicionales</li>
                    </ul>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-detail.png') }}" alt="" style="width: 100%;">
            </div>

            <h3>Editar una Cita</h3>
            <p>Para modificar los datos de una cita existente:</p>
            <ol>
                <li>Haga click en la cita en el calendario o lista</li>
                <li>Seleccione <strong>"Editar"</strong> en el menú de opciones</li>
                <li>Modifique los campos necesarios</li>
                <li>Click en <strong>"Guardar cambios"</strong></li>
            </ol>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Restricciones de Edición
                </div>
                <ul class="mb-0">
                    <li>Las citas <span class="status-badge status-fulfilled">Completadas</span> no pueden modificarse</li>
                    <li>Las citas <span class="status-badge status-cancelled">Canceladas</span> no pueden editarse, solo reagendarse</li>
                    <li>No se puede cambiar a un horario que genere conflictos</li>
                </ul>
            </div>

            <!-- <h3>Reprogramar una Cita (Drag & Drop)</h3>
            <p>El calendario permite reprogramar citas arrastrándolas:</p>
            <ol>
                <li>Ubique la cita en el calendario</li>
                <li>Haga click sostenido sobre la cita</li>
                <li>Arrastre al nuevo horario deseado</li>
                <li>Suelte para confirmar el cambio</li>
                <li>El sistema validará la disponibilidad automáticamente</li>
            </ol>

            <div class="screenshot-placeholder">
                <i class="fas fa-arrows-alt"></i>
                <p>Captura de pantalla: Cita siendo arrastrada a nuevo horario</p>
                <small>Recomendado: 800x500px - Mostrar el efecto visual de arrastrar una cita en el calendario</small>
            </div> -->

            <h3>Cancelar una Cita</h3>
            <p>Para cancelar una cita:</p>
            <ol>
                <li>Seleccione la cita</li>
                <li>Click en <strong>"Cancelar cita"</strong></li>
                <!--<li>Ingrese el motivo de cancelación (opcional pero recomendado)</li> -->
                <li>Confirme la cancelación</li>
            </ol>

            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-circle text-danger"></i>
                    Advertencia
                </div>
                <p class="mb-0">La cancelación de citas es una acción que queda registrada en el historial. Si el paciente necesita otra cita, deberá crear una nueva.</p>
            </div>
        </section>

        <!-- Section 7: WhatsApp Integration -->
        <section id="whatsapp" class="content-section">
            <h2><i class="fab fa-whatsapp me-2"></i>6. Integración con WhatsApp</h2>

            <p>SAMI incluye integración con WhatsApp para comunicación directa con los pacientes sobre sus citas:</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fab fa-whatsapp text-success"></i>
                            Funciones de WhatsApp
                        </div>
                        <ul class="mb-0">
                            <li><strong>Confirmar cita:</strong> Enviar solicitud de confirmación</li>
                            <li><strong>Recordatorios:</strong> Notificar próximas citas</li>
                            <li><strong>Cancelación:</strong> Notificar sobre cancelaciones</li>
                            <li><strong>Reprogramación:</strong> Informar cambios de horario</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box note">
                        <div class="info-box-title">
                            <i class="fas fa-cog text-primary"></i>
                            Configuración Requerida
                        </div>
                        <p class="mb-0">La integración de WhatsApp debe estar configurada por el administrador del sistema. Contacte al soporte si esta función no está disponible.</p>
                    </div>
                </div>
            </div>

            <h3>Mensaje de Confirmación</h3>
            <p>Se le enviará al paciente un mensaje vía WhatsApp, 2 horas antes de la cita programada como recordatorio para que confirme o cancele la cita.</p>
            <!--<ol>
                <li>El sistema enviará el mensaje al número registrado del paciente</li>
            </ol> -->

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-wp.jpeg') }}" alt="" >
            </div>

            <h3>Mensajes Interactivos</h3>
            <p>Los pacientes pueden interactuar con los mensajes de WhatsApp para:</p>
            <ul>
                <li><strong>Confirmar asistencia:</strong> El paciente responde "Confirmar" y la cita cambia automáticamente a estado <span class="status-badge status-confirm">Confirmada</span></li>
                <li><strong>Cancelar cita:</strong> El paciente responde "Cancelar" y la cita se actualiza a estado <span class="status-badge status-cancelled">Cancelada</span></li>
                <li><strong>Solicitar reagendamiento:</strong> El paciente puede solicitar un nuevo horario</li>
            </ul>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-robot text-primary"></i>
                    Automatización con n8n
                </div>
                <p class="mb-0">SAMI utiliza la plataforma n8n para automatizar los flujos de mensajería de WhatsApp. Las respuestas de los pacientes se procesan automáticamente y actualizan el estado de las citas en tiempo real.</p>
            </div>
        </section>

        <!-- Section 8: Appointment List -->
        <section id="listado" class="content-section">
            <h2><i class="fas fa-list-alt me-2"></i>7. Listado de Citas</h2>

            <p>Además del calendario, SAMI ofrece una vista de lista que permite gestionar citas de forma tabular:</p>

            <h3>Acceder al Listado</h3>
            <p>Navegue a <strong>Citas → Listado</strong> desde el menú lateral.</p>

            <h3>Características del Listado</h3>
            <ul>
                <li><strong>Búsqueda:</strong> Buscar por nombre de paciente, doctor o número de documento</li>
                <li><strong>Filtros avanzados:</strong> Por fecha, estado, sucursal, doctor</li>
                <li><strong>Ordenamiento:</strong> Por fecha, paciente, doctor, estado</li>
                <li><strong>Paginación:</strong> Navegar entre páginas de resultados</li>
                <li><strong>Acciones rápidas:</strong> Cambiar estado, editar, cancelar desde la lista</li>
            </ul>

            <div>
                <img src="{{ asset('images/tutorial/appointments/appointment-filter.png') }}" alt="" style="width: 100%;">
            </div>

            <h3>Columnas del Listado</h3>
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Columna</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Paciente</strong></td>
                        <td>Nombre completo y documento del paciente</td>
                    </tr>
                    <tr>
                        <td><strong>Doctor</strong></td>
                        <td>Profesional asignado a la cita</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha/Hora</strong></td>
                        <td>Fecha y hora programada</td>
                    </tr>
                    <tr>
                        <td><strong>Sucursal</strong></td>
                        <td>Ubicación de la consulta</td>
                    </tr>
                    <tr>
                        <td><strong>Tipo</strong></td>
                        <td>Presencial o Virtual</td>
                    </tr>
                    <tr>
                        <td><strong>Estado</strong></td>
                        <td>Estado actual de la cita</td>
                    </tr>
                    <tr>
                        <td><strong>Acciones</strong></td>
                        <td>Botones para ver, editar, cambiar estado</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Section 9: Tips -->
        <section id="tips" class="content-section">
            <h2><i class="fas fa-lightbulb me-2"></i>8. Tips y Mejores Prácticas</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check text-success"></i>
                            Mejores Prácticas
                        </div>
                        <ul class="mb-0">
                            <li>Confirme las citas con anticipación vía WhatsApp</li>
                            <li>Utilice el motivo de consulta para prepararse mejor</li>
                            <li>Revise el calendario al inicio del día</li>
                            <li>Deje tiempo de buffer entre citas</li>
                            <li>Actualice los estados en tiempo real</li>
                            <li>Registre los motivos de cancelación</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box warning">
                        <div class="info-box-title">
                            <i class="fas fa-exclamation text-warning"></i>
                            Errores Comunes a Evitar
                        </div>
                        <ul class="mb-0">
                            <li>No verificar disponibilidad antes de agendar</li>
                            <li>Olvidar actualizar el estado de las citas</li>
                            <li>No registrar el motivo de consulta</li>
                            <li>Agendar fuera del horario del doctor</li>
                            <li>No enviar recordatorios a los pacientes</li>
                            <li>Dejar citas en estado "Pendiente" indefinidamente</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--<h3>Atajos de Teclado (Calendario)</h3>
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Atajo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><kbd>←</kbd> / <kbd>→</kbd></td>
                        <td>Navegar al período anterior/siguiente</td>
                    </tr>
                    <tr>
                        <td><kbd>T</kbd></td>
                        <td>Ir a la fecha actual (Today)</td>
                    </tr>
                    <tr>
                        <td><kbd>M</kbd></td>
                        <td>Cambiar a vista mensual</td>
                    </tr>
                    <tr>
                        <td><kbd>W</kbd></td>
                        <td>Cambiar a vista semanal</td>
                    </tr>
                    <tr>
                        <td><kbd>D</kbd></td>
                        <td>Cambiar a vista diaria</td>
                    </tr>
                </tbody>
            </table>-->

            <div class="info-box note mt-4">
                <div class="info-box-title">
                    <i class="fas fa-question-circle text-primary"></i>
                    ¿Necesita Ayuda?
                </div>
                <p class="mb-0">Si tiene dudas sobre el módulo de citas o encuentra algún problema, contacte al equipo de soporte técnico de SAMI. Recuerde que puede acceder a otras guías desde el menú lateral para aprender sobre funcionalidades relacionadas como la <a href="{{ route('help.medical-history') }}">Historia Médica</a>.</p>
            </div>
        </section>

            </div> <!-- End col-lg-8 -->
        </div> <!-- End row -->

        <!-- Navigation -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.medical-history') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Historia Médica
            </a>
            <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
        </div>
    </main>

    <!-- Back to Top -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Back to top visibility
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });
    </script>
</body>
</html>
