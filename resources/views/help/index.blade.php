<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @auth
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @endauth
    @livewireStyles
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 5px solid var(--primary-color);
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

        .step-number {
            position: absolute;
            top: -15px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 15px;
            padding-left: 45px;
        }

        .step-content {
            color: #555;
            line-height: 1.8;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
            border: 3px dashed #7986cb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: #5c6bc0;
            margin-bottom: 15px;
        }

        .screenshot-placeholder h5 {
            color: #3949ab;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .screenshot-placeholder p {
            color: #7986cb;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .screenshot-placeholder .dimensions {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 0.75rem;
            color: #9fa8da;
        }

        /* Actual Screenshot Container */
        .screenshot-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            margin: 25px 0;
        }

        .screenshot-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .screenshot-caption {
            background: #f8f9fa;
            padding: 12px 20px;
            font-size: 0.85rem;
            color: #666;
            border-top: 1px solid #e9ecef;
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
            background: #1a237e;
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

        /* Flow Diagram */
        .flow-diagram {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 15px;
            margin: 25px 0;
        }

        .flow-step {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            min-width: 150px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            position: relative;
        }

        .flow-step i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .flow-step h6 {
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }

        .flow-step p {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        .flow-arrow {
            display: flex;
            align-items: center;
            color: #9fa8da;
            font-size: 1.5rem;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3e0;
            color: #e65100;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-trial {
            background: #e3f2fd;
            color: #1565c0;
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
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8eaf6;
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
            color: #5c6bc0;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #e8eaf6;
            color: #1a237e;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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

            .flow-diagram {
                flex-direction: column;
            }

            .flow-arrow {
                transform: rotate(90deg);
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

   @include('help.sidebar', ['active' => 'index'])


    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Centro de Ayuda</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-life-ring me-3"></i>Centro de Ayuda SAMI</h1>
            <p>Bienvenido al centro de ayuda. Aqui encontraras guias detalladas paso a paso para utilizar todas las funcionalidades del sistema.</p>
        </div>

        <!-- Quick Start Grid -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(103, 58, 183, 0.1); color: #673ab7;">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <h5 class="card-title">Roles y Permisos</h5>
                        <p class="card-text text-muted">Gestiona los accesos y funciones permitidas para cada usuario.</p>
                        <a href="{{ route('help.roles') }}" class="btn text-white" style="background: #673ab7;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-plus fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Registro de Cliente</h5>
                        <p class="card-text text-muted">Aprende como ser un usuario de SAMI: seleccionar el plan que adaptado a tu necesidad, como registrar tu pago, conocer el asistente de configuración y mucho más.</p>
                        <a href="{{ route('help.registration') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-building fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Configurar Sucursales</h5>
                        <p class="card-text text-muted">Configura tus sucursales para empezar a atender pacientes.</p>
                        <a href="{{ route('help.branches') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-door-open fa-2x text-info"></i>
                        </div>
                        <h5 class="card-title">Crear Consultorios</h5>
                        <p class="card-text text-muted">Configura los consultorios dentro de tus sucursales.</p>
                        <a href="{{ route('help.consulting-rooms') }}" class="btn btn-info text-white">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(85, 172, 238, 0.1); color: #55acee;">
                            <i class="fas fa-user-md fa-2x"></i>
                        </div>
                        <h5 class="card-title">Doctores</h5>
                        <p class="card-text text-muted">Gestiona el personal médico y sus perfiles profesionales.</p>
                        <a href="{{ route('help.doctors') }}" class="btn text-white" style="background: #55acee;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(111, 66, 193, 0.1);">
                            <i class="fas fa-user-injured fa-2x" style="color: #6f42c1;"></i>
                        </div>
                        <h5 class="card-title">Registrar Pacientes</h5>
                        <p class="card-text text-muted">Aprende a registrar y gestionar pacientes.</p>
                        <a href="{{ route('help.patients') }}" class="btn text-white" style="background: #6f42c1;">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(0, 137, 123, 0.1);">
                            <i class="fas fa-notes-medical fa-2x" style="color: #00897b;"></i>
                        </div>
                        <h5 class="card-title">Historia Medica</h5>
                        <p class="card-text text-muted">Gestiona el historial clinico de tus pacientes.</p>
                        <a href="{{ route('help.medical-history') }}" class="btn text-white" style="background: #00897b;">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(230, 81, 0, 0.1);">
                            <i class="fas fa-calendar-check fa-2x" style="color: #e65100;"></i>
                        </div>
                        <h5 class="card-title">Agendamiento de Citas</h5>
                        <p class="card-text text-muted">Domina el sistema de agendamiento de citas.</p>
                        <a href="{{ route('help.appointments') }}" class="btn text-white" style="background: #e65100;">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(94, 53, 177, 0.1);">
                            <i class="fas fa-cogs fa-2x" style="color: #5e35b1;"></i>
                        </div>
                        <h5 class="card-title">Configuraciones</h5>
                        <p class="card-text text-muted">Configura servicios, horarios y plantillas.</p>
                        <a href="{{ route('help.settings') }}" class="btn text-white" style="background: #5e35b1;">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-stethoscope fa-2x text-danger"></i>
                        </div>
                        <h5 class="card-title">Realizar Consultas</h5>
                        <p class="card-text text-muted">Aprende a documentar consultas medicas.</p>
                        <a href="{{ route('help.consultation') }}" class="btn btn-danger">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-file-invoice-dollar fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Crear Facturas</h5>
                        <p class="card-text text-muted">Aprende a generar facturas desde consultas.</p>
                        <a href="{{ route('help.billing') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Procesar Pagos</h5>
                        <p class="card-text text-muted">Gestiona pagos de facturas de pacientes.</p>
                        <a href="{{ route('help.payments') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>



            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(40, 53, 147, 0.1);">
                            <i class="fas fa-tachometer-alt fa-2x" style="color: #283593;"></i>
                        </div>
                        <h5 class="card-title">Dashboard Médico</h5>
                        <p class="card-text text-muted">Aprende a usar y personalizar tu panel principal.</p>
                        <a href="{{ route('help.doctor-dashboard') }}" class="btn text-white" style="background: #283593;">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(2, 136, 209, 0.1); color: #0288d1;">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <h5 class="card-title">Perfil de Usuario</h5>
                        <p class="card-text text-muted">Gestiona tu información personal, seguridad y firma digital.</p>
                        <a href="{{ route('help.profile') }}" class="btn text-white" style="background: #0288d1;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(26, 35, 126, 0.1); color: #1a237e;">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h5 class="card-title">Seguridad 2FA</h5>
                        <p class="card-text text-muted">Aprende a configurar la autenticación de dos factores para proteger tu cuenta.</p>
                        <a href="{{ route('help.2fa') }}" class="btn text-white" style="background: #1a237e;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-crown fa-2x text-warning"></i>
                        </div>
                        <h5 class="card-title">Mi Suscripción</h5>
                        <p class="card-text text-muted">Gestiona tu plan y pagos mensuales.</p>
                        <a href="{{ route('help.subscriptions') }}" class="btn btn-warning">
                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(108, 117, 125, 0.1); color: #6c757d;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="card-title">Gestión de Usuarios</h5>
                        <p class="card-text text-muted">Controla el acceso y perfiles de todo el personal administrativo.</p>
                        <a href="{{ route('help.users') }}" class="btn text-white" style="background: #6c757d;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>



            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(0, 137, 123, 0.1); color: #00897b;">
                            <i class="fas fa-microscope fa-2x"></i>
                        </div>
                        <h5 class="card-title">Repositorio de Estudios</h5>
                        <p class="card-text text-muted">Gestiona y consulta todas las solicitudes de laboratorios, imágenes y procedimientos.</p>
                        <a href="{{ route('help.service-requests') }}" class="btn text-white" style="background: #00897b;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(103, 58, 183, 0.1); color: #673ab7;">
                            <i class="fas fa-pills fa-2x"></i>
                        </div>
                        <h5 class="card-title">Medicamentos</h5>
                        <p class="card-text text-muted">Aprende a gestionar el catálogo de medicamentos y sus detalles técnicos.</p>
                        <a href="{{ route('help.medicines') }}" class="btn text-white" style="background: #673ab7;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(26, 35, 126, 0.1); color: #1a237e;">
                            <i class="fas fa-address-book fa-2x"></i>
                        </div>
                        <h5 class="card-title">Directorio Médico</h5>
                        <p class="card-text text-muted">Consulta la información detallada de los profesionales y solicita citas.</p>
                        <a href="{{ route('help.medical-directory') }}" class="btn text-white" style="background: #1a237e;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(25, 135, 84, 0.1); color: #198754;">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                        <h5 class="card-title">Soporte y Contacto</h5>
                        <p class="card-text text-muted">¿Necesitas ayuda técnica? Contacta con nuestro equipo.</p>
                        <a href="{{ route('help.support') }}" class="btn text-white" style="background: #198754;"><i class="fas fa-arrow-right me-2"></i>Ver Guia</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2"><i class="fas fa-headset me-2 text-primary"></i>Necesitas ayuda adicional?</h5>
                        <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte con cualquier duda o problema, haz clic en nuestra sección de Soporte y Contacto.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="https://wa.me/5078316174" target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
                        </a>
                    </div>
                </div>
                    {{--}}<div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @auth
                            <button type="button" class="btn btn-primary" onclick="Livewire.dispatch('openTicketModal')">
                                <i class="fas fa-ticket-alt me-2"></i>Crear Ticket de Soporte
                            </button>
                        @else
                            <a href="mailto:bussines@meditecpty.com" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Contactar Soporte
                            </a>
                        @endauth
                    </div>{{--}}
                </div>
            </div>
        </div>
    </main>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modal de Ticket (solo para usuarios autenticados) -->
    @auth
        @livewire('ticket-system.modal-save')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @endauth
    @livewireScripts
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

        // Livewire event listener para notificaciones toastr
        @auth
        document.addEventListener('livewire:init', () => {
            Livewire.on('showToastr', (event) => {
                // Si existe toastr, usarlo
                if (typeof toastr !== 'undefined') {
                    toastr[event.type](event.message);
                } else {
                    // Fallback a alert si no hay toastr
                    alert(event.message);
                }
            });
        });
        @endauth
    </script>
</body>
</html>
