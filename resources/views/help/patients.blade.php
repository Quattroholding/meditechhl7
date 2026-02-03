<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Registro de Pacientes - Centro de Ayuda SAMI</title>
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
            --purple-color: #6f42c1;
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
            background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%);
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
            border-left: 5px solid var(--purple-color);
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
            background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(111, 66, 193, 0.4);
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #4a148c;
            margin-bottom: 15px;
            padding-left: 45px;
        }

        .step-content {
            color: #555;
            line-height: 1.8;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            border: 3px dashed #ba68c8;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: #ab47bc;
            margin-bottom: 15px;
        }

        .screenshot-placeholder h5 {
            color: #7b1fa2;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .screenshot-placeholder p {
            color: #ba68c8;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .screenshot-placeholder .dimensions {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 0.75rem;
            color: #ce93d8;
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
            background: #4a148c;
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

        /* Table of Contents */
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #4a148c;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3e5f5;
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
            color: #6f42c1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #f3e5f5;
            color: #4a148c;
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
            border-left: 3px solid #6f42c1;
        }

        .sub-step h6 {
            color: #4a148c;
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
            color: #6f42c1;
            margin-top: 3px;
        }

        /* ID Type Cards */
        .id-type-card {
            background: #fff;
            border: 2px solid #e1bee7;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            height: 100%;
        }

        .id-type-card .code {
            font-size: 1.5rem;
            font-weight: 700;
            color: #6f42c1;
            margin-bottom: 5px;
        }

        .id-type-card .name {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }

        .id-type-card .format {
            font-family: monospace;
            background: #f3e5f5;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            color: #7b1fa2;
        }

        /* Form Section Card */
        .form-section-card {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .form-section-card h6 {
            color: #4a148c;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-card ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .form-section-card li {
            color: #555;
            margin-bottom: 5px;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
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
        }

        /* Extra small devices */
        @media screen and (max-width: 480px) {
            .id-cards{
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .id-card {
                width: 100%;
            }
                        /*Títulos de pasos*/

            .step-title {
                padding-left: 0;
                padding-top: 5%;

            }
            .step-number {
                left: 45%;
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
                text-align: left;
                padding: 10px 12px;
                font-size: 14px;
                border-bottom: 1px solid #f0f0f0;
            }
            
            .field-table td:last-child {
                border-bottom: none;
            }
            
            /* Campo (título de cada tarjeta) */
            .field-table td[data-label="Campo"] {
                background: #4a148c;
                color: white;
                font-size: 15px;
                padding: 12px;
                border-bottom: 2px solid #5913ae;
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
            .field-table td[data-label="Requerido"] {
                background: #f5f5f5;
                padding: 12px 15px;
                text-align: center;
                font-weight: 600;
            }
            
            .field-table td[data-label="Requerido"]:before {
                content: "Requerido: ";
                font-weight: 600;
                color: #4a148c;
                margin-right: 5px;
            }

            .step-card, .sub-step {
                border-left: none;
                padding: 25px;
                border-bottom: 5px solid var(--purple-color);
            }
                        .step-title {
                padding-left: 0;
                text-align: center;
                padding-top: 8%;
            }
            .info-box, .form-section-card h6 {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }


    /* Ajustar badges en móvil */
    .required,
    .optional {
        min-width: 80px;
        text-align: center;
    }

        }

                 @media screen and (max-width: 400px) {
            .step-number{
                left: 44%;
            }
         }
        @media screen and (max-width: 350px) {
            .step-number{
                left: 43%;
            }
         }
                 @media screen and (max-width: 325px) {
            .step-number{
                left: 42%;
            }
         }
        
        
    </style>
</head>
<body>
   @include('help.sidebar', ['active' => 'patients'])

    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Gestion de Pacientes</a></li>
                <li class="breadcrumb-item active">Registrar Pacientes</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-user-injured me-3"></i>Guia para Registrar Pacientes</h1>
            <p>Aprende a registrar nuevos pacientes en el sistema SAMI. Incluye informacion personal, contacto de emergencia, documentos y relaciones familiares.</p>
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
                                Sobre los Pacientes
                            </a>
                        </li>
                        <li>
                            <a href="#tipos-id">
                                <i class="fas fa-id-card"></i>
                                Tipos de Identificacion
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
                                Paso 2: Nuevo Paciente
                            </a>
                        </li>
                        <li>
                            <a href="#paso-3">
                                <i class="fas fa-id-card-alt"></i>
                                Paso 3: Datos de Identificacion
                            </a>
                        </li>
                        <li>
                            <a href="#paso-4">
                                <i class="fas fa-user"></i>
                                Paso 4: Informacion Personal
                            </a>
                        </li>
                        <li>
                            <a href="#paso-5">
                                <i class="fas fa-map-marker-alt"></i>
                                Paso 5: Direccion
                            </a>
                        </li>
                        <li>
                            <a href="#paso-6">
                                <i class="fas fa-phone-alt"></i>
                                Paso 6: Contacto Emergencia
                            </a>
                        </li>
                        <li>
                            <a href="#paso-7">
                                <i class="fas fa-file-upload"></i>
                                Paso 7: Documentos
                            </a>
                        </li>
                        <!--<li>
                            <a href="#paso-8">
                                <i class="fas fa-users"></i>
                                Paso 8: Dependientes
                            </a>
                        </li>-->
                        <li>
                            <a href="#paso-9">
                                <i class="fas fa-save"></i>
                                Paso 8: Guardar
                            </a>
                        </li>
                        <li>
                            <a href="#asociar">
                                <i class="fas fa-link"></i>
                                Asociar Paciente Existente
                            </a>
                        </li>
                        <li>
                            <a href="#perfil">
                                <i class="fas fa-address-card"></i>
                                Ver Perfil del Paciente
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
                <!-- Sobre los Pacientes -->
                <section id="concepto" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Sobre los Pacientes en SAMI</h3>
                    <div class="step-content">
                        <p>En SAMI, los <strong>pacientes</strong> son el centro del sistema. Toda la informacion clínica, citas, consultas y documentos estan vinculados a cada paciente.</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Identificación única</strong><br>
                                    <small class="text-muted">Cada paciente se identifica por su número de documento</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Multi-clínica</strong><br>
                                    <small class="text-muted">Un paciente puede ser atendido en múltiples clínicas del sistema</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Relaciones familiares</strong><br>
                                    <small class="text-muted">Puedes registrar dependientes (hijos, conyuges, etc.)</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Cumplimiento FHIR</strong><br>
                                    <small class="text-muted">Datos compatibles con estándares internacionales de salud</small>
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Pacientes compartidos:</strong> Si un paciente ya existe en el sistema (registrado por otra clínica), puedes asociarlo a tu clínica sin duplicar su información.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tipos de Identificacion -->
                <section id="tipos-id" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-id-card me-2"></i>Tipos de Identificación</h3>
                    <div class="step-content">
                        <p>SAMI soporta diferentes tipos de documentos de identificación:</p>

                        <div class="row mt-4 id-cards">
                            <div class="id-card col-6 col-md-4 mb-3">
                                <div class="id-type-card">
                                    <div class="code">CC</div>
                                    <div class="name">Cédula de Ciudadanía</div>
                                    <div class="format">8-123-456</div>
                                </div>
                            </div>
                            <div class="id-card col-6 col-md-4 mb-3">
                                <div class="id-type-card">
                                    <div class="code">CE</div>
                                    <div class="name">Cédula de Extranjería</div>
                                    <div class="format">E-8-123456</div>
                                </div>
                            </div>
                            <div class="id-card col-6 col-md-4 mb-3">
                                <div class="id-type-card">
                                    <div class="code">PA</div>
                                    <div class="name">Pasaporte</div>
                                    <div class="format">PA1234567</div>
                                </div>
                            </div>
                            <div class="id-card col-6 col-md-4 mb-3">
                                <div class="id-type-card">
                                    <div class="code">PT</div>
                                    <div class="name">Permiso Temporal</div>
                                    <div class="format">PT-12345678</div>
                                </div>
                            </div>
                            <div class="id-card col-6 col-md-4 mb-3">
                                <div class="id-type-card">
                                    <div class="code">SS</div>
                                    <div class="name">Seguro Social</div>
                                    <div class="format">123-45-6789</div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Formato importante:</strong> Cada tipo de documento tiene un formato específico. El sistema validará que el numero ingresado coincida con el formato del tipo seleccionado.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 1: Acceder al Modulo -->
                <section id="paso-1" class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Acceder al Módulo de Pacientes</h3>
                    <div class="step-content">
                        <p>Para acceder al módulo de pacientes:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-route me-2"></i>Navegación</h6>
                            <ol>
                                <li>Inicia sesión en tu cuenta SAMI</li>
                                <li>En el menú lateral, busca <strong>"Pacientes"</strong></li>
                                <li>Haz clic para ver la lista de pacientes</li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/create-patient.png') }}" alt="" style="width: 100%;">
                        </div>

                        {{--}}<p class="mt-3">URL directa:</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL Directa</h6>
                            <code class="d-block p-2 bg-dark text-light rounded">{{ config('app.url') }}/patients</code>
                        </div>{{--}}

                        <div>
                            <img src="{{ asset('images/tutorial/patients/direct-url.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Paso 2: Nuevo Paciente -->
                <section id="paso-2" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Iniciar Registro de Nuevo Paciente</h3>
                    <div class="step-content">
                        <p>En la lista de pacientes, haz clic en el botón <strong>"Nuevo(a)"</strong>:</p>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Buscar primero:</strong> Antes de crear un nuevo paciente, usa el buscador para verificar que no exista ya en el sistema. Puedes buscar por nombre o numero de documento.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 3: Datos de Identificacion -->
                <section id="paso-3" class="step-card step-important">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Ingresar Datos de Identificación</h3>
                    <div class="step-content">
                        <p>La primera sección del formulario es la <strong>identificación del paciente</strong>:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/id-data.png') }}" alt="" style="width: 100%;">
                        </div>

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
                                    <td data-label="Campo"><strong>Tipo de Documento</strong></td>
                                    <td data-label="Descripción">Selecciona el tipo: CC, CE, PA, PT o SS</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Número de Documento</strong></td>
                                    <td data-label="Descripción">Número de identificacion (formato segun tipo)</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Género</strong></td>
                                    <td data-label="Descripción">Masculino o Femenino</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-note">
                            <i class="fas fa-search"></i>
                            <div>
                                <strong>Verificación automática:</strong> Al ingresar el número de documento, el sistema verificará automáticamente si el paciente ya existe. Si existe, te dará la opción de asociarlo a tu clínica.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 4: Informacion Personal -->
                <section id="paso-4" class="step-card">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Completar Información Personal</h3>
                    <div class="step-content">
                        <p>Ingresa los datos personales del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/personal-data.png') }}" alt="" style="width: 100%;">
                        </div>

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
                                    <td data-label="Campo"><strong>Nombre</strong></td>
                                    <td data-label="Descripción">Nombre(s) del paciente</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Apellido</strong></td>
                                    <td data-label="Descripción">Apellido(s) del paciente</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Email</strong></td>
                                    <td data-label="Descripción">Correo electrónico (único en el sistema)</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Teléfono</strong></td>
                                    <td data-label="Descripción">Número de teléfono con código de país</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Fecha de Nacimiento</strong></td>
                                    <td data-label="Descripción">Fecha en formato día/mes/año</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Tipo de Sangre</strong></td>
                                    <td data-label="Descripción">A+, A-, B+, B-, AB+, AB-, O+, O-</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Estado Civil</strong></td>
                                    <td data-label="Descripción">Soltero, Casado, Divorciado, Viudo</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Email único:</strong> El correo electrónico debe ser único. Si otro paciente ya tiene ese email, deberás usar uno diferente.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 5: Direccion -->
                <section id="paso-5" class="step-card">
                    <div class="step-number">5</div>
                    <h3 class="step-title">Ingresar Dirección</h3>
                    <div class="step-content">
                        <p>Completa la información de ubicación del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/address-data.png') }}" alt="" style="width: 100%;">
                        </div>

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
                                    <td data-label="Campo"><strong>Dirección Física</strong></td>
                                    <td data-label="Descripción">Dirección completa de residencia</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>País</strong></td>
                                    <td data-label="Descripción">País de residencia</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Provincia/Estado</strong></td>
                                    <td data-label="Descripción">División administrativa (se carga segun el país)</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Paso 6: Contacto de Emergencia -->
                <section id="paso-6" class="step-card">
                    <div class="step-number">6</div>
                    <h3 class="step-title">Contacto de Emergencia (Opcional)</h3>
                    <div class="step-content">
                        <p>Puedes agregar información de un contacto de emergencia:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/emergency-contact.png') }}" alt="" style="width: 100%;">
                        </div>

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
                                    <td data-label="Campo"><strong>Nombre del Contacto</strong></td>
                                    <td data-label="Descripción">Nombre completo de la persona de emergencia</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Email del Contacto</strong></td>
                                    <td data-label="Descripción">Correo electrónico del contacto</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Teléfono del Contacto</strong></td>
                                    <td data-label="Descripción">Numero de teléfono del contacto</td>
                                    <td data-label="Requerido"><span class="optional">Opcional</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Recomendación:</strong> Aunque es opcional, es muy útil tener un contacto de emergencia para casos de urgencia médica.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 7: Documentos -->
                <section id="paso-7" class="step-card">
                    <div class="step-number">7</div>
                    <h3 class="step-title">Subir Documentos (Opcional)</h3>
                    <div class="step-content">
                        <p>Puedes adjuntar documentos del paciente como identificación, seguros, exámenes previos, etc:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/file-data.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Formatos Aceptados:</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        <div><strong>PDF</strong> - Documentos</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-file-word text-primary"></i>
                                        <div><strong>DOC/DOCX</strong> - Word</div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-file-image text-success"></i>
                                        <div><strong>JPG/PNG</strong> - Imágenes</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-weight"></i>
                                        <div><strong>Max 1MB</strong> por archivo</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 8: Dependientes 
                <section id="paso-8" class="step-card step-important">
                    <div class="step-number">8</div>
                    <h3 class="step-title">Configurar Dependientes (Opcional)</h3>
                    <div class="step-content">
                        <p>Si el paciente es dependiente de otro (ej: hijo de un paciente existente), puedes establecer esa relacion:</p>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #9</h5>
                            <p>Seccion de dependientes con checkbox "Es dependiente de otro paciente", selector de paciente principal y tipo de relacion</p>
                            <span class="dimensions">Recomendado: 1200x500px</span>
                        </div>

                        <h5 class="mt-4">Tipos de Relacion:</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-child text-purple me-2"></i> Hijo/a</li>
                                    <li><i class="fas fa-ring text-purple me-2"></i> Conyuge</li>
                                    <li><i class="fas fa-user text-purple me-2"></i> Padre/Madre</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-users text-purple me-2"></i> Hermano/a</li>
                                    <li><i class="fas fa-heart text-purple me-2"></i> Pareja</li>
                                    <li><i class="fas fa-baby text-purple me-2"></i> Nieto/a</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-user-tie text-purple me-2"></i> Abuelo/a</li>
                                </ul>
                            </div>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Opciones adicionales al marcar como dependiente:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Copiar seguro:</strong> Copia la poliza de seguro del paciente principal</li>
                                    <li><strong>Contacto de emergencia:</strong> Marca al paciente principal como contacto de emergencia</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section> -->

                <!-- Paso 9: Guardar -->
                <section id="paso-9" class="step-card step-success">
                    <div class="step-number">8</div>
                    <h3 class="step-title">Guardar el Paciente</h3>
                    <div class="step-content">
                        <p>Una vez completada toda la informacion:</p>

                        <ol>
                            <li>Revisa que todos los datos obligatorios esten completos</li>
                            <li>Verifica que la información sea correcta</li>
                            <li>Haz clic en el botón <strong>"Guardar"</strong></li>
                            <li>Espera la confirmación del sistema</li>
                        </ol>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/save-patient.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Exito!</strong> El paciente ha sido registrado. Ahora puedes:
                                <ul class="mb-0 mt-2">
                                    <li>Ver su perfil completo</li>
                                    <li>Agendar una cita</li>
                                    <li>Registrar su historia médica</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/saved-patient.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Asociar Paciente Existente -->
                <section id="asociar" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-link me-2"></i>Asociar Paciente Existente</h3>
                    <div class="step-content">
                        <p>Si el paciente ya existe en el sistema (fue registrado por otra clinica), puedes asociarlo a tu clínica:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-search me-2"></i>Proceso de Asociacion</h6>
                            <ol>
                                <li>Al ingresar el numero de documento, el sistema detectara que ya existe</li>
                                <li>Aparecera un mensaje indicando que el paciente ya esta registrado</li>
                                <li>Haz clic en <strong>"Asociar a mi clínica"</strong></li>
                                <li>El paciente quedará vinculado a tu organización</li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/asociar.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Beneficios de asociar:</strong> Al asociar un paciente existente, tienes acceso a todo su historial médico previo (con las autorizaciones correspondientes), evitando duplicar información.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Ver Perfil del Paciente -->
                <section id="perfil" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-address-card me-2"></i>Ver Perfil del Paciente</h3>
                    <div class="step-content">
                        <p>Despues de crear o asociar un paciente, puedes ver su perfil completo:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/patients/profile.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">El perfil incluye:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-section-card">
                                    <h6><i class="fas fa-user"></i> Información Personal</h6>
                                    <ul>
                                        <li>Datos de identificación</li>
                                        <li>Información de contacto</li>
                                        <li>Dirección</li>
                                        <li>Contacto de emergencia</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-section-card">
                                    <h6><i class="fas fa-notes-medical"></i> Información Clinica</h6>
                                    <ul>
                                        <li>Historia médica</li>
                                        <li>Citas programadas</li>
                                        <li>Consultas realizadas</li>
                                        <li>Documentos adjuntos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Siguiente Paso -->
                <section id="siguiente" class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguiente Paso: Historia Médica</h3>
                    <div class="step-content">
                        <p>Ahora que tienes pacientes registrados, el siguiente paso es aprender a gestionar su <strong>historia médica</strong>.</p>

                        <div class="info-box info-note">
                            <i class="fas fa-notes-medical"></i>
                            <div>
                                <strong>La historia médica incluye:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Antecedentes personales y familiares</li>
                                    <li>Alergias y condiciones</li>
                                    <li>Signos vitales</li>
                                    <li>Diagnósticos (con códigos ICD-10)</li>
                                    <li>Medicamentos y tratamientos</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('help.medical-history') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%);">
                                <i class="fas fa-notes-medical me-2"></i>Continuar: Historia Médica
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Contact Support -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #6f42c1;"></i>¿Tienes preguntas sobre el registro de pacientes?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="mailto:soporte@sami.com" class="btn text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #4a148c 100%);">
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
