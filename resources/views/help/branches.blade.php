<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Sucursales - Centro de Ayuda SAMI</title>
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

        /* Module Header */
        .module-header {
            background: linear-gradient(135deg, #43a047 0%, #1b5e20 100%);
            color: #fff;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
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

        /* Step Cards */
        .step-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid var(--success-color);
            position: relative;
        }

        .step-card.step-important {
            border-left-color: var(--warning-color);
        }

        .step-card.step-success {
            border-left-color: var(--success-color);
        }

        .step-card.step-info {
            border-left-color: var(--info-color);
        }

        .step-card.step-primary {
            border-left-color: var(--primary-color);
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #43a047 0%, #1b5e20 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(67, 160, 71, 0.4);
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 15px;
            padding-left: 45px;
        }

        .step-content {
            color: #555;
            line-height: 1.8;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border: 3px dashed #81c784;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: #66bb6a;
            margin-bottom: 15px;
        }

        .screenshot-placeholder h5 {
            color: #2e7d32;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .screenshot-placeholder p {
            color: #81c784;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .screenshot-placeholder .dimensions {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 0.75rem;
            color: #a5d6a7;
        }

        /* Info Boxes */
        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .info-box i {
            font-size: 1.5rem;
            margin-top: 3px;
        }

        .info-box.info-note {
            background: #e3f2fd;
            border: 1px solid #90caf9;
        }

        .info-box.info-note i {
            color: #1976d2;
        }

        .info-box.info-warning {
            background: #fff3e0;
            border: 1px solid #ffcc80;
        }

        .info-box.info-warning i {
            color: #f57c00;
        }

        .info-box.info-tip {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
        }

        .info-box.info-tip i {
            color: #388e3c;
        }

        .info-box.info-danger {
            background: #ffebee;
            border: 1px solid #ef9a9a;
        }

        .info-box.info-danger i {
            color: #d32f2f;
        }

        /* Field Table */
        .field-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .field-table th {
            background: #1b5e20;
            color: #fff;
            padding: 15px;
            font-weight: 600;
            text-align: left;
        }

        .field-table th:first-child {
            border-radius: 10px 0 0 0;
        }

        .field-table th:last-child {
            border-radius: 0 10px 0 0;
        }

        .field-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
        }

        .field-table tr:last-child td:first-child {
            border-radius: 0 0 0 10px;
        }

        .field-table tr:last-child td:last-child {
            border-radius: 0 0 10px 0;
        }

        .field-table .required {
            color: #d32f2f;
            font-weight: 600;
        }

        .field-table .optional {
            color: #757575;
        }

        /* Hierarchy Diagram */
        .hierarchy-diagram {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
            text-align: center;
        }

        .hierarchy-level {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin: 0 20px;
        }

        .hierarchy-box {
            background: #fff;
            border: 3px solid;
            border-radius: 12px;
            padding: 20px 30px;
            min-width: 180px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .hierarchy-box.client {
            border-color: #1a237e;
        }

        .hierarchy-box.branch {
            border-color: #43a047;
        }

        .hierarchy-box.room {
            border-color: #0288d1;
        }

        .hierarchy-box i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .hierarchy-box.client i { color: #1a237e; }
        .hierarchy-box.branch i { color: #43a047; }
        .hierarchy-box.room i { color: #0288d1; }

        .hierarchy-box h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .hierarchy-box p {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        .hierarchy-connector {
            width: 3px;
            height: 30px;
            background: #ccc;
            margin: 10px auto;
        }

        .hierarchy-connector.horizontal {
            width: 100px;
            height: 3px;
            display: inline-block;
            vertical-align: middle;
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
            color: #1b5e20;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8f5e9;
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
            color: #43a047;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #e8f5e9;
            color: #1b5e20;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }

        /* Sub-steps */
        .sub-step {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 3px solid #43a047;
        }

        .sub-step h6 {
            color: #1b5e20;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Checklist */
        .checklist {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }

        .checklist li {
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .checklist li:last-child {
            border-bottom: none;
        }

        .checklist li i {
            color: #43a047;
            margin-top: 3px;
        }

        /* Branch Type Cards */
        .branch-type-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            height: 100%;
            border-top: 4px solid #43a047;
        }

        .branch-type-card i {
            font-size: 2.5rem;
            color: #43a047;
            margin-bottom: 15px;
        }

        .branch-type-card h5 {
            color: #1b5e20;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .branch-type-card .rooms-count {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #43a047 0%, #1b5e20 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(67, 160, 71, 0.4);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
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

        /* Print Styles */
        @media print {
            .help-sidebar,
            .back-to-top {
                display: none;
            }

            .help-content {
                margin-left: 0;
            }

            .screenshot-placeholder {
                page-break-inside: avoid;
            }

            .step-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="help-sidebar">
        <div class="logo">
            <img src="{{ asset('assets/img/logo-sami.png') }}" alt="SAMI Logo" onerror="this.style.display='none'">
            <h4>Centro de Ayuda</h4>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Inicio</div>
            <a href="{{ route('help.index') }}" class="nav-link">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Guias de Registro</div>
            <a href="{{ route('help.registration') }}" class="nav-link">
                <i class="fas fa-user-plus"></i>
                <span>Registro de Cliente</span>
            </a>
            <a href="{{ route('help.branches') }}" class="nav-link active">
                <i class="fas fa-building"></i>
                <span>Crear Sucursales</span>
            </a>
            <a href="{{ route('help.consulting-rooms') }}" class="nav-link">
                <i class="fas fa-door-open"></i>
                <span>Crear Consultorios</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Gestion de Pacientes</div>
            <a href="{{ route('help.patients') }}" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Registrar Pacientes</span>
            </a>
            <a href="{{ route('help.medical-history') }}" class="nav-link">
                <i class="fas fa-notes-medical"></i>
                <span>Historia Medica</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Citas</div>
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-plus"></i>
                <span>Agendar Citas</span>
                <span class="badge bg-secondary">Pronto</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-check"></i>
                <span>Gestionar Citas</span>
                <span class="badge bg-secondary">Pronto</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Consultas</div>
            <a href="#" class="nav-link">
                <i class="fas fa-stethoscope"></i>
                <span>Realizar Consulta</span>
                <span class="badge bg-secondary">Pronto</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-prescription"></i>
                <span>Crear Recetas</span>
                <span class="badge bg-secondary">Pronto</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Soporte</div>
            <a href="#" class="nav-link">
                <i class="fas fa-question-circle"></i>
                <span>Preguntas Frecuentes</span>
                <span class="badge bg-secondary">Pronto</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-headset"></i>
                <span>Contactar Soporte</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Guias de Configuracion</a></li>
                <li class="breadcrumb-item active">Crear Sucursales</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-building me-3"></i>Guia para Crear Sucursales</h1>
            <p>Aprende a configurar las sucursales de tu clinica o consultorio. Las sucursales representan las ubicaciones fisicas donde atenderas a tus pacientes.</p>
        </div>

        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guia</h5>
                    <ul class="toc-list">
                        <li>
                            <a href="#concepto">
                                <i class="fas fa-info-circle"></i>
                                Que es una Sucursal?
                            </a>
                        </li>
                        <li>
                            <a href="#jerarquia">
                                <i class="fas fa-sitemap"></i>
                                Jerarquia del Sistema
                            </a>
                        </li>
                        <li>
                            <a href="#tipos">
                                <i class="fas fa-tags"></i>
                                Tipos de Sucursales
                            </a>
                        </li>
                        <li>
                            <a href="#requisitos">
                                <i class="fas fa-clipboard-check"></i>
                                Requisitos Previos
                            </a>
                        </li>
                        <li>
                            <a href="#paso-1">
                                <i class="fas fa-mouse-pointer"></i>
                                Paso 1: Acceder al Modulo
                            </a>
                        </li>
                        <li>
                            <a href="#paso-2">
                                <i class="fas fa-plus-circle"></i>
                                Paso 2: Nueva Sucursal
                            </a>
                        </li>
                        <li>
                            <a href="#paso-3">
                                <i class="fas fa-edit"></i>
                                Paso 3: Llenar Formulario
                            </a>
                        </li>
                        <li>
                            <a href="#paso-4">
                                <i class="fas fa-save"></i>
                                Paso 4: Guardar
                            </a>
                        </li>
                        <li>
                            <a href="#editar">
                                <i class="fas fa-pencil-alt"></i>
                                Editar Sucursal
                            </a>
                        </li>
                        <li>
                            <a href="#eliminar">
                                <i class="fas fa-trash-alt"></i>
                                Eliminar Sucursal
                            </a>
                        </li>
                        <li>
                            <a href="#siguiente">
                                <i class="fas fa-arrow-right"></i>
                                Siguiente Paso
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Que es una Sucursal -->
                <section id="concepto" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Que es una Sucursal?</h3>
                    <div class="step-content">
                        <p>Una <strong>sucursal</strong> (o branch) representa una <strong>ubicacion fisica</strong> donde tu clinica o consultorio ofrece servicios medicos. Puede ser:</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Tu consultorio principal</strong><br>
                                    <small class="text-muted">La ubicacion donde atiendes regularmente</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Una clinica o centro medico</strong><br>
                                    <small class="text-muted">Instalaciones con multiples areas de atencion</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Sucursales adicionales</strong><br>
                                    <small class="text-muted">Otras ubicaciones donde tambien brindas servicios</small>
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> Cada sucursal puede contener <strong>multiples consultorios</strong>. Debes crear al menos una sucursal antes de poder crear consultorios.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Jerarquia del Sistema -->
                <section id="jerarquia" class="step-card step-primary">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-sitemap me-2"></i>Jerarquia del Sistema</h3>
                    <div class="step-content">
                        <p>El sistema SAMI utiliza una estructura jerarquica de tres niveles para organizar tu practica medica:</p>

                        <div class="hierarchy-diagram">
                            <div class="hierarchy-level">
                                <div class="hierarchy-box client">
                                    <i class="fas fa-hospital"></i>
                                    <h6>Cliente</h6>
                                    <p>Tu Clinica/Organizacion</p>
                                </div>
                                <div class="hierarchy-connector"></div>
                                <div class="hierarchy-box branch">
                                    <i class="fas fa-building"></i>
                                    <h6>Sucursal</h6>
                                    <p>Ubicacion Fisica</p>
                                </div>
                                <div class="hierarchy-connector"></div>
                                <div class="hierarchy-box room">
                                    <i class="fas fa-door-open"></i>
                                    <h6>Consultorio</h6>
                                    <p>Espacio de Atencion</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <span class="badge bg-primary fs-6 mb-2">Nivel 1</span>
                                    <h6>Cliente</h6>
                                    <p class="small text-muted">Se crea automaticamente al registrarte. Representa tu organizacion.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <span class="badge bg-success fs-6 mb-2">Nivel 2</span>
                                    <h6>Sucursal</h6>
                                    <p class="small text-muted"><strong>Estas aqui.</strong> Crea tus ubicaciones fisicas.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <span class="badge bg-info fs-6 mb-2">Nivel 3</span>
                                    <h6>Consultorio</h6>
                                    <p class="small text-muted">Siguiente paso. Espacios dentro de cada sucursal.</p>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Ejemplo:</strong> "Clinica San Jose" (Cliente) tiene "Sede Centro" y "Sede Norte" (Sucursales). Cada sede tiene "Consultorio 1", "Consultorio 2", etc.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tipos de Sucursales -->
                <section id="tipos" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-tags me-2"></i>Tipos de Sucursales</h3>
                    <div class="step-content">
                        <p>SAMI ofrece diferentes tipos de sucursales segun el tamano de tu instalacion:</p>

                        <div class="row mt-4">
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="branch-type-card">
                                    <i class="fas fa-user-md"></i>
                                    <h5>Consultorio</h5>
                                    <p class="text-muted small mb-3">Practica individual o pequena</p>
                                    <span class="rooms-count">1 consultorio</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="branch-type-card">
                                    <i class="fas fa-clinic-medical"></i>
                                    <h5>Centro de Salud</h5>
                                    <p class="text-muted small mb-3">Atencion primaria</p>
                                    <span class="rooms-count">2 consultorios</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="branch-type-card">
                                    <i class="fas fa-hospital-alt"></i>
                                    <h5>Clinica</h5>
                                    <p class="text-muted small mb-3">Multiples especialidades</p>
                                    <span class="rooms-count">5 consultorios</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="branch-type-card">
                                    <i class="fas fa-hospital"></i>
                                    <h5>Hospital</h5>
                                    <p class="text-muted small mb-3">Gran infraestructura</p>
                                    <span class="rooms-count">10+ consultorios</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Nota:</strong> El tipo de sucursal es solo una clasificacion. Puedes crear tantos consultorios como necesites independientemente del tipo seleccionado.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Requisitos Previos -->
                <section id="requisitos" class="step-card step-important">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-clipboard-check me-2"></i>Requisitos Previos</h3>
                    <div class="step-content">
                        <p>Antes de crear una sucursal, asegurate de:</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Haber completado el registro</strong><br>
                                    <small class="text-muted">Tu cuenta debe estar activa con una suscripcion valida</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Tener permisos de administrador</strong><br>
                                    <small class="text-muted">Rol de "admin client" o "doctor" con permisos de configuracion</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Conocer los datos de la ubicacion</strong><br>
                                    <small class="text-muted">Nombre, direccion, telefono de la sucursal</small>
                                </div>
                            </li>
                        </ul>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #1</h5>
                            <p>Dashboard principal mostrando la notificacion de "Configuracion inicial requerida" que aparece despues del registro</p>
                            <span class="dimensions">Recomendado: 1200x600px</span>
                        </div>
                    </div>
                </section>

                <!-- Paso 1: Acceder al Modulo -->
                <section id="paso-1" class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Acceder al Modulo de Sucursales</h3>
                    <div class="step-content">
                        <p>Para acceder al modulo de sucursales, sigue estos pasos:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-route me-2"></i>Navegacion</h6>
                            <ol>
                                <li>Inicia sesion en tu cuenta SAMI</li>
                                <li>En el menu lateral, busca <strong>"Clientes"</strong> o <strong>"Configuracion"</strong></li>
                                <li>Haz clic en <strong>"Sucursales"</strong></li>
                            </ol>
                        </div>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #2</h5>
                            <p>Menu lateral con la opcion "Sucursales" resaltada dentro de la seccion de Clientes</p>
                            <span class="dimensions">Recomendado: 400x600px</span>
                        </div>

                        <p class="mt-3">Tambien puedes acceder directamente via URL:</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL Directa</h6>
                            <code class="d-block p-2 bg-dark text-light rounded">{{ config('app.url') }}/clients/branch</code>
                        </div>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #3</h5>
                            <p>Lista de sucursales (puede estar vacia si es la primera vez). Mostrar la tabla con columnas: ID, Nombre, Telefono, Direccion, Tipo, Acciones</p>
                            <span class="dimensions">Recomendado: 1200x600px</span>
                        </div>
                    </div>
                </section>

                <!-- Paso 2: Nueva Sucursal -->
                <section id="paso-2" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Iniciar Creacion de Nueva Sucursal</h3>
                    <div class="step-content">
                        <p>En la lista de sucursales, haz clic en el boton <strong>"Nueva Sucursal"</strong> o <strong>"Agregar"</strong>:</p>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #4</h5>
                            <p>Boton "Nueva Sucursal" resaltado en la parte superior de la lista de sucursales</p>
                            <span class="dimensions">Recomendado: 1200x400px</span>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Consejo:</strong> Si tienes multiples clinicas (clientes), asegurate de tener seleccionado el cliente correcto antes de crear la sucursal.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 3: Llenar Formulario -->
                <section id="paso-3" class="step-card step-important">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Completar el Formulario</h3>
                    <div class="step-content">
                        <p>Llena todos los campos del formulario de creacion de sucursal:</p>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #5</h5>
                            <p>Formulario completo de creacion de sucursal mostrando todos los campos</p>
                            <span class="dimensions">Recomendado: 1200x800px</span>
                        </div>

                        <h5 class="mt-4 mb-3">Campos del Formulario:</h5>
                        <table class="field-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripcion</th>
                                    <th>Requerido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Cliente</strong></td>
                                    <td>Selecciona a que clinica/organizacion pertenece esta sucursal. Si solo tienes una, se selecciona automaticamente.</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo</strong></td>
                                    <td>
                                        Clasificacion del establecimiento:
                                        <ul class="mb-0 mt-1">
                                            <li>Consultorio</li>
                                            <li>Centro de Salud</li>
                                            <li>Clinica</li>
                                            <li>Hospital</li>
                                        </ul>
                                    </td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre</strong></td>
                                    <td>Nombre identificativo de la sucursal. Ej: "Sede Central", "Consultorio Norte", "Clinica Las Palmas"</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Telefono</strong></td>
                                    <td>Numero de contacto de la sucursal con codigo de pais (+507, +1, etc.)</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Direccion</strong></td>
                                    <td>Direccion fisica completa de la ubicacion</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Pais</strong></td>
                                    <td>Pais donde se encuentra la sucursal</td>
                                    <td><span class="optional">Opcional</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Provincia/Estado</strong></td>
                                    <td>Division administrativa (se carga automaticamente al seleccionar pais)</td>
                                    <td><span class="optional">Opcional</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Sobre el Telefono:</strong> Ingresa el numero con el formato internacional. El sistema incluye un selector de pais para facilitar el ingreso.
                            </div>
                        </div>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #6</h5>
                            <p>Campo de telefono con el selector de codigo de pais desplegado</p>
                            <span class="dimensions">Recomendado: 600x400px</span>
                        </div>
                    </div>
                </section>

                <!-- Paso 4: Guardar -->
                <section id="paso-4" class="step-card step-success">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Guardar la Sucursal</h3>
                    <div class="step-content">
                        <p>Una vez completados todos los campos requeridos:</p>

                        <ol>
                            <li>Revisa que toda la informacion sea correcta</li>
                            <li>Haz clic en el boton <strong>"Guardar"</strong> o <strong>"Crear Sucursal"</strong></li>
                            <li>Espera la confirmacion del sistema</li>
                        </ol>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #7</h5>
                            <p>Boton "Guardar" resaltado en el formulario y/o mensaje de exito despues de crear la sucursal</p>
                            <span class="dimensions">Recomendado: 1200x400px</span>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Exito!</strong> Tu sucursal ha sido creada. Ahora aparecera en la lista de sucursales y podras crear consultorios dentro de ella.
                            </div>
                        </div>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #8</h5>
                            <p>Lista de sucursales mostrando la nueva sucursal recien creada</p>
                            <span class="dimensions">Recomendado: 1200x500px</span>
                        </div>
                    </div>
                </section>

                <!-- Editar Sucursal -->
                <section id="editar" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-pencil-alt me-2"></i>Editar una Sucursal</h3>
                    <div class="step-content">
                        <p>Si necesitas modificar los datos de una sucursal existente:</p>

                        <ol>
                            <li>Ve a la lista de sucursales</li>
                            <li>Encuentra la sucursal que deseas editar</li>
                            <li>Haz clic en el boton de <strong>editar</strong> (icono de lapiz) en la columna de acciones</li>
                            <li>Modifica los campos necesarios</li>
                            <li>Haz clic en <strong>"Actualizar"</strong></li>
                        </ol>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #9</h5>
                            <p>Fila de la tabla con el boton de editar resaltado</p>
                            <span class="dimensions">Recomendado: 1200x300px</span>
                        </div>
                    </div>
                </section>

                <!-- Eliminar Sucursal -->
                <section id="eliminar" class="step-card step-important">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-trash-alt me-2"></i>Eliminar una Sucursal</h3>
                    <div class="step-content">
                        <p>Para eliminar una sucursal:</p>

                        <ol>
                            <li>Ve a la lista de sucursales</li>
                            <li>Encuentra la sucursal que deseas eliminar</li>
                            <li>Haz clic en el boton de <strong>eliminar</strong> (icono de papelera)</li>
                            <li>Confirma la eliminacion en el dialogo</li>
                        </ol>

                        <div class="info-box info-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Advertencia:</strong> No podras eliminar una sucursal que tenga consultorios asociados. Primero debes eliminar o reasignar los consultorios.
                            </div>
                        </div>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #10</h5>
                            <p>Dialogo de confirmacion de eliminacion de sucursal</p>
                            <span class="dimensions">Recomendado: 600x400px</span>
                        </div>
                    </div>
                </section>

                <!-- Siguiente Paso -->
                <section id="siguiente" class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguiente Paso: Crear Consultorios</h3>
                    <div class="step-content">
                        <p>Ahora que tienes tu sucursal creada, el siguiente paso es crear los <strong>consultorios</strong> dentro de ella.</p>

                        <div class="info-box info-note">
                            <i class="fas fa-door-open"></i>
                            <div>
                                <strong>Recuerda:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Cada sucursal puede tener <strong>multiples consultorios</strong></li>
                                    <li>Los consultorios son los espacios fisicos donde se realizan las consultas</li>
                                    <li>Para agendar citas, necesitas al menos un consultorio configurado</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('help.consulting-rooms') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-door-open me-2"></i>Continuar: Crear Consultorios
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Contact Support -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2 text-success"></i>Tienes preguntas sobre las sucursales?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="mailto:soporte@sami.com" class="btn btn-success">
                                    <i class="fas fa-envelope me-2"></i>Contactar Soporte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to Top functionality
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

        // Smooth scroll for TOC links
        document.querySelectorAll('.toc-list a').forEach(link => {
            link.addEventListener('click', function(e) {
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
