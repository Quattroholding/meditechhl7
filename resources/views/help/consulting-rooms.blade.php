<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Consultorios - Centro de Ayuda SAMI</title>
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
            background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
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
            border-left: 5px solid var(--info-color);
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
            background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(2, 136, 209, 0.4);
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #01579b;
            margin-bottom: 15px;
            padding-left: 45px;
        }

        .step-content {
            color: #555;
            line-height: 1.8;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
            border: 3px dashed #4fc3f7;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: #29b6f6;
            margin-bottom: 15px;
        }

        .screenshot-placeholder h5 {
            color: #0277bd;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .screenshot-placeholder p {
            color: #4fc3f7;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .screenshot-placeholder .dimensions {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 0.75rem;
            color: #81d4fa;
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
            background: #01579b;
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

        /* Relationship Diagram */
        .relationship-diagram {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
        }

        .relationship-row {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .relationship-box {
            background: #fff;
            border: 3px solid;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            min-width: 150px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .relationship-box.branch {
            border-color: #43a047;
        }

        .relationship-box.room {
            border-color: #0288d1;
        }

        .relationship-box i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .relationship-box.branch i { color: #43a047; }
        .relationship-box.room i { color: #0288d1; }

        .relationship-box h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .relationship-box p {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        .relationship-arrow {
            font-size: 2rem;
            color: #ccc;
        }

        .rooms-group {
            display: flex;
            flex-direction: column;
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

        /* Sub-steps */
        .sub-step {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 3px solid #0288d1;
        }

        .sub-step h6 {
            color: #01579b;
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
            color: #0288d1;
            margin-top: 3px;
        }

        /* Example Card */
        .example-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .example-card h6 {
            color: #0277bd;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .example-card .example-item {
            background: #fff;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .example-card .example-item:last-child {
            margin-bottom: 0;
        }

        .example-card .example-item i {
            color: #0288d1;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(2, 136, 209, 0.4);
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

            .relationship-row {
                flex-direction: column;
            }

            .relationship-arrow {
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
   @include('help.sidebar', ['active' => 'consulting-rooms'])

    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Guias de Configuracion</a></li>
                <li class="breadcrumb-item active">Crear Consultorios</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-door-open me-3"></i>Guia para Crear Consultorios</h1>
            <p>Aprende a configurar los consultorios dentro de tus sucursales. Los consultorios son los espacios donde se realizan las consultas medicas y se atienden a los pacientes.</p>
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
                                Que es un Consultorio?
                            </a>
                        </li>
                        <li>
                            <a href="#relacion">
                                <i class="fas fa-link"></i>
                                Relacion con Sucursales
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
                                Paso 2: Nuevo Consultorio
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
                            <a href="#multiples">
                                <i class="fas fa-clone"></i>
                                Crear Multiples Consultorios
                            </a>
                        </li>
                        <li>
                            <a href="#editar">
                                <i class="fas fa-pencil-alt"></i>
                                Editar Consultorio
                            </a>
                        </li>
                        <li>
                            <a href="#eliminar">
                                <i class="fas fa-trash-alt"></i>
                                Eliminar Consultorio
                            </a>
                        </li>
                        <li>
                            <a href="#siguiente">
                                <i class="fas fa-arrow-right"></i>
                                Siguientes Pasos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Que es un Consultorio -->
                <section id="concepto" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Que es un Consultorio?</h3>
                    <div class="step-content">
                        <p>Un <strong>consultorio</strong> (o consulting room) es el <strong>espacio fisico</strong> dentro de una sucursal donde se realizan las consultas medicas. Es donde el medico atiende directamente a sus pacientes.</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Espacio de atencion</strong><br>
                                    <small class="text-muted">Donde se realizan las consultas y examenes</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Identificacion unica</strong><br>
                                    <small class="text-muted">Cada consultorio tiene nombre, numero y piso</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Asignacion de citas</strong><br>
                                    <small class="text-muted">Las citas se agendan en un consultorio especifico</small>
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-note">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>Importante para Citas:</strong> Para poder agendar citas en el sistema, necesitas tener al menos un consultorio configurado. Las citas se asocian a un consultorio especifico.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Relacion con Sucursales -->
                <section id="relacion" class="step-card step-important">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-link me-2"></i>Relacion Sucursal - Consultorio</h3>
                    <div class="step-content">
                        <p>Es fundamental entender la relacion entre sucursales y consultorios:</p>

                        <div class="relationship-diagram">
                            <div class="relationship-row">
                                <div class="relationship-box branch">
                                    <i class="fas fa-building"></i>
                                    <h6>Sucursal</h6>
                                    <p>"Sede Centro"</p>
                                </div>
                                <div class="relationship-arrow">
                                    <i class="fas fa-long-arrow-alt-right"></i>
                                </div>
                                <div class="rooms-group">
                                    <div class="relationship-box room">
                                        <i class="fas fa-door-open"></i>
                                        <h6>Consultorio 1</h6>
                                        <p>Piso 1</p>
                                    </div>
                                    <div class="relationship-box room">
                                        <i class="fas fa-door-open"></i>
                                        <h6>Consultorio 2</h6>
                                        <p>Piso 1</p>
                                    </div>
                                    <div class="relationship-box room">
                                        <i class="fas fa-door-open"></i>
                                        <h6>Consultorio 3</h6>
                                        <p>Piso 2</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="info-box info-tip">
                                    <i class="fas fa-check"></i>
                                    <div>
                                        <strong>Una sucursal puede tener:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Multiples consultorios</li>
                                            <li>Consultorios en diferentes pisos</li>
                                            <li>Sin limite de cantidad</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box info-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Cada consultorio:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Pertenece a UNA sola sucursal</li>
                                            <li>Debe tener sucursal asignada</li>
                                            <li>No puede existir sin sucursal</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-danger mt-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Requisito Obligatorio:</strong> Debes crear primero al menos una sucursal antes de poder crear consultorios. Si no tienes sucursales, ve a la <a href="{{ route('help.branches') }}">guia de sucursales</a> primero.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Requisitos Previos -->
                <section id="requisitos" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-clipboard-check me-2"></i>Requisitos Previos</h3>
                    <div class="step-content">
                        <p>Antes de crear un consultorio, asegurate de:</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Tener al menos una sucursal creada</strong><br>
                                    <small class="text-muted">Los consultorios se crean dentro de sucursales existentes</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Conocer la ubicacion fisica</strong><br>
                                    <small class="text-muted">Numero de consultorio, piso donde se encuentra</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Tener permisos de administrador</strong><br>
                                    <small class="text-muted">Permiso "rooms.create" para crear consultorios</small>
                                </div>
                            </li>
                        </ul>

                        <div class="example-card">
                            <h6><i class="fas fa-lightbulb me-2"></i>Ejemplos de Consultorios</h6>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <div>
                                    <strong>Consultorio de Medicina General</strong> - Numero: 101 - Piso: 1
                                </div>
                            </div>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <div>
                                    <strong>Consultorio de Pediatria</strong> - Numero: 102 - Piso: 1
                                </div>
                            </div>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <div>
                                    <strong>Sala de Procedimientos</strong> - Numero: 201 - Piso: 2
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 1: Acceder al Modulo -->
                <section id="paso-1" class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Acceder al Modulo de Consultorios</h3>
                    <div class="step-content">
                        <p>Para acceder al modulo de consultorios:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-route me-2"></i>Navegacion</h6>
                            <ol>
                                <li>Inicia sesion en tu cuenta SAMI</li>
                                <li>En el menu lateral, busca <strong>"Clientes"</strong> o <strong>"Configuracion"</strong></li>
                                <li>Haz clic en <strong>"Consultorios"</strong></li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/create.png') }}" alt="" style="width: 100%;">
                        </div>

                        <p class="mt-3">URL directa:</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL Directa</h6>
                            <code class="d-block p-2 bg-dark text-light rounded">{{ config('app.url') }}/clients/consulting_rooms</code>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/index.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Paso 2: Nuevo Consultorio -->
                <section id="paso-2" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Iniciar Creacion de Nuevo Consultorio</h3>
                    <div class="step-content">
                        <p>En la lista de consultorios, haz clic en el boton <strong>"Nuevo Consultorio"</strong> o <strong>"Agregar"</strong>:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/new.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Sin sucursales?</strong> Si el sistema te muestra un mensaje de error o no puedes seleccionar una sucursal, significa que no tienes sucursales creadas. Primero <a href="{{ route('help.branches') }}">crea una sucursal</a>.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 3: Llenar Formulario -->
                <section id="paso-3" class="step-card step-important">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Completar el Formulario</h3>
                    <div class="step-content">
                        <p>El formulario de consultorio es sencillo pero todos los campos son obligatorios:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/form-crooms.png') }}" alt="" style="width: 100%;">
                        </div>


                        <h5 class="mt-4 mb-3">Campos del Formulario:</h5>
                        <table class="field-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripcion</th>
                                    <th>Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Sucursal</strong></td>
                                    <td>
                                        Selecciona la sucursal a la que pertenecera este consultorio.
                                        <br><small class="text-danger">Este campo es crucial - determina donde se ubica fisicamente el consultorio.</small>
                                    </td>
                                    <td>"Sede Centro"</td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre</strong></td>
                                    <td>Nombre descriptivo del consultorio. Puede indicar su uso o especialidad.</td>
                                    <td>"Consultorio de Cardiologia", "Sala de Procedimientos", "Consultorio 1"</td>
                                </tr>
                                <tr>
                                    <td><strong>Numero</strong></td>
                                    <td>Numero o codigo identificador del consultorio dentro de la sucursal.</td>
                                    <td>"101", "A-1", "C1"</td>
                                </tr>
                                <tr>
                                    <td><strong>Piso</strong></td>
                                    <td>Nivel o piso donde se encuentra el consultorio.</td>
                                    <td>"1", "Planta Baja", "2do Piso"</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Consejos para nombrar consultorios:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Usa nombres descriptivos que ayuden a identificarlos facilmente</li>
                                    <li>Si tienes multiples consultorios similares, usa numeracion: "Consultorio 1", "Consultorio 2"</li>
                                    <li>Puedes indicar la especialidad: "Consultorio Pediatria", "Sala Dermatologia"</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/filled.png') }}" alt="" style="width: 100%;">
                        </div>

                    </div>
                </section>

                <!-- Paso 4: Guardar -->
                <section id="paso-4" class="step-card step-success">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Guardar el Consultorio</h3>
                    <div class="step-content">
                        <p>Una vez completados todos los campos:</p>

                        <ol>
                            <li>Verifica que todos los datos sean correctos</li>
                            <li>Asegurate de haber seleccionado la sucursal correcta</li>
                            <li>Haz clic en el boton <strong>"Guardar"</strong></li>
                            <li>Espera la confirmacion del sistema</li>
                        </ol>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/save.png') }}" alt="" style="width: 100%;">
                        </div>


                        <div class="info-box info-tip">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Exito!</strong> Tu consultorio ha sido creado y ahora aparece en la lista. Ya puedes asignar citas a este consultorio.
                            </div>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/saved.png') }}" alt="" style="width: 100%;">
                        </div>

                    </div>
                </section>

                <!-- Crear Multiples Consultorios -->
                <section id="multiples" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-clone me-2"></i>Crear Multiples Consultorios</h3>
                    <div class="step-content">
                        <p>Si tu sucursal tiene varios consultorios, puedes crearlos uno por uno siguiendo el mismo proceso:</p>

                        <div class="example-card">
                            <h6><i class="fas fa-building me-2"></i>Ejemplo: Clinica con 4 Consultorios</h6>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <span><strong>Consultorio 1</strong> - Numero: 101 - Piso: 1 - Medicina General</span>
                            </div>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <span><strong>Consultorio 2</strong> - Numero: 102 - Piso: 1 - Pediatria</span>
                            </div>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <span><strong>Consultorio 3</strong> - Numero: 201 - Piso: 2 - Ginecologia</span>
                            </div>
                            <div class="example-item">
                                <i class="fas fa-door-open"></i>
                                <span><strong>Sala de Procedimientos</strong> - Numero: 202 - Piso: 2</span>
                            </div>
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Organizacion Sugerida:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Usa una nomenclatura consistente para los numeros (101, 102, 201, 202...)</li>
                                    <li>Agrupa consultorios similares en el mismo piso cuando sea posible</li>
                                    <li>Identifica claramente las salas de procedimientos o consultorios especiales</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Editar Consultorio -->
                <section id="editar" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-pencil-alt me-2"></i>Editar un Consultorio</h3>
                    <div class="step-content">
                        <p>Para modificar los datos de un consultorio existente:</p>

                        <ol>
                            <li>Ve a la lista de consultorios</li>
                            <li>Encuentra el consultorio que deseas editar</li>
                            <li>Haz clic en el boton de <strong>editar</strong> (icono de lapiz)</li>
                            <li>Modifica los campos necesarios</li>
                            <li>Haz clic en <strong>"Actualizar"</strong></li>
                        </ol>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/update.png') }}" alt="" style="width: 100%;">
                        </div>


                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Cambiar de Sucursal:</strong> Ten cuidado al cambiar la sucursal de un consultorio, ya que esto podria afectar citas existentes asociadas a ese consultorio.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Eliminar Consultorio -->
                <section id="eliminar" class="step-card step-important">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-trash-alt me-2"></i>Eliminar un Consultorio</h3>
                    <div class="step-content">
                        <p>Para eliminar un consultorio:</p>

                        <ol>
                            <li>Ve a la lista de consultorios</li>
                            <li>Encuentra el consultorio que deseas eliminar</li>
                            <li>Haz clic en el boton de <strong>eliminar</strong> (icono de papelera)</li>
                            <li>Confirma la eliminacion en el dialogo</li>
                        </ol>

                        <div class="info-box info-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Advertencia:</strong> Ten cuidado al eliminar consultorios. Si existen citas asociadas a este consultorio, podrian verse afectadas. Se recomienda:
                                <ul class="mb-0 mt-2">
                                    <li>Verificar que no haya citas pendientes en el consultorio</li>
                                    <li>Reasignar citas a otros consultorios si es necesario</li>
                                    <li>Considerar desactivar en lugar de eliminar</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/consulting_rooms/delete.png') }}" alt="" style="width: 100%;">
                        </div>

                    </div>
                </section>

                <!-- Siguientes Pasos -->
                <section id="siguiente" class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguientes Pasos</h3>
                    <div class="step-content">
                        <p>Felicitaciones! Ya tienes configurada tu estructura basica: <strong>Cliente > Sucursal > Consultorio</strong>. Ahora puedes continuar con:</p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-clock fa-2x text-warning"></i>
                                        </div>
                                        <h5 class="card-title">Configurar Horarios</h5>
                                        <p class="card-text text-muted small">Define tus horarios de trabajo para cada sucursal</p>
                                        <span class="badge bg-secondary">Proximamente</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                        <h5 class="card-title">Registrar Pacientes</h5>
                                        <p class="card-text text-muted small">Comienza a registrar a tus pacientes</p>
                                        <span class="badge bg-secondary">Proximamente</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-calendar-plus fa-2x text-success"></i>
                                        </div>
                                        <h5 class="card-title">Agendar Citas</h5>
                                        <p class="card-text text-muted small">Agenda tu primera cita en el consultorio</p>
                                        <span class="badge bg-secondary">Proximamente</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-stethoscope fa-2x text-danger"></i>
                                        </div>
                                        <h5 class="card-title">Realizar Consultas</h5>
                                        <p class="card-text text-muted small">Documenta tus consultas medicas</p>
                                        <span class="badge bg-secondary">Proximamente</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-check-double"></i>
                            <div>
                                <strong>Resumen de lo Completado:</strong>
                                <ol class="mb-0 mt-2">
                                    <li><i class="fas fa-check text-success me-1"></i> Registro de cuenta <a href="{{ route('help.registration') }}">(Ver guia)</a></li>
                                    <li><i class="fas fa-check text-success me-1"></i> Creacion de sucursal <a href="{{ route('help.branches') }}">(Ver guia)</a></li>
                                    <li><i class="fas fa-check text-success me-1"></i> Creacion de consultorio <strong>(Esta guia)</strong></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Contact Support -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2 text-info"></i>Tienes preguntas sobre los consultorios?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="mailto:soporte@sami.com" class="btn btn-info text-white">
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
