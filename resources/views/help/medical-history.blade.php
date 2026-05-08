@extends('help.layout')
@section('style')

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

/* Contenedor principal con límites estrictos */
.help-content {
    margin-left: 280px;
    padding: 30px;
    max-width: calc(100vw - 280px);
    overflow-x: hidden;
    box-sizing: border-box;
}

/* Corregir Bootstrap rows */
    .help-content .row {
        margin-left: 0;
        margin-right: 0;
        max-width: 100%;
    }

    .help-content .row > [class*="col-"] {
        padding-left: 15px;
        padding-right: 15px;
    }

    /* Evitar que las imágenes fuercen scroll */
    .help-content img {
        max-width: 100%;
        height: auto;
    }

    /* Asegurar que tablas y contenido no desborden */
    .help-content table,
    .help-content .field-table {
        max-width: 100%;
        overflow-x: auto;
        display: block;
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
        .category-hospitalization { background: #fae0f7; color: #cf0091; }
        .category-inmu { background: #daffe6; color: #008f4f; }
        .category-other { background: #fdffda; color: #8f8a00; }

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

               /* Timeline */
        .process-timeline {
            position: relative;
            padding: 20px 0;
        }

        .process-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #009655 0%,  var(--teal-color));
        }

        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 5px;
            width: 20px;
            height: 20px;
            background: #fff;
            border: 3px solid var(--teal-color);
            border-radius: 50%;
        }

        .timeline-item.completed::before {
            background: var(--teal-color);
        }

        .timeline-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .timeline-content h6 {
            color: #004d40;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .timeline-content p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }


/* Responsive: Tablets */
    @media screen and (max-width: 1024px) and (min-width: 769px) {
        .vital-signs-table th,
        .vital-signs-table td {
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .vital-signs-table .code {
            font-size: 0.8rem;
            padding: 2px 6px;
        }
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

        /* Responsive: Mobile */
    @media screen and (max-width: 768px) {

    }
                             /* Extra small devices */
        @media screen and (max-width: 480px) {
           .icd-code-box, .info-box {
            flex-direction: column;
            align-items: center;
           }
            .step-card, .sub-step{
                border-left: none;
                padding: 25px;
                border-bottom: 5px solid var(--teal-color);
            }
            .sub-step{
                border-bottom-color: #00897b;;
            }
            .step-number {
                left: 45%;
            }
           .icd-example h6, .icd-code-box{
            text-align: center;
        }
            .step-title {
                padding-left: 0;
                text-align: center;
                padding-top: 8%;
            }

                    /* Ocultar el thead en móviles */
        .vital-signs-table thead {
            display: none;
        }

        /* Convertir tabla en cards */
        .vital-signs-table,
        .vital-signs-table tbody,
        .vital-signs-table tr,
        .vital-signs-table td {
            display: block;
            width: 100%;
        }

        .vital-signs-table tr {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .vital-signs-table td {
            text-align: right;
            padding: 10px 15px;
            position: relative;
            border-bottom: 1px solid #f5f5f5;
        }

        .vital-signs-table td:last-child {
            border-bottom: none;
        }

        /* Resetear border-radius en móvil */
        .vital-signs-table tr:last-child td:first-child,
        .vital-signs-table tr:last-child td:last-child {
            border-radius: 0;
        }

        /* Agregar labels antes de cada dato */
        .vital-signs-table td:before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #004d40;
        }

        /* Opcional: hacer que el primer td (Signo Vital) se vea como header */
        .vital-signs-table td:first-child {
            background: #004d40;
            font-weight: 600;
            color: white;
            text-align: left;
            font-size: 1rem;
        }

        .vital-signs-table td:first-child:before {
            display: none;
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

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'medical-history'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="#">Gestion de Pacientes</a></li>
            <li class="breadcrumb-item active">Historia Médica</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-notes-medical me-3"></i>Guia de Historia Médica</h1>
        <p>Aprende a visualizar y gestionar la historia médica completa de tus pacientes, incluyendo antecedentes, diagnósticos, signos vitales y mas.</p>
    </div>
@stop
@section('table-content')
    <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li>
                            <a href="#concepto">
                                <i class="fas fa-info-circle"></i>
                                ¿Qué es la Historia Médica?
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
                                Resumen General
                            </a>
                        </li>
                        <li>
                            <a href="#consultas">
                                <i class="fas fa-user-md"></i>
                                Consultas Médicas
                            </a>
                        </li>
                        <li>
                            <a href="#condiciones">
                                <i class="fas fa-diagnoses"></i>
                                Condiciones/Diagnósticos
                            </a>
                        </li>
                        <li>
                            <a href="#signos-vitales">
                                <i class="fas fa-heartbeat"></i>
                                Signos Vitales
                            </a>
                        </li>
                        <li>
                            <a href="#enfermedad">
                                <i class="fas fa-stethoscope"></i>
                                Enfermedad Actual
                            </a>
                        </li>
                        <li>
                            <a href="#medicamentos">
                                <i class="fas fa-pills"></i>
                                Órdenes Médicas
                            </a>
                        </li>
                        <li>
                            <a href="#antecedentes">
                                <i class="fas fa-history"></i>
                                Antecedentes Médicos
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#examenes">
                                <i class="fas fa-stethoscope"></i>
                                Examenes Fisicos
                            </a>
                        </li> -->
                        <li>
                            <a href="#notas">
                                <i class="fas fa-sticky-note"></i>
                                Notas Médicas
                            </a>
                        </li>
                        <li>
                            <a href="#licencias">
                                <i class="fas fa-file-medical"></i>
                                Incapacidades Médicas
                            </a>
                        </li>
                        <li>
                            <a href="#notas-privadas">
                                <i class="fas fa-file-text"></i>
                                Notas Privadas
                            </a>
                        </li>
                        <!--<li>
                            <a href="#agregar">
                                <i class="fas fa-plus-circle"></i>
                                Agregar Antecedentes
                            </a>
                        </li>-->
                        <li>
                            <a href="#seguros">
                                <i class="fas fa-shield-alt"></i>
                                Gestión de Seguros
                            </a>
                        </li>
                        <li>
                            <a href="#auth-code">
                                <i class="fas fa-address-book"></i>
                                Flujo de Solicitud de Historial
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
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>¿Qué es la Historia Médica?</h3>
                    <div class="step-content">
                        <p>La <strong>historia médica</strong> (o historial clínico) es el registro completo de toda la información de salud de un paciente. En SAMI, la historia médica esta organizada en multiples secciones para facilitar el acceso a la información.</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Registro cronológico</strong><br>
                                    <small class="text-muted">Toda la información esta ordenada por fecha</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Códigos estandarizados</strong><br>
                                    <small class="text-muted">Diagnósticos con códigos ICD-10, signos vitales con LOINC</small>
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
                                <strong>Privacidad:</strong> Los médicos solo pueden ver los registros de las consultas que ellos han realizado, a menos que tengan autorizacion especial del paciente o del administrador.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Secciones Disponibles -->
                <section id="secciones" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-th-large me-2"></i>Secciones de la Historia Medica</h3>
                    <div class="step-content">
                        <p>La historia médica está dividida en las siguientes secciones:</p>

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
                                    <h6>Consultas</h6>
                                    <p>Consultas Médicas realizadas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-diagnoses"></i>
                                    <h6>Diagnósticos</h6>
                                    <p>Diagnósticos ICD-10</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-heartbeat"></i>
                                    <h6>Signos Vitales</h6>
                                    <p>Mediciones vitales</p>
                                </div>
                            </div>
                            <!--<div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-stethoscope"></i>
                                    <h6>Examenes Fisicos</h6>
                                    <p>Hallazgos de examen</p>
                                </div>
                            </div>-->
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-notes-medical"></i>
                                    <h6>Enfermedades Actuales</h6>
                                    <p>Motivo de consulta</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-stethoscope"></i>
                                    <h6>Órdenes Médicos</h6>
                                    <p>Prescripciones de medicamentos, Procedimientos, Laboratorios e Imágenes</p>
                                </div>
                            </div>
                            <!--<div class="col-md-4 mb-3">
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
                            </div>-->
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-history"></i>
                                    <h6>Antecedentes</h6>
                                    <p>Historial médico</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-comment-medical"></i>
                                    <h6>Notas Médicas</h6>
                                    <p>Impresiones médicas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-file-medical"></i>
                                    <h6>Incapacidades</h6>
                                    <p>Incapacidades Médicas</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-sticky-note"></i>
                                    <h6>Notas Privadas</h6>
                                    <p>Notas del médico</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="section-card">
                                    <i class="fas fa-shield-alt"></i>
                                    <h6>Seguros</h6>
                                    <p>Gestión de pólizas médicas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Como Acceder -->
                <section id="acceder" class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">¿Cómo acceder a la Historia Médica?</h3>
                    <div class="step-content">
                        <p>Para acceder a la historia medica de un paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-patient.png') }}" alt="" style="width: 100%;">
                        </div>
                        <div class="sub-step">
                            <h6><i class="fas fa-route me-2"></i>Pasos</h6>
                            <ol>
                                <li>Ve a la Sección de Pacientes</li>
                                <li>Haz clic lista pacientes y busca el paciente</li>
                                <li>Haz clic en el nombre del paciente o en <strong>"Historial Médico"</strong></li>
                            </ol>
                        </div>

                        {{--}}<p class="mt-3">URL directa:</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL Directa</h6>
                            <code class="d-block p-2 bg-dark text-light rounded">{{ config('app.url') }}/patients/{id}/medical_history</code>
                        </div>{{--}}

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-profile.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Vista General -->
                <section id="resumen" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Resumen General</h3>
                    <div class="step-content">
                        <p>La seccion de <strong>Resumen General</strong> muestra un resumen rápido del estado del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/general.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Información Mostrada:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-chart-bar"></i>
                                        <div><strong>Total de consultas médicas</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-diagnoses"></i>
                                        <div><strong>Condiciones activas</strong></div>
                                    </li>
                                    <!--<li>
                                        <i class="fas fa-calendar"></i>
                                        <div><strong>Fecha de última visita</strong></div>
                                    </li>-->
                                    <li>
                                        <i class="fas fa-clock"></i>
                                        <div><strong>Actividad reciente</strong></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-pills"></i>
                                        <div><strong>Ordenes médicas</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-allergies"></i>
                                        <div><strong>Antecedentes Médicos</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-heartbeat"></i>
                                        <div><strong>Signos Vitales</strong></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

            <!-- Sección de Consultas -->
                <section id="consultas" class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Consultas Médicas</h3>
                    <div class="step-content">
                        <p>La sección de <strong>consultas médicas</strong> contiene el historial de las consultas realizadas al paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-encounter.png') }}" alt="" style="width: 100%;">
                        </div>
                    <h5 class="mt-4">Información mostrada de cada consulta:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-chart-bar"></i>
                                        <div><strong>Fecha y hora en que se realizó</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-diagnoses"></i>
                                        <div><strong>Nombre del Doctor y su especialidad</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar"></i>
                                        <div><strong>Diagnóstico registrado</strong></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-pills"></i>
                                        <div><strong>Estado</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-allergies"></i>
                                        <div><strong>Botón para ver resumen médico</strong></div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="sub-step mt-4">
                            <h6><i class="fas fa-file-pdf me-2"></i>Resumen Médico</h6>
                            <p>El <strong>Resumen Médico</strong> es un documento consolidado que resume toda la información relevante de una consulta específica. Para acceder a él, diríjase a la columna de <strong>"Acciones"</strong> en la tabla de consultas y haga clic en el botón <strong>"Ver Detalles"</strong>.</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-pdf.png') }}" alt="" style="width: 100%;">
                        </div>
                            <p>Este documento incluye:</p>
                            <ul class="checklist">
                                <li><i class="fas fa-check-circle"></i> <div><strong>Datos del Paciente:</strong> Nombre completo, edad, género, fecha de nacimiento, estado civil, dirección, número de identificación, teléfono de casa y lugar de residencia.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Información del Encuentro:</strong> Fecha, hora, nombre del médico que atendió y número de consulta.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Motivo de Consulta y Enfermedad Actual:</strong> Descripción detallada de los síntomas y motivo de la visita.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Signos Vitales:</strong> Registro de las mediciones tomadas durante la consulta.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Diagnósticos:</strong> Conclusiones médicas codificadas bajo ICD-10.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Órdenes Médicas:</strong> Medicamentos recetados con sus dosis e instrucciones, y estudios solicitados.</div></li>
                                <li><i class="fas fa-check-circle"></i> <div><strong>Notas Médicas:</strong> Recomendaciones y observaciones adicionales del profesional de salud.</div></li>
                            </ul>
                        </div>
                        <div class="info-box info-note">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Nota:</strong> Las secciones dependen de la plantilla de consulta configurada por el medico. Algunas pueden o no aparecer en el resumen médico.
                        </div>
                    </div>
                </section>

                <!-- Condiciones/Diagnosticos -->
                <section id="condiciones" class="step-card step-important">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Condiciones / Diagnósticos</h3>
                    <div class="step-content">
                        <p>Las <strong>condiciones</strong> son los diagnósticos registrados durante las consultas, codificados con el estandar <strong>ICD-10</strong>:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/conditions.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Detalle de la Tabla de Condiciones:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-barcode"></i>
                                        <div><strong>Código:</strong> Código alfanumérico estandarizado bajo el sistema ICD-10 para la identificación precisa del diagnóstico.</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-file-medical"></i>
                                        <div><strong>Descripción:</strong> Nombre o descripción clínica del diagnóstico registrado.</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-info-circle"></i>
                                        <div><strong>Estado:</strong> Indica la situación actual de la condición (ej. <strong>Activa</strong>, Resuelta, etc.).</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <div><strong>Severidad:</strong> Nivel de gravedad del diagnóstico (ej. Leve, Moderado, Severo).</div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <li>
                                        <i class="fas fa-tags"></i>
                                        <div><strong>Categoría:</strong> Clasificación adicional para agrupar diagnósticos similares.</div>
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar-alt"></i>
                                        <div><strong>Fecha Dx:</strong> Fecha exacta en la que el profesional de salud registró el diagnóstico.</div>
                                    </li>
                                    <!--<li>
                                        <i class="fas fa-mouse-pointer"></i>
                                        <div><strong>Acciones:</strong> Botón de <strong>"Ver Detalles"</strong> (ícono de ojo) para consultar información extendida del registro.</div>
                                    </li>-->
                                </ul>
                            </div>
                        </div>

                        <div class="icd-example mt-4">
                            <h6><i class="fas fa-code me-2"></i>Ejemplos de Codigos ICD-10:</h6>
                            <div class="icd-code-box">
                                <span class="code">J06.9</span>
                                <span class="description">Infección aguda de las vías respiratorias superiores</span>
                            </div>
                            <div class="icd-code-box">
                                <span class="code">I10</span>
                                <span class="description">Hipertensión esencial (primaria)</span>
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
                        <p>Los <strong>signos vitales</strong> son las mediciones fisiológicas tomadas durante las consultas:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-vs.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Campos de la Sección de Signos Vitales:</h5>
                        <table class="vital-signs-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <!--<th>Codigo LOINC</th>-->
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--<tr>
                                    <td data-label="Signo Vital"><i class="fas fa-thermometer-half text-warning me-2"></i> Temperatura</td>
                                    <td data-label="Codigo LOINC"><span class="code">8310-5</span></td>
                                    <td data-label="Unidad">°C</td>
                                </tr>-->
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-heartbeat text-danger me-2"></i> Frecuencia Cardiaca</td>
                                    <!--<td data-label="Codigo LOINC"><span class="code">8867-4</span></td>-->
                                    <td data-label="Unidad">lpm</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-heart text-danger me-2"></i> Presion Arterial Sistólica</td>
                                    <!--<td data-label="Codigo LOINC"><span class="code">8480-6</span></td>-->
                                    <td data-label="Unidad">mmHg</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-heart text-danger me-2"></i> Presion Arterial Diastólica</td>
                                    <!--<td data-label="Codigo LOINC"><span class="code">8462-4</span></td>-->
                                    <td data-label="Unidad">mmHg</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-lungs-virus text-info me-2"></i> Frecuencia Respiratoria</td>
                                    <!--<td data-label="Codigo LOINC"><span class="code">8867-4</span></td>-->
                                    <td data-label="Unidad">lpm</td>
                                </tr>
                                <!--<tr>
                                    <td data-label="Signo Vital"><i class="fas fa-lungs text-info me-2"></i> Saturacion O2</td>
                                    <td data-label="Codigo LOINC"><span class="code">2708-6</span></td>
                                    <td data-label="Unidad">%</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-weight text-secondary me-2"></i> Peso</td>
                                    <td data-label="Codigo LOINC"><span class="code">29463-7</span></td>
                                    <td data-label="Unidad">kg</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-ruler-vertical text-secondary me-2"></i> Talla</td>
                                    <td data-label="Codigo LOINC"><span class="code">8302-2</span></td>
                                    <td data-label="Unidad">cm</td>
                                </tr>
                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-calculator text-success me-2"></i> IMC</td>
                                    <td data-label="Codigo LOINC"><span class="code">39156-5</span></td>
                                    <td data-label="Unidad">kg/m²</td>
                                </tr>
                                                                <tr>
                                    <td data-label="Signo Vital"><i class="fas fa-droplet text-danger me-2"></i> Glucemia Capilar</td>
                                    <td data-label="Codigo LOINC"><span class="code">39156-5</span></td>
                                    <td data-label="Unidad">mg/dL</td>
                                </tr>-->
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

                <!-- Enfermedad actual -->
                <section id="enfermedad" class="step-card step-important">
                    <div class="step-number">6</div>
                    <h3 class="step-title">Enfermedad Actual</h3>
                    <div class="step-content">
                        <p>La Sección de <strong>Enfermedad Actual</strong> posee los registros detallado de los padecimientos registrados en las citas:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-ai.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Información mostrada del padecimiento:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="checklist">
                                                                        <li>
                                        <i class="fas fa-allergies"></i>
                                        <div><strong>Datos generales de la consulta como: fecha, Doctor, especialidad, diagnósticos y estado de la consulta</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-chart-bar"></i>
                                        <div><strong>Descripción</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-medkit"></i>
                                        <div><strong>Factores agravantes y síntomas asociados</strong></div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="checklist">
                                    <!--<li>
                                        <i class="fas fa-calendar"></i>
                                        <div><strong>Localización (lugar donde se presenta la molestia)</strong></div>
                                    </li>-->
                                    <li>
                                        <i class="fas fa-pills"></i>
                                        <div><strong>Cronología (Tiempo determinado del día en que se da la molestia)</strong></div>
                                    </li>
                                    <li>
                                        <i class="fas fa-diagnoses"></i>
                                        <div><strong>Duración</strong></div>
                                    </li>
                                </ul>
                            </div>
                        </div>


                    </div>
                </section>

                <!-- Medicamentos -->
                <section id="medicamentos" class="step-card">
                    <div class="step-number">7</div>
                    <h3 class="step-title">Órdenes Médicas</h3>
                    <div class="step-content">
                        <p>La seccion de <strong>órdenes médicas</strong> muestra todas las prescripciones de medicamentos, solicitudes de procedimientos, imágenes y laboratorio realizadas:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-order.png') }}" alt="" style="width: 100%;">
                        </div>
                        <div class="info-box info-note" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <i class="fas fa-pills" style="color: #059669;"></i>
                            <div>
                                <strong>Información incluida en medicamentos:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Nombre del medicamento</li>
                                    <li>Dosis y forma de administracion</li>
                                    <li>Frecuencia (cada cuantas horas)</li>
                                    <li>Duracion del tratamiento</li>
                                    <li>Indicaciones especiales</li>
                                </ul>
                            </div>
                        </div>
                        <div class="info-box info-note" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <i class="fas fa-pills" style="color: #059669;"></i>
                            <div>
                                <strong>Información incluida en solicitudes de imágenes y laboratorios:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Nombre del estudio o servicio</li>
                                    <li>Código del estudio</li>
                                    <li>Tipo de estudio</li>
                                    <li>Cantidad</li>
                                    <li>Fecha</li>
                                </ul>
                            </div>
                        </div>
                        <div class="info-box info-note">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Nota:</strong> Desde esta sección podrá visualizar y descargar las órdenes médicas y recetas del paciente.
                        </div>
                    </div>
                </section>

                <!-- Antecedentes Medicos -->
                <section id="antecedentes" class="step-card">
                    <div class="step-number">8</div>
                    <h3 class="step-title">Antecedentes Médicos</h3>
                    <div class="step-content">
                        <p>Los <strong>antecedentes médicos</strong> registran eventos de salud históricos del paciente:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-prev.png') }}" alt="" style="width: 100%;">
                        </div>

                    <h5 class="mt-4">Categorías de Antecedentes:</h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-allergy">Alergia</span>
                                    <span class="text-muted">Alergias a medicamentos, alimentos, etc.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-surgery">Cirugía</span>
                                    <span class="text-muted">Procedimientos quirúrgicos previos</span>
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
                                    <span class="text-muted">Hábitos, ocupación, estilo de vida</span>
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
                                    <span class="category-badge category-condition">Enfermedad Crónica</span>
                                    <span class="text-muted">Enfermedades crónicas previas</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-hospitalization">Hospitalización</span>
                                    <span class="text-muted">Por emergencia, cirugía, etc</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-inmu">Inmunización</span>
                                    <span class="text-muted">Vacunación</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="category-badge category-other">Otro</span>
                                    <!--<span class="text-muted">Vacunación</span>-->
                                </div>
                            </div>
                        </div>

                    <!-- Agregar Antecedentes -->
                    <h3 class="step-title" style="padding-left: 0;">Agregar Antecedentes Médicos</h3>
                    <div class="step-content">
                        <p>Puedes agregar manualmente antecedentes médicos históricos del paciente:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-plus-circle me-2"></i>Pasos para Agregar</h6>
                            <ol>
                                <li>En la sección de "Pacientes", haz clic en "Lista Pacientes"</li>
                                <li>Elige el paciente al cuál deseas agregarle el antecedente y haga clic al botón de Historial Médico (botón en la columna de acciones de color negro)</li>
                                <li>En el encabezado, en la parte derecha haga clic en <strong>"Antecedentes Médicos"</strong></li>
                                <li>Selecciona la <strong>categoría</strong> (alergía, cirugía, etc.)</li>
                                <li>Ingresa el <strong>título</strong> descriptivo</li>
                                <li>Selecciona la <strong>fecha de ocurrencia</strong></li>
                                <li>Agrega una <strong>descripción</strong> detallada</li>
                                <li>Haz clic en <strong>"Guardar"</strong></li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/modal-prev.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> Los antecedentes agregados manualmente se registran con estado "activo" y verificacion "confirmada". Asegurate de ingresar información precisa.
                            </div>
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

                <!-- Notas Clinicas -->
                <section id="notas" class="step-card">
                    <div class="step-number">9</div>
                    <h3 class="step-title">Notas Médicas</h3>
                    <div class="step-content">
                        <p>Las <strong>notas médicas</strong> son las impresiones diagnósticas y observaciones del médico:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-note.png') }}" alt="" style="width: 100%;">
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="sub-step">
                                    <h6><i class="fas fa-comment-medical me-2"></i>Notas Médicas</h6>
                                    <p class="mb-0">Notas que el médico describe para tenerlo presente en sus siguientes consultas y que también, puede ser vista por otros médicos de la red y por el mismo paciente.</p>
                                </div>
                            </div>
                            <!--<div class="col-md-6">
                                <div class="sub-step">
                                    <h6><i class="fas fa-sticky-note me-2"></i>Notas Privadas</h6>
                                    <p class="mb-0">Notas privadas del medico que solo el puede ver.</p>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </section>

                <!-- Licencias Medicas -->
                <section id="licencias" class="step-card">
                    <div class="step-number">10</div>
                    <h3 class="step-title">Incapacidades Médicas</h3>
                    <div class="step-content">
                        <p>La Sección de <strong>Incapacidades médicas</strong> contiene el historial de las incapacidades médicas emitidas al paciente.</p>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-license.png') }}" alt="" style="width: 100%;">
                        </div>
                        <h5 class="mt-4">Detalle de la Tabla de Incapacidades:</h5>
                         <div class="row">
                             <div class="col-md-6">
                                 <ul class="checklist">
                                     <li>
                                         <i class="fas fa-id-card"></i>
                                         <div><strong>Número de Registro:</strong> Corresponde al número identificador de la Compañía de Seguro a la cual se encuentra suscrito el paciente.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-calendar-alt"></i>
                                         <div><strong>Fecha emisión:</strong> Corresponde a la fecha en que se generó oficialmente la incapacidad.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-calendar-day"></i>
                                         <div><strong>Periodo de incapacidad:</strong> Rango de fechas en el que se establece que el paciente no podrá ejercer sus labores.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-clock"></i>
                                         <div><strong>Días:</strong> Cantidad total de días por los cuales el paciente se encuentra incapacitado.</div>
                                     </li>
                                 </ul>
                             </div>
                             <div class="col-md-6">
                                 <ul class="checklist">
                                     <li>
                                         <i class="fas fa-diagnoses"></i>
                                         <div><strong>Diagnóstico:</strong> Motivo médico por el cual el profesional de salud solicitó la incapacidad.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-user-md"></i>
                                         <div><strong>Médico:</strong> Profesional encargado de realizar el análisis clínico y emitir el documento.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-info-circle"></i>
                                         <div><strong>Estado:</strong> Indica la situación actual en que se encuentra la incapacidad registrada.</div>
                                     </li>
                                     <li>
                                         <i class="fas fa-download"></i>
                                         <div><strong>Acciones:</strong> Opción para <strong>descargar</strong> la incapacidad oficial en formato PDF.</div>
                                     </li>
                                 </ul>
                             </div>
                         </div>
                        <div class="info-box info-tip">
                            <i class="fas fa-file-download"></i>
                            <div>
                                <strong>Descarga:</strong> Las incapacidades médicas pueden descargarse en formato PDF para entregar al paciente o a su empleador.
                            </div>
                        </div>
                    </div>
                </section>

            <!-- Notas Privadas -->
                <section id="notas-privadas" class="step-card">
                    <div class="step-number">11</div>
                    <h3 class="step-title">Notas Privadas</h3>
                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-pnote.png') }}" alt="" style="width: 100%;">
                        </div>
                    <div class="step-content">
                        <p>La sección de <strong>Notas Privadas</strong> son notas que solo están habilitidas para el médico y sólo pueden ser visualizadas por el mismo.</p>
                        <div class="sub-step">
                            <h6><i class="fas fa-plus-circle me-2"></i>Pasos para Agregar</h6>
                            <ol>
                                <li>En la sección de <strong>"Pacientes"</strong>, haz clic en <strong>"Lista Pacientes"</strong></li>
                                <li>Elige el paciente al cuál deseas agregarle la nota y dale clic al botón de <strong>Historial Médico</strong> (botón en la columna de acciones de color negro)</li>
                                <li>En el encabezado, en la parte derecha haz clic en <strong>Agregar Nota Privada</strong></li>
                                <li>EScriba la información que necesite</li>
                                <li>Haz clic en <strong>"Guardar"</strong></li>
                            </ol>
                        </div>
                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-pnoterecord.png') }}" alt="" style="width: 100%;">
                        </div>
                        <!--<div class="info-box info-tip">
                            <i class="fas fa-file-download"></i>
                            <div>
                                <strong>Descarga:</strong> Las licencias médicas pueden descargarse en formato PDF para entregar al paciente o a su empleador.
                            </div>
                        </div>-->
                    </div>
                </section>


                <!-- Gestión de Seguros -->
                <section id="seguros" class="step-card step-primary">
                    <div class="step-number">12</div>
                    <h3 class="step-title">Gestión de Seguros Médicos</h3>
                    <div class="step-content">
                        <p>SAMI permite gestionar las pólizas de seguro médico del paciente directamente desde su historial:</p>

                        <div class="sub-step">
                            <h6><i class="fas fa-plus-circle me-2"></i>Pasos para Gestionar</h6>
                            <ol>
                                <li>En la sección de <strong>"Pacientes"</strong>, haz clic en <strong>"Lista Pacientes"</strong>.</li>
                                <li>Busca al paciente y haz clic en su nombre o en el botón de <strong>"Historial Médico"</strong>.</li>
                                <li>En el encabezado del historial, haz clic en el botón 🛡️ <strong>"Gestionar Seguros"</strong>.</li>
                                <li>Llena los datos correspondientes en el formulario</li>
                                <li>Haz clic en el botón <strong>"Agregar Seguro"</strong> para completar el registro.</li>
                            </ol>
                        </div>

                        <div>
                            <img src="{{ asset('images/tutorial/medical_history/med-insurance.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Acciones Disponibles:</h5>
                        <ul class="checklist">
                            <li>
                                <i class="fas fa-eye"></i>
                                <div><strong>Visualización:</strong> Ve el listado de seguros actuales, incluyendo prioridad (Primario, Secundario, Terciario), número de póliza y vigencia.</div>
                            </li>
                            <li>
                                <i class="fas fa-plus-circle"></i>
                                <div><strong>Agregar Nuevo:</strong> Completa el formulario con la compañía de seguros, número de póliza, titular, cobertura, fechas de vigencia, etc.</div>
                            </li>
                            <li>
                                <i class="fas fa-toggle-on"></i>
                                <div><strong>Activar/Desactivar:</strong> Utiliza el botón de acciones para activar o desactivar una póliza sin eliminarla.</div>
                            </li>
                            <li>
                                <i class="fas fa-trash-alt"></i>
                                <div><strong>Eliminar:</strong> Remueve definitivamente un registro de seguro si ya no es necesario.</div>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Flujo de Loggin -->
                <section id="auth-code" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Flujo de Solicitud y Habilitación de Historial Médico Completo del Paciente</h3>
                    <div class="step-content">
                        <p>En caso de que usted como Médico necesite conocer más Información acerca del historial médico del paciente, deberá solicitar el consentimiento del mismo para poder accerder al historial médico completo del paciente, en caso de que el paciente tenga historial médico, con otros clientes.</p>

                        <div class="process-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>1. Ir al Historial Médico</h6>
                                    <p>Primero deberá ir a <strong>"Lista Pacientes"</strong>, busca su paciente y haga clic en <strong>"Historial Médico"</strong>, en esta pantalla encontrará un botón que dice <strong>"Solicitar Acceso Completo al Historial"</strong>, haga clic.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/medical_history/info-patient.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>2. Solicitar Código de autorización</h6>
                                    <p>Haga clic en <strong>"Enviar Código de Autorización"</strong> para que el código de desbloqueo sea enviado al paciente.</p>
                                    <div>
                                        <img  src="{{ asset('images/tutorial/medical_history/info-modal.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>3. Correo de Código de Autorización</h6>
                                    <p>El Sistema enviará un correo con el <strong>Código de Autorización</strong> que deberá ingresar el Médico.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/medical_history/info-email.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>4. Ingreso de Código de Autorización</h6>
                                    <p>Luego de que el paciente le haya proporcionado el <strong>Código de Autorización</strong> deberá ingresarlo a la plataforma para validarlo.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/medical_history/info-sendcode.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>4. Acceso a Historial Completo</h6>
                                    <p>Luego de validar <strong>Código de Autorización</strong> podrá visualizar el <strong>Historial Médico</strong> completo del paciente.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/medical_history/info-medhs.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
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
                        <p>Ahora que conoces la historia médica, puedes continuar aprendiendo sobre:</p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-calendar-plus fa-2x text-warning"></i>
                                        </div>
                                        <h5 class="card-title">Agendar Citas</h5>
                                        <p class="card-text text-muted small">Programa citas para tus pacientes</p>
                                        <a href="{{ route('help.appointments') }}" class="btn text-white" style="background: #e65100;">
                                            <i class="fas fa-arrow-right me-2"></i>Ver Guía
                                        </a>
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
                                        <p class="card-text text-muted small">Documenta consultas médicas</p>
                                        <a href="{{ route('help.consultation') }}" class="btn btn-danger">
                                            <i class="fas fa-arrow-right me-2"></i>Ver Guia
                                        </a>
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
                                    <li><i class="fas fa-check text-success me-1"></i> Historia médica <strong>(Esta guía)</strong></li>
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
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #00897b;"></i>¿Tienes preguntas sobre la historia médica?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="mailto:business@meditecpty.com" class="btn text-white" style="background: linear-gradient(135deg, #00897b 0%, #004d40 100%);">
                                    <i class="fas fa-envelope me-2"></i>Contactar Soporte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop
