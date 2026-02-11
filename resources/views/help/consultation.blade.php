<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Realizar Consulta - Centro de Ayuda SAMI</title>
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
            --consultation-color: #d32f2f;
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
        .help-header {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
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
            color: #d32f2f;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffebee;
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

        /* Steps */
        .step-card {
            background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
            border-left: 4px solid #d32f2f;
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
            background: #d32f2f;
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
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            border: 2px dashed #f44336;
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
            color: #d32f2f;
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #c62828;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: #d32f2f;
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
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #d32f2f;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffebee;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list li {
            margin-bottom: 12px;
        }

        .toc-list a {
            color: #c62828;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #ffebee;
            color: #d32f2f;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }

        /* Field Tables */
        .field-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .field-table th {
            background: #d32f2f;
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
            background: #fff5f5;
        }

        /* Section Cards */
        .section-card {
            background: #fff;
            border: 2px solid #ffebee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            border-color: #f44336;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.2);
        }

        .section-card .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 10px;
            background: #ffebee;
            color: #d32f2f;
        }

        .section-card h5 {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .section-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #d32f2f;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.4);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background: #c62828;
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

        
        @media (max-width: 480px) { 
        .step-card, .info-box{
                border-left: none;
                padding: 25px;
                border-bottom: 5px solid #d32f2f;
                border-radius: 8px;
            }

            .info-box.note {
                border-left: none;
                border-bottom-color: #2196f3; 
            }
            .info-box.warning {
                border-left: none;
                border-bottom-color: #ff9800; 
            }
            .info-box.tip {
                border-left: none;
                border-bottom-color: #4caf50; 
            }
            
            .field-table {
                border: 0;
                box-shadow: none;
            }
            
            .field-table thead {
                display: none;
            }
            
            .field-table tbody {
                display: block;
            }
            
            .field-table tr {
                display: block;
                margin-bottom: 15px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                background: #fff;
            }
            
            .field-table td {
                display: block;
                text-align: center;
                padding: 10px 12px;
                font-size: 14px;
                border-bottom: 1px solid #f0f0f0;
            }
            .field-table td:last-child {
                border-bottom: none;
            }
            
            /* Campo (título de cada tarjeta) */
            .field-table td[data-label="Campo"] {
                background: #d32f2f;
                color: white;
                font-size: 15px;
                padding: 12px;
                border-bottom: 2px solid #c42121;
            }
            

            .field-table td[data-label="Campo"] strong {
                color: white;
            }
            
            /* Descripción */
            .field-table td[data-label="Descripcion"] {
                padding: 15px;
                color: #555;
                font-size: 13px;
                line-height: 1.5;
            }
            
            .field-table td[data-label="Descripcion"]:before {
                content: "📝 ";
                margin-right: 5px;
            }
            
            /* Requerido */
            .field-table td[data-label="Unidad"] {
                background: #f5f5f5;
                padding: 12px 15px;
                text-align: center;
                font-weight: 600;
            }
            
            .field-table td[data-label="Unidad"]:before {
                content: "Unidad: ";
                font-weight: 600;
                color: #d32f2f;
                margin-right: 5px;
            }

        }

        @media (max-width: 365px){
            .info-box-title, .content-section h4{
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .content-section, .help-content {
                padding:25px;
            }
            .info-box{
                padding: 20px;
            }
            .step-card{
                padding: 15px;
            }
            .section-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            p, .section-card h5{
                text-align: center;
            }
        }
    </style>
</head>
<body>
   @include('help.sidebar', ['active' => 'consultation'])

    <!-- Main Content -->
    <main class="help-content">

        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('help.consultation') }}">Guias de Consultas Medicas</a></li>
                <li class="breadcrumb-item active">Realizar Consulta</li>
            </ol>
        </nav>
            
        <!-- Header -->
        <header class="help-header">
            <h1><i class="fas fa-stethoscope me-3"></i>Guía para Realizar Consultas Médicas</h1>
            <p>Aprende a documentar consultas médicas de manera completa y eficiente en SAMI.</p>
            
        </header>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                        <li><a href="#acceder"><i class="fas fa-sign-in-alt"></i> 2. Acceder a Consulta</a></li>
                        <li><a href="#secciones"><i class="fas fa-list"></i> 3. Secciones de Consulta</a></li>
                        <li><a href="#documentar"><i class="fas fa-edit"></i> 4. Documentar Consulta</a></li>
                        <li><a href="#finalizar"><i class="fas fa-check-circle"></i> 5. Finalizar Consulta</a></li>
                        <li><a href="#tips"><i class="fas fa-lightbulb"></i> 6. Tips y Mejores Prácticas</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">

        <!-- Section 1: Introduction -->
        <section id="introduccion" class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Módulo de Consultas</h2>

            <p>El módulo de <strong>Consultas Médicas</strong> es el corazón del sistema SAMI, donde los profesionales de salud documentan cada encuentro con sus pacientes. Este módulo permite registrar de manera estructurada toda la información clínica relevante.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Características Principales
                        </div>
                        <ul class="mb-0">
                            <li>Interfaz intuitiva con secciones expandibles</li>
                            <li>Autoguardado automático de información</li>
                            <li>Plantillas personalizables por especialidad</li>
                            <li>Acceso rápido a historia clínica del paciente</li>
                            <li>Generación automática de documentos médicos</li>
                            <li>Soporte para teleconsultas</li>
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
                            <li>Tener una <a href="{{ route('help.appointments') }}">cita agendada</a> para el paciente</li>
                            <li>El paciente debe estar <a href="{{ route('help.patients') }}">registrado</a> en el sistema</li>
                            <li>La cita debe estar en estado "Confirmada" o "Arrived"</li>
                            <li>Tener configuradas las plantillas de consulta (opcional)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_view.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 2: Accessing Consultation -->
        <section id="acceder" class="content-section">
            <h2><i class="fas fa-sign-in-alt me-2"></i>2. Acceder a una Consulta</h2>

            <p>Existen varias formas de acceder al módulo de consulta para atender a un paciente:</p>

            <!-- Step 1 -->
            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Desde el Calendario de Citas</span></h4>
                <p>Esta es la forma más común de acceder a una consulta:</p>
                <ul>
                    <li>Navegue a <strong>Citas → Calendario</strong></li>
                    <li>Localice la cita del paciente en el calendario</li>
                    <li>Haga clic en la cita para ver los detalles</li>
                    <li>Presione el botón <strong>"Iniciar"</strong></li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_start.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 2 -->
            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Desde el Listado de Citas</span></h4>
                <p>También puede acceder desde la vista de lista:</p>
                <ul>
                    <li>Navegue a <strong>Citas → Lista Citas</strong></li>
                    <li>Busque la cita del paciente usando los filtros</li>
                    <li>En la columna de acciones, haga clic en </i> <strong>"Iniciar Consulta"</strong></li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_start2.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 3 -->
            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Desde el Perfil del Paciente</span></h4>
                <p>Si está revisando el perfil de un paciente:</p>
                <ul>
                    <li>En el perfil del paciente, vaya a la pestaña de <strong>"Citas"</strong></li>
                    <li>Localice la cita actual</li>
                    <li>Haga clic en <strong>"Iniciar Consulta"</strong></li>
                </ul>
            </div>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Importante
                </div>
                <p class="mb-0">Solo puede iniciar una consulta si la cita está en estado <strong>"LLegado"</strong>. Si la cita está en otro estado, primero debe actualizarla, ya sea que tenga que <strong>"Confirmar"</strong> o <strong>"Registrar Llegada"</strong>, todo dependerá del estado que tenga la cita.</p>
            </div>
        </section>

        <!-- Section 3: Consultation Sections -->
        <section id="secciones" class="content-section">
            <h2><i class="fas fa-list me-2"></i>3. Secciones de la Consulta</h2>

            <p>La interfaz de consulta está organizada en secciones expandibles (acordeón) que se cargan dinámicamente. Cada sección contiene campos específicos para documentar diferentes aspectos de la consulta médica.</p>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Secciones Personalizables
                </div>
                <p class="mb-0">Las secciones que ve pueden variar según su especialidad médica y las plantillas configuradas en <a href="{{ route('help.settings') }}">Configuraciones</a>. Los administradores pueden personalizar qué secciones aparecen para cada profesional.</p>
            </div>

            <h3>Secciones Comunes</h3>

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <h5>Motivo de Consulta</h5>
                        <p>Razón principal por la cual el paciente solicita atención médica. Descripción breve del problema o síntoma principal.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5>Enfermedad Actual</h5>
                        <p>Descripción detallada de la evolución de los síntomas, tiempo de inicio, factores agravantes y atenuantes.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h5>Signos Vitales</h5>
                        <p>Registro de presión arterial, frecuencia cardíaca, temperatura, frecuencia respiratoria, saturación de oxígeno y otros.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h5>Examen Físico</h5>
                        <p>Hallazgos del examen físico general y por sistemas. Incluye inspección, palpación, percusión y auscultación.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-diagnoses"></i>
                        </div>
                        <h5>Diagnóstico</h5>
                        <p>Diagnósticos principales y secundarios utilizando códigos CIE-10. Puede agregar múltiples diagnósticos.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h5>Plan de Tratamiento</h5>
                        <p>Indicaciones médicas, medicamentos prescritos, órdenes de laboratorio, imágenes y recomendaciones generales.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h5>Prescripción</h5>
                        <p>Medicamentos recetados con dosis, frecuencia, vía de administración y duración del tratamiento.</p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="section-card">
                        <div class="icon-circle">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h5>Órdenes Médicas</h5>
                        <p>Solicitudes de exámenes de laboratorio, estudios de imagen, procedimientos y otras órdenes médicas.</p>
                    </div>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_tab.png') }}" alt="" style="width: 100%;">
            </div>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb text-success"></i>
                    Carga Progresiva
                </div>
                <p class="mb-0">Las secciones se cargan de forma progresiva para mejorar el rendimiento. Cada sección se carga cuando la expande por primera vez, lo que hace que la interfaz sea más rápida y fluida.</p>
            </div>
        </section>

        <!-- Section 4: Documenting Consultation -->
        <section id="documentar" class="content-section">
            <h2><i class="fas fa-edit me-2"></i>4. Documentar la Consulta</h2>

            <p>A continuación, se describe el proceso paso a paso para documentar una consulta médica completa:</p>

            <!-- Step 1: Patient Info -->
            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Revisar Información del Paciente</span></h4>
                <p>Al iniciar la consulta, verá un encabezado con la información básica del paciente:</p>
                <ul>
                    <li><strong>Nombre completo</strong> del paciente</li>
                    <li><strong>Edad</strong> y fecha de nacimiento</li>
                    <li><strong>Tipo y número de identificación</strong></li>
                    <li><strong>Información de la cita:</strong> fecha, hora, tipo (presencial/virtual)</li>
                </ul>

                <div class="info-box note">
                    <div class="info-box-title">
                        <i class="fas fa-info-circle text-primary"></i>
                        Menú Lateral
                    </div>
                    <p class="mb-0">En el lado derecho de la pantalla encontrará un menú lateral con acceso rápido a la historia clínica del paciente, documentos previos y, en caso de teleconsulta, la sala de videollamada.</p>
                </div>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_patientinfo.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 2: Chief Complaint -->
            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Registrar Motivo de Consulta</span></h4>
                <p>Expanda la primera sección <strong>"Motivo de Consulta"</strong> y documente:</p>
                <ul>
                    <li>La razón principal de la visita en palabras del paciente</li>
                    <li>Síntomas principales que presenta</li>
                    <li>Duración aproximada del problema</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_mot.png') }}" alt="" style="width: 100%;">
            </div>

                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-save text-success"></i>
                        Autoguardado
                    </div>
                    <p class="mb-0">El sistema guarda automáticamente la información mientras escribe. No es necesario presionar un botón de guardar después de cada sección.</p>
                </div>
            </div>

            <!-- Step 3: Vital Signs -->
            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Registrar Signos Vitales</span></h4>
                <p>En la sección de <strong>"Signos Vitales"</strong>, complete los campos correspondientes:</p>

                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Presión Arterial</strong></td>
                            <td data-label="Descripcion">Sistólica / Diastólica</td>
                            <td data-label="Unidad">mmHg</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Frecuencia Cardíaca</strong></td>
                            <td data-label="Descripcion">Pulsaciones por minuto</td>
                            <td data-label="Unidad">lpm</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Temperatura</strong></td>
                            <td data-label="Descripcion">Temperatura corporal</td>
                            <td data-label="Unidad">°C</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Frecuencia Respiratoria</strong></td>
                            <td data-label="Descripcion">Respiraciones por minuto</td>
                            <td data-label="Unidad">rpm</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Saturación O₂</strong></td>
                            <td data-label="Descripcion">Saturación de oxígeno</td>
                            <td data-label="Unidad">%</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Peso</strong></td>
                            <td data-label="Descripcion">Peso corporal</td>
                            <td data-label="Unidad">kg</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Talla</strong></td>
                            <td data-label="Descripcion">Altura</td>
                            <td data-label="Unidad">cm</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>IMC</strong></td>
                            <td data-label="Descripcion">Índice de Masa Corporal (calculado automáticamente)</td>
                            <td data-label="Unidad">kg/m²</td>
                        </tr>
                    </tbody>
                </table>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_vitalsigns.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 4: Physical Exam -->
            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Documentar Examen Físico</span></h4>
                <p>Complete los hallazgos del examen físico en las áreas correspondientes:</p>
                <ul>
                    <li><strong>Apariencia General:</strong> Estado general del paciente</li>
                    <li><strong>Examen por Sistemas:</strong> Hallazgos específicos por cada sistema</li>
                    <li>Utilice las plantillas predefinidas si están configuradas</li>
                    <li>Agregue notas adicionales según sea necesario</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_physicalexam.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 5: Diagnosis -->
            <div class="step-card">
                <h4><span class="step-number">5</span><span class="step-title">Agregar Diagnósticos</span></h4>
                <p>En la sección de <strong>"Diagnóstico"</strong>:</p>
                <ul>
                    <li>Haga clic en <strong>"Agregar Diagnóstico"</strong></li>
                    <li>Busque el diagnóstico por nombre o código CIE-10</li>
                    <li>Seleccione el tipo: Principal, Secundario, o Presuntivo</li>
                    <li>Agregue notas adicionales si es necesario</li>
                    <li>Puede agregar múltiples diagnósticos</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_diagnostics.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 6: Prescription -->
            <div class="step-card">
                <h4><span class="step-number">6</span><span class="step-title">Crear Prescripción Médica</span></h4>
                <p>Para prescribir medicamentos:</p>
                <ul>
                    <li>Vaya a la sección <strong>"Prescripción"</strong></li>
                    <li>Haga clic en <strong>"Agregar Medicamento"</strong></li>
                    <li>Busque el medicamento por nombre comercial o genérico</li>
                    <li>Complete los campos:
                        <ul>
                            <li><strong>Dosis:</strong> Cantidad por toma</li>
                            <li><strong>Frecuencia:</strong> Cada cuántas horas</li>
                            <li><strong>Vía:</strong> Oral, intravenosa, tópica, etc.</li>
                            <li><strong>Duración:</strong> Días de tratamiento</li>
                            <li><strong>Indicaciones:</strong> Instrucciones especiales</li>
                        </ul>
                    </li>
                    <li>Agregue todos los medicamentos necesarios</li>
                </ul>

                <div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Verificación de Medicamentos
                    </div>
                    <p class="mb-0">Siempre verifique las dosis, interacciones y contraindicaciones antes de prescribir. El sistema puede mostrar alertas de interacciones medicamentosas si están configuradas.</p>
                </div>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_medicines.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 7: Medical Orders -->
            <div class="step-card">
                <h4><span class="step-number">7</span><span class="step-title">Generar Órdenes Médicas</span></h4>
                <p>Si necesita solicitar exámenes o procedimientos:</p>
                <ul>
                    <li>Abra la sección <strong>"Órdenes Médicas"</strong></li>
                    <li>Seleccione el tipo de orden:
                        <ul>
                            <li>Laboratorio</li>
                            <li>Imágenes diagnósticas</li>
                            <li>Procedimientos</li>
                            <li>Interconsultas</li>
                        </ul>
                    </li>
                    <li>Busque y seleccione los estudios requeridos</li>
                    <li>Agregue indicaciones especiales si es necesario</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_lab.png') }}" alt="" style="width: 100%;">
            </div>
            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_img.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 8: Treatment Plan -->
            <div class="step-card">
                <h4><span class="step-number">8</span><span class="step-title">Definir Plan de Tratamiento</span></h4>
                <p>Complete el plan de manejo del paciente:</p>
                <ul>
                    <li><strong>Indicaciones generales:</strong> Reposo, dieta, actividad física</li>
                    <li><strong>Recomendaciones:</strong> Cuidados en casa, signos de alarma</li>
                    <li><strong>Seguimiento:</strong> Cuándo debe regresar a control</li>
                    <li><strong>Educación al paciente:</strong> Información sobre su condición</li>
                </ul>
            </div>
        </section>

        <!-- Section 5: Finish Consultation -->
        <section id="finalizar" class="content-section">
            <h2><i class="fas fa-check-circle me-2"></i>5. Finalizar la Consulta</h2>

            <p>Una vez que haya documentado toda la información necesaria, debe finalizar formalmente la consulta:</p>

            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Revisar la Información</span></h4>
                <p>Antes de finalizar, revise que toda la información esté completa:</p>
                <ul>
                    <li>Motivo de consulta documentado</li>
                    <li>Signos vitales registrados</li>
                    <li>Examen físico completado</li>
                    <li>Diagnósticos agregados</li>
                    <li>Prescripciones y órdenes generadas</li>
                    <li>Plan de tratamiento definido</li>
                </ul>
            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Generar Documentos</span></h4>
                <p>El sistema puede generar automáticamente varios documentos:</p>
                <ul>
                    <li><strong>Resumen de Consulta:</strong> Documento completo con toda la información</li>
                    <li><strong>Receta Médica:</strong> Prescripción para el paciente</li>
                    <li><strong>Órdenes Médicas:</strong> Solicitudes de exámenes y procedimientos</li>
                    <li><strong>Incapacidad Médica:</strong> Si aplica</li>
                </ul>

                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-file-pdf text-success"></i>
                        Documentos en PDF
                    </div>
                    <p class="mb-0">Todos los documentos se generan en formato PDF con su firma y sello digital (si están configurados). Puede descargarlos, imprimirlos o enviarlos por correo electrónico al paciente.</p>
                </div>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_finished.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Finalizar Consulta</span></h4>
                <p>Para cerrar formalmente la consulta:</p>
                <ul>
                    <li>Haga clic en el botón <strong>"Finalizar Consulta"</strong> en la parte superior o inferior de la página</li>
                    <li>Confirme que desea finalizar la consulta</li>
                    <li>El sistema actualizará el estado de la cita a <strong>"Fulfilled"</strong> (Completada)</li>
                    <li>La información quedará guardada en la historia clínica del paciente</li>
                </ul>

                <div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Importante
                    </div>
                    <p class="mb-0">Una vez finalizada la consulta, no podrá editar la información. Asegúrese de que todo esté correcto antes de finalizar. Si necesita hacer cambios posteriores, deberá contactar al administrador del sistema.</p>
                </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Después de Finalizar</span></h4>
                <p>Después de finalizar la consulta, puede:</p>
                <ul>
                    <li>Ver el resumen completo de la consulta</li>
                    <li>Descargar los documentos generados</li>
                    <li>Enviar la receta al paciente por WhatsApp o correo</li>
                    <li>Agendar una cita de seguimiento si es necesario</li>
                    <li>Regresar al calendario para atender al siguiente paciente</li>
                </ul>
            </div>
        </section>

        <!-- Section 6: Tips and Best Practices -->
        <section id="tips" class="content-section">
            <h2><i class="fas fa-lightbulb me-2"></i>6. Tips y Mejores Prácticas</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Buenas Prácticas
                        </div>
                        <ul class="mb-0">
                            <li><strong>Sea específico:</strong> Documente de manera clara y detallada</li>
                            <li><strong>Use plantillas:</strong> Configure plantillas para agilizar el proceso</li>
                            <li><strong>Revise la historia:</strong> Consulte encuentros previos antes de iniciar</li>
                            <li><strong>Actualice diagnósticos:</strong> Use códigos CIE-10 actualizados</li>
                            <li><strong>Verifique medicamentos:</strong> Confirme dosis y contraindicaciones</li>
                            <li><strong>Documente todo:</strong> Incluya hallazgos negativos relevantes</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box warning">
                        <div class="info-box-title">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Errores Comunes a Evitar
                        </div>
                        <ul class="mb-0">
                            <li>No documentar signos vitales</li>
                            <li>Omitir el examen físico</li>
                            <li>No especificar diagnósticos con CIE-10</li>
                            <li>Prescribir sin indicar dosis o duración</li>
                            <li>Finalizar sin revisar la información</li>
                            <li>No generar los documentos para el paciente</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--<h3 class="mt-4">Atajos de Teclado</h3>
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Atajo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><kbd>Ctrl</kbd> + <kbd>S</kbd></td>
                        <td>Guardar cambios manualmente</td>
                    </tr>
                    <tr>
                        <td><kbd>Ctrl</kbd> + <kbd>F</kbd></td>
                        <td>Buscar en diagnósticos o medicamentos</td>
                    </tr>
                    <tr>
                        <td><kbd>Esc</kbd></td>
                        <td>Cerrar modal o diálogo abierto</td>
                    </tr>
                </tbody>
            </table>-->

            <!--<h3 class="mt-4">Teleconsultas</h3>
            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-video text-primary"></i>
                    Consultas Virtuales
                </div>
                <p>Si la cita es virtual, verá un panel lateral con la videollamada integrada:</p>
                <ul class="mb-0">
                    <li>La sala de videollamada se abre automáticamente al iniciar la consulta</li>
                    <li>Puede minimizar/maximizar el video según necesite</li>
                    <li>El paciente recibe un enlace único para unirse a la llamada</li>
                    <li>Puede compartir pantalla si necesita mostrar algo al paciente</li>
                    <li>La videollamada se desconecta automáticamente al finalizar la consulta</li>
                </ul>
            </div> -->

            <h3 class="mt-4">Preguntas Frecuentes</h3>

            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Qué pasa si cierro la ventana sin finalizar la consulta?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            No se preocupe, toda la información se guarda automáticamente. Puede volver a abrir la consulta más tarde y continuará donde la dejó. Sin embargo, la cita permanecerá en estado "En Consulta" hasta que la finalice formalmente.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Puedo editar una consulta después de finalizarla?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Por razones de auditoría médica y legal, las consultas finalizadas no pueden editarse directamente. Si necesita hacer correcciones, debe contactar al administrador del sistema o crear una nota de enmienda en el siguiente encuentro.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Cómo personalizo las secciones que aparecen?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Las secciones se configuran en <a href="{{ route('help.settings') }}">Configuraciones → Plantillas de Consulta</a>. Puede crear plantillas personalizadas según su especialidad médica. Contacte a su administrador si necesita ayuda con esta configuración.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Los documentos generados tienen validez legal?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí, todos los documentos generados incluyen su firma y sello digital (si están configurados) y cumplen con los estándares legales para documentos médicos electrónicos. Asegúrese de tener su firma y sello cargados en el sistema.
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-box tip mt-4">
                <div class="info-box-title">
                    <i class="fas fa-question-circle text-success"></i>
                    ¿Necesita Más Ayuda?
                </div>
                <p class="mb-0">Si tiene preguntas adicionales o necesita asistencia, contacte al equipo de soporte de SAMI o consulte con el administrador de su clínica.</p>
            </div>
        </section>

            </div> <!-- End col-lg-8 -->
        </div> <!-- End row -->

        <!-- Navigation Footer -->
        <div class="row mt-5">
            <div class="col-md-6">
                <a href="{{ route('help.appointments') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Anterior: Agendamiento de Citas
                </a>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    </main>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });

        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
