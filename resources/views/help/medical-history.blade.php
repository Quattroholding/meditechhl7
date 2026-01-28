<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Historia Medica - Centro de Ayuda SAMI</title>
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
            --teal-color: #20c997;
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
            background: linear-gradient(135deg, #00897b 0%, #004d40 100%);
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
            border-left: 5px solid var(--teal-color);
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

        .step-card.step-danger {
            border-left-color: var(--danger-color);
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00897b 0%, #004d40 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(0, 137, 123, 0.4);
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #004d40;
            margin-bottom: 15px;
            padding-left: 45px;
        }

        .step-content {
            color: #555;
            line-height: 1.8;
        }

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            border: 3px dashed #4db6ac;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .screenshot-placeholder i {
            font-size: 3rem;
            color: #26a69a;
            margin-bottom: 15px;
        }

        .screenshot-placeholder h5 {
            color: #00796b;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .screenshot-placeholder p {
            color: #4db6ac;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .screenshot-placeholder .dimensions {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 0.75rem;
            color: #80cbc4;
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

        /* Table of Contents */
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #004d40;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0f2f1;
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
            color: #00897b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #e0f2f1;
            color: #004d40;
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
            border-left: 3px solid #00897b;
        }

        .sub-step h6 {
            color: #004d40;
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
            color: #00897b;
            margin-top: 3px;
        }

        /* Section Cards */
        .section-card {
            background: #fff;
            border: 2px solid #b2dfdb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            border-color: #00897b;
            box-shadow: 0 5px 15px rgba(0, 137, 123, 0.2);
        }

        .section-card i {
            font-size: 2.5rem;
            color: #00897b;
            margin-bottom: 15px;
        }

        .section-card h6 {
            color: #004d40;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .section-card p {
            font-size: 0.85rem;
            color: #666;
            margin: 0;
        }

        /* Vital Signs Table */
        .vital-signs-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .vital-signs-table th {
            background: #004d40;
            color: #fff;
            padding: 12px 15px;
            font-weight: 600;
            text-align: left;
        }

        .vital-signs-table th:first-child {
            border-radius: 10px 0 0 0;
        }

        .vital-signs-table th:last-child {
            border-radius: 0 10px 0 0;
        }

        .vital-signs-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
        }

        .vital-signs-table tr:last-child td:first-child {
            border-radius: 0 0 0 10px;
        }

        .vital-signs-table tr:last-child td:last-child {
            border-radius: 0 0 10px 0;
        }

        .vital-signs-table .code {
            font-family: monospace;
            background: #e0f2f1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #00796b;
        }

        /* Category Badge */
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .category-allergy { background: #ffebee; color: #c62828; }
        .category-surgery { background: #e3f2fd; color: #1565c0; }
        .category-family { background: #f3e5f5; color: #7b1fa2; }
        .category-social { background: #fff3e0; color: #ef6c00; }
        .category-medication { background: #e8f5e9; color: #2e7d32; }
        .category-condition { background: #e0f7fa; color: #00838f; }

        /* ICD Code Example */
        .icd-example {
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .icd-example h6 {
            color: #004d40;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .icd-code-box {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .icd-code-box:last-child {
            margin-bottom: 0;
        }

        .icd-code-box .code {
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #00897b;
            background: #e0f2f1;
            padding: 8px 15px;
            border-radius: 6px;
            min-width: 80px;
            text-align: center;
        }

        .icd-code-box .description {
            color: #555;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00897b 0%, #004d40 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 137, 123, 0.4);
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
    </style>
</head>
<body>
    @include('help.sidebar', ['active' => 'medical-history'])

    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Gestion de Pacientes</a></li>
                <li class="breadcrumb-item active">Historia Medica</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-notes-medical me-3"></i>Guia de Historia Medica</h1>
            <p>Aprende a visualizar y gestionar la historia medica completa de tus pacientes, incluyendo antecedentes, diagnosticos, signos vitales y mas.</p>
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
                                Que es la Historia Medica?
                            </a>
                        </li>
                        <li>
                            <a href="#secciones">
                                <i class="fas fa-th-large"></i>
                                Secciones Disponibles
                            </a>
                        </li>
                        <li>
                            <a href="#acceder">
                                <i class="fas fa-mouse-pointer"></i>
                                Como Acceder
                            </a>
                        </li>
                        <li>
                            <a href="#resumen">
                                <i class="fas fa-chart-pie"></i>
                                Vista General (Resumen)
                            </a>
                        </li>
                        <li>
                            <a href="#antecedentes">
                                <i class="fas fa-history"></i>
                                Antecedentes Medicos
                            </a>
                        </li>
                        <li>
                            <a href="#condiciones">
                                <i class="fas fa-diagnoses"></i>
                                Condiciones/Diagnosticos
                            </a>
                        </li>
                        <li>
                            <a href="#signos-vitales">
                                <i class="fas fa-heartbeat"></i>
                                Signos Vitales
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#examenes">
                                <i class="fas fa-stethoscope"></i>
                                Examenes Fisicos
                            </a>
                        </li> -->
                        <li>
                            <a href="#medicamentos">
                                <i class="fas fa-pills"></i>
                                Órdenes Médicas
                            </a>
                        </li>
                        <li>
                            <a href="#notas">
                                <i class="fas fa-sticky-note"></i>
                                Notas Clinicas
                            </a>
                        </li>
                        <li>
                            <a href="#licencias">
                                <i class="fas fa-file-medical"></i>
                                Licencias Medicas
                            </a>
                        </li>
                        <li>
                            <a href="#agregar">
                                <i class="fas fa-plus-circle"></i>
                                Agregar Antecedentes
                            </a>
                        </li>
                       <!-- <li>
                            <a href="#filtros">
                                <i class="fas fa-filter"></i>
                                Filtros y Busqueda
                            </a>
                        </li> -->
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Que es la Historia Medica -->
                <section id="concepto" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Que es la Historia Medica?</h3>
                    <div class="step-content">
                        <p>La <strong>historia medica</strong> (o historial clinico) es el registro completo de toda la informacion de salud de un paciente. En SAMI, la historia medica esta organizada en multiples secciones para facilitar el acceso a la informacion.</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Registro cronologico</strong><br>
                                    <small class="text-muted">Toda la informacion esta ordenada por fecha</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Codigos estandarizados</strong><br>
                                    <small class="text-muted">Diagnosticos con codigos ICD-10, signos vitales con LOINC</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Agrupacion por encuentro</strong><br>
                                    <small class="text-muted">Los datos se pueden ver agrupados por consulta</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Cumplimiento FHIR</strong><br>
                                    <small class="text-muted">Compatible con estandares internacionales de interoperabilidad</small>
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-note">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>Privacidad:</strong> Los medicos solo pueden ver los registros de las consultas que ellos han realizado, a menos que tengan autorizacion especial del paciente o del administrador.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Secciones Disponibles -->
                <section id="secciones" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-th-large me-2"></i>Secciones de la Historia Medica</h3>
                    <div class="step-content">
                        <p>La historia medica esta dividida en las siguientes secciones:</p>

                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-chart-pie"></i>
                                    <h6>Vista General</h6>
                                    <p>Resumen y estadisticas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-calendar-check"></i>
                                    <h6>Encuentros</h6>
                                    <p>Consultas realizadas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-heartbeat"></i>
                                    <h6>Signos Vitales</h6>
                                    <p>Mediciones vitales</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-stethoscope"></i>
                                    <h6>Examenes Fisicos</h6>
                                    <p>Hallazgos de examen</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-diagnoses"></i>
                                    <h6>Condiciones</h6>
                                    <p>Diagnosticos ICD-10</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-notes-medical"></i>
                                    <h6>Enfermedades Actuales</h6>
                                    <p>Motivo de consulta</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-pills"></i>
                                    <h6>Medicamentos</h6>
                                    <p>Prescripciones</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-flask"></i>
                                    <h6>Solicitudes</h6>
                                    <p>Labs e imagenes</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-history"></i>
                                    <h6>Antecedentes</h6>
                                    <p>Historial medico</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-comment-medical"></i>
                                    <h6>Notas Clinicas</h6>
                                    <p>Impresiones medicas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-sticky-note"></i>
                                    <h6>Notas Personales</h6>
                                    <p>Notas del medico</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-file-medical"></i>
                                    <h6>Licencias Medicas</h6>
                                    <p>Incapacidades</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Como Acceder -->
                <section id="acceder" class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Como Acceder a la Historia Medica</h3>
                    <div class="step-content">
                        <p>Para acceder a la historia medica de un paciente:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-route me-2"></i>Pasos</h6>
                            <ol>
                                <li>Ve a la lista de pacientes o busca el paciente</li>
                                <li>Haz clic en el nombre del paciente o en <strong>"Ver Perfil"</strong></li>
                                <li>En el perfil, haz clic en <strong>"Historia Medica"</strong></li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/menu.png') }}" alt="" style="width: 100%;">
                        </div>

                        <p class="mt-3">URL directa:</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL Directa</h6>
                            <code class="d-block p-2 bg-dark text-light rounded">{{ config('app.url') }}/patients/{id}/medical_history</code>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/direct-mh.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Vista General -->
                <section id="resumen" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Vista General (Resumen)</h3>
                    <div class="step-content">
                        <p>La seccion de <strong>Vista General</strong> muestra un resumen rapido del estado del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/general.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Informacion Mostrada:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-chart-bar"></i>
                                        <div><strong>Total de encuentros</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-diagnoses"></i>
                                        <div><strong>Condiciones activas</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar"></i>
                                        <div><strong>Fecha de ultima visita</strong></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-pills"></i>
                                        <div><strong>Medicamentos activos</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-allergies"></i>
                                        <div><strong>Alergias conocidas</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-clock"></i>
                                        <div><strong>Actividad reciente</strong></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Antecedentes Medicos -->
                <section id="antecedentes" class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Antecedentes Medicos</h3>
                    <div class="step-content">
                        <p>Los <strong>antecedentes medicos</strong> registran eventos de salud historicos del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-prev.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Categorias de Antecedentes:</h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-allergy">Alergia</span>
                                    <span class="text-muted">Alergias a medicamentos, alimentos, etc.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-surgery">Cirugia</span>
                                    <span class="text-muted">Procedimientos quirurgicos previos</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-family">Historia Familiar</span>
                                    <span class="text-muted">Enfermedades hereditarias</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-social">Historia Social</span>
                                    <span class="text-muted">Habitos, ocupacion, estilo de vida</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-medication">Medicamentos</span>
                                    <span class="text-muted">Medicamentos previos relevantes</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-condition">Condicion</span>
                                    <span class="text-muted">Enfermedades cronicas previas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Condiciones/Diagnosticos -->
                <section id="condiciones" class="step-card step-important">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Condiciones / Diagnosticos</h3>
                    <div class="step-content">
                        <p>Las <strong>condiciones</strong> son los diagnosticos registrados durante las consultas, codificados con el estandar <strong>ICD-10</strong>:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/conditions.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Estados de una Condicion:</h5>
                        <div class="row mt-3">
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-danger">Activa</span> - Condicion presente
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-warning text-dark">Recurrencia</span> - Ha vuelto a aparecer
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-info">Remision</span> - Mejorando
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-success">Resuelta</span> - Ya no esta presente
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-secondary">Desconocido</span> - Estado no determinado
                            </div>
                        </div>

                        <div class="icd-example mt-4">
                            <h6><i class="fas fa-code me-2"></i>Ejemplos de Codigos ICD-10:</h6>
                            <div class="icd-code-box">
                                <span class="code">J06.9</span>
                                <span class="description">Infeccion aguda de las vias respiratorias superiores</span>
                            </div>
                            <div class="icd-code-box">
                                <span class="code">I10</span>
                                <span class="description">Hipertension esencial (primaria)</span>
                            </div>
                            <div class="icd-code-box">
                                <span class="code">E11.9</span>
                                <span class="description">Diabetes mellitus tipo 2 sin complicaciones</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Signos Vitales -->
                <section id="signos-vitales" class="step-card">
                    <div class="step-number">5</div>
                    <h3 class="step-title">Signos Vitales</h3>
                    <div class="step-content">
                        <p>Los <strong>signos vitales</strong> son las mediciones fisiologicas tomadas durante las consultas:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/vital-signs.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Signos Vitales Soportados:</h5>
                        <table class="vital-signs-table">
                            <thead>
                                <tr>
                                    <th>Signo Vital</th>
                                    <th>Codigo LOINC</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-heart text-danger me-2"></i> Presion Sistolica</td>
                                    <td><span class="code">8480-6</span></td>
                                    <td>mmHg</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-heart text-danger me-2"></i> Presion Diastolica</td>
                                    <td><span class="code">8462-4</span></td>
                                    <td>mmHg</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-heartbeat text-danger me-2"></i> Frecuencia Cardiaca</td>
                                    <td><span class="code">8867-4</span></td>
                                    <td>lpm</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-thermometer-half text-warning me-2"></i> Temperatura</td>
                                    <td><span class="code">8310-5</span></td>
                                    <td>°C</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-lungs text-info me-2"></i> Saturacion O2</td>
                                    <td><span class="code">2708-6</span></td>
                                    <td>%</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-weight text-secondary me-2"></i> Peso</td>
                                    <td><span class="code">29463-7</span></td>
                                    <td>kg</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-ruler-vertical text-secondary me-2"></i> Altura</td>
                                    <td><span class="code">8302-2</span></td>
                                    <td>cm</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calculator text-success me-2"></i> IMC</td>
                                    <td><span class="code">39156-5</span></td>
                                    <td>kg/m²</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="info-box info-tip">
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <strong>Graficos de Tendencia:</strong> SAMI puede mostrar graficos de tendencia de los signos vitales a lo largo del tiempo, permitiendo visualizar la evolucion del paciente.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Examenes Fisicos 
                <section id="examenes" class="step-card">
                    <div class="step-number">6</div>
                    <h3 class="step-title">Examenes Fisicos</h3>
                    <div class="step-content">
                        <p>Los <strong>examenes fisicos</strong> documentan los hallazgos de la exploracion clinica:</p>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #7</h5>
                            <p>Registro de examen fisico mostrando hallazgos por sistema (cardiovascular, respiratorio, etc.)</p>
                            <span class="dimensions">Recomendado: 1200x600px</span>
                        </div>

                        <h5 class="mt-4">Sistemas Evaluados:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li><i class="fas fa-heart"></i><div>Cardiovascular</div></li>
                                    <li><i class="fas fa-lungs"></i><div>Respiratorio</div></li>
                                    <li><i class="fas fa-stomach"></i><div>Abdominal</div></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li><i class="fas fa-brain"></i><div>Neurologico</div></li>
                                    <li><i class="fas fa-bone"></i><div>Musculoesqueletico</div></li>
                                    <li><i class="fas fa-hand-paper"></i><div>Piel</div></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section> -->

                <!-- Medicamentos -->
                <section id="medicamentos" class="step-card">
                    <div class="step-number">6</div>
                    <h3 class="step-title">Órdenes Médicas</h3>
                    <div class="step-content">
                        <p>La seccion de <strong>órdenes médicas</strong> muestra todas las prescripciones de medicamentos y solicitudes de imágenes y laboratorio realizadas:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-order.png') }}" alt="" style="width: 100%;">
                        </div>
                        <div class="info-box info-note" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <i class="fas fa-pills" style="color: #059669;"></i>
                            <div>
                                <strong>Informacion incluida en medicamentos:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Nombre del medicamento</li>
                                    <li>Dosis y forma de administracion</li>
                                    <li>Frecuencia (cada cuantas horas)</li>
                                    <li>Duracion del tratamiento</li>
                                    <li>Indicaciones especiales</li>
                                </ul>
                            </div>
                        </div>
                        <div class="info-box info-note">
                            <i class="fas fa-pills"></i>
                            <div>
                                <strong>Informacion incluida en solicitudes de imágenes y laboratorios:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Nombre del estudio o servicio</li>
                                    <li>Código del estudio</li>
                                    <li>Tipo de estudio</li>
                                    <li>Cantidad</li>
                                    <li>Fecha</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Notas Clinicas -->
                <section id="notas" class="step-card">
                    <div class="step-number">7</div>
                    <h3 class="step-title">Notas Clínicas</h3>
                    <div class="step-content">
                        <p>Las <strong>notas clínicas</strong> son las impresiones diagnosticas y observaciones del medico:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-note.png') }}" alt="" style="width: 100%;">
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="sub-step">
                                    <h6><i class="fas fa-comment-medical me-2"></i>Notas Clínicas</h6>
                                    <p class="mb-0">Impresiones diagnosticas formales que forman parte del expediente oficial.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sub-step">
                                    <h6><i class="fas fa-sticky-note me-2"></i>Notas Personales</h6>
                                    <p class="mb-0">Notas privadas del medico que solo el puede ver.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Licencias Medicas -->
                <section id="licencias" class="step-card">
                    <div class="step-number">8</div>
                    <h3 class="step-title">Licencias Médicas</h3>
                    <div class="step-content">
                        <p>Las <strong>licencias médicas</strong> (incapacidades) emitidas al paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-license.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-file-download"></i>
                            <div>
                                <strong>Descarga:</strong> Las licencias médicas pueden descargarse en formato PDF para entregar al paciente o a su empleador.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Agregar Antecedentes -->
                <section id="agregar" class="step-card step-success">
                    <div class="step-number">9</div>
                    <h3 class="step-title">Agregar Antecedentes Médicos</h3>
                    <div class="step-content">
                        <p>Puedes agregar manualmente antecedentes médicos historicos del paciente:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-plus-circle me-2"></i>Pasos para Agregar</h6>
                            <ol>
                                <li>En la seccion de "Antecedentes Médicos", haz clic en <strong>"Agregar"</strong></li>
                                <li>Selecciona la <strong>categoria</strong> (alergia, cirugia, etc.)</li>
                                <li>Ingresa el <strong>titulo</strong> descriptivo</li>
                                <li>Agrega una <strong>descripcion</strong> detallada</li>
                                <li>Selecciona la <strong>fecha de ocurrencia</strong></li>
                                <li>Haz clic en <strong>"Guardar"</strong></li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/modal-prev.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> Los antecedentes agregados manualmente se registran con estado "activo" y verificacion "confirmada". Asegurate de ingresar informacion precisa.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Filtros y Busqueda 
                <section id="filtros" class="step-card">
                    <div class="step-number">11</div>
                    <h3 class="step-title">Filtros y Busqueda</h3>
                    <div class="step-content">
                        <p>SAMI ofrece potentes herramientas de filtrado para encontrar informacion especifica:</p>

                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                            <h5>Captura de Pantalla #12</h5>
                            <p>Panel de filtros mostrando opciones de rango de tiempo, busqueda y filtros adicionales</p>
                            <span class="dimensions">Recomendado: 1200x400px</span>
                        </div>

                        <h5 class="mt-4">Opciones de Filtrado:</h5>
                        <table class="vital-signs-table">
                            <thead>
                                <tr>
                                    <th>Filtro</th>
                                    <th>Descripcion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Rango de Tiempo</strong></td>
                                    <td>Hoy, Esta semana, Este mes, Este ano, Personalizado, Todo</td>
                                </tr>
                                <tr>
                                    <td><strong>Busqueda de Texto</strong></td>
                                    <td>Busca en notas y descripciones</td>
                                </tr>
                                <tr>
                                    <td><strong>Por Medico</strong></td>
                                    <td>Filtra registros de un medico especifico</td>
                                </tr>
                                <tr>
                                    <td><strong>Por Condicion</strong></td>
                                    <td>Filtra por codigo de diagnostico</td>
                                </tr>
                                <tr>
                                    <td><strong>Agrupar por Encuentro</strong></td>
                                    <td>Agrupa los datos por consulta</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>-->

                <!-- Siguientes Pasos -->
                <section class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguientes Pasos</h3>
                    <div class="step-content">
                        <p>Ahora que conoces la historia medica, puedes continuar aprendiendo sobre:</p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-calendar-plus fa-2x text-warning"></i>
                                        </div>
                                        <h5 class="card-title">Agendar Citas</h5>
                                        <p class="card-text text-muted small">Programa citas para tus pacientes</p>
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
                                        <p class="card-text text-muted small">Documenta consultas medicas</p>
                                        <span class="badge bg-secondary">Proximamente</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-check-double"></i>
                            <div>
                                <strong>Resumen de lo Aprendido:</strong>
                                <ol class="mb-0 mt-2">
                                    <li><i class="fas fa-check text-success me-1"></i> Registro de cuenta</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Creacion de sucursal</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Creacion de consultorio</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Registro de pacientes</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Historia medica <strong>(Esta guia)</strong></li>
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
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #00897b;"></i>Tienes preguntas sobre la historia medica?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="mailto:soporte@sami.com" class="btn text-white" style="background: linear-gradient(135deg, #00897b 0%, #004d40 100%);">
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
