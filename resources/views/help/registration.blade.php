<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Registro - Centro de Ayuda SAMI</title>
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

        /* Field Table
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
        }*/

        /* Field Table - Desktop & Mobile Responsive */
        .field-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
        }

        .field-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

        .field-table tr:last-child td {
            border-bottom: none;
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


        /* Pricing Table - Desktop & Mobile Responsive */
        .pricing-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
        }

        .pricing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .pricing-table th {
            background: #1a237e;
            color: #fff;
            padding: 15px;
            font-weight: 600;
            text-align: left;
            font-size: 14px;
        }

        .pricing-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
        }

        .pricing-table tr:last-child td {
            border-bottom: none;
        }

        .pricing-table tr.recommended {
            background-color: #e8f5e9;
        }

        .pricing-table tr.recommended td {
            background-color: #e8f5e9;
        }

        .badge-recommended {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .text-muted {
            color: #757575;
        }

        .text-success {
            color: #4caf50;
        }

        .text-danger {
            color: #d32f2f;
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

        /* Sub-steps */
        .sub-step {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 3px solid #667eea;
        }

        .sub-step h6 {
            color: #1a237e;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Code Block */
        .code-block {
            background: #263238;
            color: #aed581;
            padding: 15px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            margin: 15px 0;
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
            color: #667eea;
            margin-top: 3px;
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
                transform-origin: center center; /* Asegura que rote desde el centro */
                width: 100%; /* Ancho completo */
                height: 60px; /* Altura fija para la flecha rotada */
                display: flex;
                align-items: center;
                justify-content: center;
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
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
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
            border: 3px solid #667eea;
            border-radius: 50%;
        }

        .timeline-item.completed::before {
            background: #667eea;
        }

        .timeline-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .timeline-content h6 {
            color: #1a237e;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .timeline-content p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }

        .yappy-img{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .yappy-imgsize{width: 276px; height: 593px; }
        /* RESPONSIVE - Mobile */
        @media screen and (max-width: 768px) {
            .pricing-table {
                border: 0;
                box-shadow: none;
            }

            .pricing-table thead {
                display: none;
            }

            .pricing-table tbody {
                display: block;
            }

            .pricing-table tr {
                display: block;
                margin-bottom: 15px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .pricing-table td {
                display: block;
                text-align: left;
                padding: 12px 15px;
                border-bottom: 1px solid #e9ecef;
                position: relative;
                padding-left: 50%;
            }

            .pricing-table td:last-child {
                border-bottom: none;
            }

            .pricing-table td:before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                font-weight: 600;
                color: #1a237e;
            }

            .pricing-table td[data-label="Plan"] {
                background: #f5f5f5;
                font-size: 16px;
                padding-left: 15px;
            }

            .pricing-table td[data-label="Plan"]:before {
                display: none;
            }

            .pricing-table tr.recommended td[data-label="Plan"] {
                background: #4caf50;
                color: white;
            }

            .pricing-table tr.recommended td[data-label="Plan"] strong {
                color: white;
            }

        }
        /* Tablets
        @media screen and (min-width: 481px) and (max-width: 768px) {
            .field-table td[data-label="Campo"] {
                font-size: 17px;
            }

            .field-table td[data-label="Descripcion"] {
                font-size: 15px;
            }
        }*/

        /* Extra small devices */
        @media screen and (max-width: 480px) {
            .pricing-table td {
                padding: 10px 12px;
                padding-left: 45%;
                font-size: 14px;
            }

            .pricing-table td:before {
                font-size: 13px;
                width: 40%;
            }
            .yappy-imgsize{
                width: 100%;
                height: auto;
            }
            .info-box {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .step-number {
                left: 45%;
            }
            .step-title {
                padding-left: 0;
                text-align: center;
                padding-top: 8%;
            }
            .step-card, .sub-step{
                border-left: none;
                padding: 25px;
                border-bottom: 5px solid var(--primary-color);
            }
            body{
                text-align: center;
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
                background: #1a237e;
                color: white;
                font-size: 15px;
                padding: 12px;
                border-bottom: 2px solid #0d47a1;
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
                color: #1a237e;
                margin-right: 5px;
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
   @include('help.sidebar', ['active' => 'registration'])
    <!-- Main Content -->
    <main class="help-content">
        <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Guias de Registro</a></li>
                <li class="breadcrumb-item active">Registro de Cliente</li>
            </ol>
        </nav>

        <!-- Module Header -->
        <div class="module-header">
            <h1><i class="fas fa-user-plus me-3"></i>Guia de Registro de Cliente/Medico</h1>
            <p>Aprende paso a paso como registrar tu clinica o consultorio medico en SAMI. El proceso inicia desde la pagina principal, donde seleccionas tu plan y luego completas el formulario de registro.</p>
        </div>

        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guia</h5>
                    <ul class="toc-list">
                        <li>
                            <a href="#resumen">
                                <i class="fas fa-info-circle"></i>
                                Resumen del Proceso
                            </a>
                        </li>
                        <li>
                            <a href="#requisitos">
                                <i class="fas fa-clipboard-check"></i>
                                Requisitos Previos
                            </a>
                        </li>
                        <li>
                            <a href="#paso-0">
                                <i class="fas fa-globe"></i>
                                Paso 1: Seleccionar Plan en Landing
                            </a>
                        </li>
                        <li>
                            <a href="#planes-disponibles">
                                <i class="fas fa-box"></i>
                                Planes Disponibles
                            </a>
                        </li>
                        <li>
                            <a href="#paso-1">
                                <i class="fas fa-edit"></i>
                                Paso 2: Completar Formulario
                            </a>
                        </li>
                        <li>
                            <a href="#paso-2">
                                <i class="fas fa-user"></i>
                                Paso 3: Datos Personales
                            </a>
                        </li>
                        <li>
                            <a href="#paso-3">
                                <i class="fas fa-stethoscope"></i>
                                Paso 4: Info Profesional
                            </a>
                        </li>
                        <li>
                            <a href="#paso-4">
                                <i class="fas fa-image"></i>
                                Paso 5: Logo (Opcional)
                            </a>
                        </li>
                        <li>
                            <a href="#paso-5">
                                <i class="fas fa-gift"></i>
                                Paso 6: Codigo de Referido
                            </a>
                        </li>
                        <li>
                            <a href="#paso-6">
                                <i class="fas fa-check-square"></i>
                                Paso 7: Terminos y Envio
                            </a>
                        </li>
                        <li>
                            <a href="#post-registro">
                                <i class="fas fa-flag-checkered"></i>
                                Despues del Registro
                            </a>
                        </li>
                        <li>
                            <a href="#loggin">
                                <i class="fas fa-cogs"></i>
                                Flujo de Inicio de Sesión
                            </a>
                        </li>
                        <li>
                            <a href="#assistant">
                                <i class="fas fa-cogs"></i>
                                Asistente de Configuración
                            </a>
                        </li>
                        <li>
                            <a href="#pago">
                                <i class="fas fa-credit-card"></i>
                                Proceso de Pago
                            </a>
                        </li>
                        <li>
                            <a href="#que-sucede">
                                <i class="fas fa-cogs"></i>
                                Flujo de pago con el botón de Yappy
                            </a>
                        </li>
                        <li>
                            <a href="#siguientes-pasos">
                                <i class="fas fa-arrow-right"></i>
                                Siguientes Pasos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Resumen del Proceso -->
                <section id="resumen" class="step-card step-info">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Resumen del Proceso</h3>
                    <div class="step-content">
                        <p>El registro en SAMI es un proceso sencillo que te permite crear tu cuenta y comenzar a gestionar tu practica medica en pocos minutos.</p>

                        <!-- Flow Diagram -->
                        <div class="flow-diagram">
                            <div class="flow-step">
                                <i class="fas fa-home"></i>
                                <h6>Landing Page</h6>
                                <p>Ver planes</p>
                            </div>
                            <div class="flow-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="flow-step">
                                <i class="fas fa-box"></i>
                                <h6>Seleccionar</h6>
                                <p>Elegir plan</p>
                            </div>
                            <div class="flow-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="flow-step">
                                <i class="fas fa-edit"></i>
                                <h6>Completar</h6>
                                <p>Llenar datos</p>
                            </div>
                            <div class="flow-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="flow-step">
                                <i class="fas fa-paper-plane"></i>
                                <h6>Enviar</h6>
                                <p>Crear cuenta</p>
                            </div>
                            <div class="flow-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="flow-step">
                                <i class="fas fa-credit-card"></i>
                                <h6>Pagar</h6>
                                <p>Activar plan</p>
                            </div>
                            <div class="flow-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div class="flow-step">
                                <i class="fas fa-check-circle"></i>
                                <h6>Listo!</h6>
                                <p>Usar SAMI</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Requisitos Previos -->
                <section id="requisitos" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-clipboard-check me-2"></i>Requisitos Previos</h3>
                    <div class="step-content">
                        <p>Antes de comenzar el registro, asegurate de tener a la mano:</p>

                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Documento de Identidad</strong><br>
                                    <small class="text-muted">Cedula (CC), Pasaporte (PA), Carnet de Extranjeria (CE) o Permiso Temporal (PT)</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Correo Electronico Valido</strong><br>
                                    <small class="text-muted">Se enviaran tus credenciales de acceso a este correo</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Numero de Telefono</strong><br>
                                    <small class="text-muted">Preferiblemente con WhatsApp para notificaciones</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Especialidad Medica</strong><br>
                                    <small class="text-muted">Conocer tu especialidad para seleccionarla en el formulario</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Logo (Opcional)</strong><br>
                                    <small class="text-muted">Imagen en formato JPG, JPEG o PNG (maximo 2MB)</small>
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Consejo:</strong> Si tienes un codigo de referido de otro usuario SAMI, tenlo listo para obtener un descuento en tu suscripcion.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 0: Seleccionar Plan desde Landing -->
                <section id="paso-0" class="step-card step-important">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Seleccionar Plan desde la Pagina Principal</h3>
                    <div class="step-content">
                        <div class="sub-step">
                            <h6><i class="fas fa-home me-2"></i>Pasos para Iniciar</h6>
                            <ol>
                                {{--}}<li>Visita la pagina principal: <div class="code-block">{{ config('app.url') }}</div></li>{{--}}
                                <li>Navega a la seccion <strong>"Planes"</strong> (menu o scroll hacia abajo)</li>
                                <li>Revisa los planes disponibles y sus caracteristicas</li>
                                <li>Haz clic en el boton <strong>"Suscribirse Ahora"</strong> del plan que desees</li>
                            </ol>
                        </div>
                        <div>
                            <img src="{{ asset('images/tutorial/register/plans.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </section>

                <!-- Planes Disponibles -->
                <section id="planes-disponibles" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-box me-2"></i>Planes Disponibles</h3>
                    <div class="step-content">
                        <p>SAMI ofrece tres planes para registro publico (el plan Empresarial solo esta disponible contactando ventas):</p>

                        <div class="pricing-table-wrapper">
                            <table class="pricing-table">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Precio/mes</th>
                                        <th>Usuarios</th>
                                        <th>Citas</th>
                                        <th>SAMI</th>
                                        <th>Directorio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Plan"><strong>Basico</strong></td>
                                        <td data-label="Precio/mes">$49.99</td>
                                        <td data-label="Usuarios">1</td>
                                        <td data-label="Citas">30/mes</td>
                                        <td data-label="SAMI"><span class="text-muted">Tentativo</span></td>
                                        <td data-label="Directorio"><i class="fas fa-times text-danger"></i></td>
                                    </tr>
                                    <tr class="recommended">
                                        <td data-label="Plan">
                                            <strong>Estandar ⭐</strong><br>
                                            <small class="badge-recommended">RECOMENDADO</small>
                                        </td>
                                        <td data-label="Precio/mes">$74.99</td>
                                        <td data-label="Usuarios">1</td>
                                        <td data-label="Citas">Ilimitadas</td>
                                        <td data-label="SAMI"><i class="fas fa-check text-success"></i> Activo</td>
                                        <td data-label="Directorio"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td data-label="Plan"><strong>Premium</strong></td>
                                        <td data-label="Precio/mes">$124.99</td>
                                        <td data-label="Usuarios">4</td>
                                        <td data-label="Citas">Ilimitadas</td>
                                        <td data-label="SAMI"><i class="fas fa-check text-success"></i> Activo</td>
                                        <td data-label="Directorio"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--<table class="field-table">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Precio/mes</th>
                                    <th>Usuarios</th>
                                    <th>Citas</th>
                                    <th>SAMI</th>
                                    <th>Directorio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Basico</strong></td>
                                    <td>$49.99</td>
                                    <td>1</td>
                                    <td>30/mes</td>
                                    <td><span class="text-muted">Tentativo</span></td>
                                    <td><i class="fas fa-times text-danger"></i></td>
                                </tr>
                                <tr style="background-color: #e8f5e9;">
                                    <td><strong>Estandar ⭐</strong><br><small class="text-success">RECOMENDADO</small></td>
                                    <td>$74.99</td>
                                    <td>1</td>
                                    <td>Ilimitadas</td>
                                    <td><i class="fas fa-check text-success"></i> Activo</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><strong>Premium</strong></td>
                                    <td>$124.99</td>
                                    <td>4</td>
                                    <td>Ilimitadas</td>
                                    <td><i class="fas fa-check text-success"></i> Activo</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                            </tbody>
                        </table>-->

                        <h5 class="mt-4">Diferencias Clave Entre Planes:</h5>

                        <div class="sub-step">
                            <h6><i class="fas fa-balance-scale me-2"></i>Basico vs Estandar</h6>
                            <p><strong>UNICA DIFERENCIA:</strong></p>
                            <ul>
                                <li><strong>Basico:</strong> Limite de 30 citas por mes, SAMI no activo</li>
                                <li><strong>Estandar:</strong> Citas ilimitadas + SAMI activo + Directorio Medico</li>
                            </ul>
                            <p><strong>Todo lo demas es EXACTAMENTE IGUAL</strong> (gestion de pacientes, historia clinica, dashboard, etc.)</p>
                        </div>

                        <div class="sub-step">
                            <h6><i class="fas fa-balance-scale me-2"></i>Estandar vs Premium</h6>
                            <p><strong>UNICA DIFERENCIA:</strong></p>
                            <ul>
                                <li><strong>Estandar:</strong> 1 usuario (medico solo)</li>
                                <li><strong>Premium:</strong> 4 usuarios (Admin + Doctor + Recepcionista + Asistente)</li>
                            </ul>
                            <p><strong>Todo lo demas es EXACTAMENTE IGUAL</strong> (citas ilimitadas, SAMI activo, todas las funcionalidades)</p>
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-star"></i>
                            <div>
                                <strong>Por que el Estandar es recomendado?</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Por solo $25 mas que el Basico, obtienes citas ilimitadas</li>
                                    <li>SAMI activo automatiza tus citas por WhatsApp 24/7</li>
                                    <li>Apareces en el Directorio Medico, captando nuevos pacientes constantemente</li>
                                    <li>Potencial ilimitado de crecimiento sin restricciones</li>
                                </ul>
                            </div>
                        </div>
                        {{--}}
                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Plan Empresarial:</strong> Este plan (50+ usuarios, SAMI personalizado) NO aparece en el registro publico. Solo se puede acceder contactando al equipo de ventas desde el landing page.
                            </div>
                        </div>
                        {{--}}
                    </div>
                </section>

                <!-- Paso 1: Completar Formulario -->
                <section id="paso-1" class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Completar Formulario de Registro</h3>
                    <div class="step-content">
                        <p>Una vez hayas seleccionado tu plan desde el landing page, seras redirigido al formulario de registro con tu plan pre-seleccionado:</p>

                        {{--}}<div class="sub-step">
                            <h6><i class="fas fa-link me-2"></i>URL del Formulario</h6>
                            <div class="code-block">
                                {{ config('app.url') }}/register/client?package={id}
                            </div>
                            <p class="mt-2 mb-0"><small class="text-muted">Notaras que la URL incluye el parametro <code>package</code> con el ID del plan seleccionado.</small></p>
                        </div>{{--}}

                        <div class="yappy-img">
                            <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/register_form.png') }}" alt="">
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Nota:</strong> Si ya tienes una cuenta pero esta inactiva, puedes usar este mismo formulario para reactivarla con un nuevo plan.
                            </div>
                        </div>
                        {{--}}
                        <div class="info-box info-tip">
                            <i class="fas fa-robot"></i>
                            <div>
                                <strong>Alerta SAMI:</strong> Si seleccionaste el plan Estandar o Premium, veras una alerta animada explicando los beneficios del agente SAMI y el Directorio Medico.
                            </div>
                        </div>
                        {{--}}
                    </div>
                </section>

                <!-- Paso 2: Datos Personales -->
                <section id="paso-2" class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Completar Datos Personales</h3>
                    <div class="step-content">
                        <p>Ingresa tu informacion personal basica:</p>
                    <div class="field-table-wrapper">
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
                                    <td data-label="Campo"><strong>Tipo de Documento</strong></td>
                                    <td data-label="Descripcion">Selecciona CC, PA, CE o PT</td>
                                    <td data-label="Requerido"><span class="required">Si*</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Numero de Documento</strong></td>
                                    <td data-label="Descripcion">Tu numero de identificacion</td>
                                    <td data-label="Requerido"><span class="required">Si*</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Nombre</strong></td>
                                    <td data-label="Descripcion">Tu nombre (s)</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Apellido</strong></td>
                                    <td data-label="Descripcion">Tu apellido (s)</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Correo Electronico</strong></td>
                                    <td data-label="Descripcion">Email para credenciales y notificaciones</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Telefono</strong></td>
                                    <td data-label="Descripcion">Con codigo de pais (+507, +1, etc.)</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Contrasena</strong></td>
                                    <td data-label="Descripcion">Minimo 8 caracteres</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Confirmar Contrasena</strong></td>
                                    <td data-label="Descripcion">Repetir la contrasena</td>
                                    <td data-label="Requerido"><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td data-label="Campo"><strong>Genero</strong></td>
                                    <td data-label="Descripcion">Masculino o Femenino (para titulo Dr./Dra.)</td>
                                    <td data-label="Requerido"><span class="required">Si*</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                        <!--<table class="field-table">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripcion</th>
                                    <th>Requerido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tipo de Documento</strong></td>
                                    <td>Selecciona CC, PA, CE o PT</td>
                                    <td><span class="required">Si*</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Numero de Documento</strong></td>
                                    <td>Tu numero de identificacion</td>
                                    <td><span class="required">Si*</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre</strong></td>
                                    <td>Tu nombre (s)</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Apellido</strong></td>
                                    <td>Tu apellido (s)</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Correo Electronico</strong></td>
                                    <td>Email para credenciales y notificaciones</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Telefono</strong></td>
                                    <td>Con codigo de pais (+507, +1, etc.)</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Contrasena</strong></td>
                                    <td>Minimo 8 caracteres</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Confirmar Contrasena</strong></td>
                                    <td>Repetir la contrasena</td>
                                    <td><span class="required">Si</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Genero</strong></td>
                                    <td>Masculino o Femenino (para titulo Dr./Dra.)</td>
                                    <td><span class="required">Si*</span></td>
                                </tr>
                            </tbody>
                        </table>-->

                        <!--<p class="mt-3"><small class="text-muted">* Requerido solo para planes individuales (1 usuario)</small></p>-->

                        <div>
                            <img src="{{ asset('images/tutorial/register/form.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Sobre el correo electronico:</strong> Si ya existe una cuenta con ese email pero esta inactiva o sin plan, se reactivara con los nuevos datos. Si la cuenta esta activa, deberas usar otro correo.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 3: Informacion Profesional -->
                <section id="paso-3" class="step-card">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Informacion Profesional</h3>
                    <div class="step-content">
                        <p>Selecciona tu especialidad medica de la lista disponible:</p>

                        <div class="yappy-img">
                            <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/speciality.png') }}" alt="">
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Nota:</strong> La especialidad seleccionada se asociara a tu perfil de medico. Podras agregar mas especialidades o modificarla posteriormente desde tu panel de control.
                            </div>
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> En caso de que la persona que está realizando el registro no sea médico, seleccionar la opción <strong>"Otro"</strong>.
                            </div>
                        </div>

                        <h5 class="mt-4">Algunas Especialidades Disponibles:</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Medicina General</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pediatria</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Ginecologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Cardiologia</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Dermatologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Neurologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Oftalmologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Traumatologia</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Psiquiatria</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Endocrinologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Urologia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Y muchas mas...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 4: Logo (Opcional) -->
                <section id="paso-4" class="step-card">
                    <div class="step-number">5</div>
                    <h3 class="step-title">Subir Logo (Opcional)</h3>
                    <div class="step-content">
                        <p>Puedes subir el logo de tu clinica o consultorio. Este aparecera en tu perfil y en los documentos que generes:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/register/logo.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Requisitos del Logo:</h5>
                        <ul class="checklist">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Formatos aceptados:</strong> JPG, JPEG, PNG
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Tamano maximo:</strong> 2MB
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Recomendacion:</strong> Imagen cuadrada para mejor visualizacion
                                </div>
                            </li>
                        </ul>

                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Consejo:</strong> Si no tienes un logo ahora, puedes omitir este paso y agregarlo despues desde la configuracion de tu cuenta.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 5: Codigo de Referido -->
                <section id="paso-5" class="step-card">
                    <div class="step-number">6</div>
                    <h3 class="step-title">Codigo de Referido (Opcional)</h3>
                    <div class="step-content">
                        <p>Si alguien te refirio a SAMI, ingresa su codigo de referido para obtener un descuento:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/register/codigo.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-gift"></i>
                            <div>
                                <strong>Beneficios del Codigo de Referido:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Tu recibiras un descuento inmediato en tu primera factura</li>
                                    <li>La persona que te refirio recibira un beneficio en su proxima factura</li>
                                </ul>
                            </div>
                        </div>

                        <div class="info-box info-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Nota:</strong> Una vez registrado, tu tambien recibiras tu propio codigo de referido para compartir con otros colegas.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 6: Terminos y Envio -->
                <section id="paso-6" class="step-card step-success">
                    <div class="step-number">7</div>
                    <h3 class="step-title">Aceptar Terminos y Enviar</h3>
                    <div class="step-content">
                        <p>Finalmente, debes aceptar los terminos y condiciones y la politica de privacidad:</p>

                        <div>
                            <img src="{{ asset('images/tutorial/register/terminos.png') }}" alt="" style="width: 100%;">
                        </div>

                        <div class="sub-step">
                            <h6><i class="fas fa-check-square me-2"></i>Pasos Finales</h6>
                            <ol>
                                <li>Marca la casilla <strong>"Estoy de acuerdo con los Términos de Servicios y Políticas de Privacidad"</strong></li>
                                <!--<li>Completa la verificación CAPTCHA (si aparece)</li>-->
                                <li>Haz clic en el botón <strong>"Registrarse"</strong></li>
                            </ol>
                        </div>

                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> No podras completar el registro sin aceptar los terminos y condiciones. Te recomendamos leerlos antes de aceptar.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Despues del Registro -->
                <section id="post-registro" class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-flag-checkered me-2"></i>Despues del Registro</h3>
                    <div class="step-content">
                        <p>Una vez enviado el formulario exitosamente, seras redirigido a la pagina de confirmacion:</p>

                        <div class="yappy-img">
                            <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/register_completed.png') }}" alt="">
                        </div>

                        <h5 class="mt-4">Lo que sucede automaticamente:</h5>
                        <ul class="checklist">
                            <li>
                                <i class="fas fa-envelope text-primary"></i>
                                <div>
                                    <strong>Correo de Bienvenida</strong><br>
                                    <small class="text-muted">Recibiras un email con tus credenciales de acceso</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-user-md text-primary"></i>
                                <div>
                                    <strong>Perfil de Medico Creado</strong><br>
                                    <small class="text-muted">Si seleccionaste un plan individual, se crea tu perfil de practitioner</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-building text-primary"></i>
                                <div>
                                    <strong>Clinica/Consultorio Creado</strong><br>
                                    <small class="text-muted">Se crea tu organizacion en el sistema</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-file-invoice-dollar text-primary"></i>
                                <div>
                                    <strong>Factura Generada</strong><br>
                                    <small class="text-muted">Se genera la primera factura de tu suscripción</small>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-ticket-alt text-primary"></i>
                                <div>
                                    <strong>Codigo de Referido Asignado</strong><br>
                                    <small class="text-muted">Recibiras tu codigo unico para referir a otros</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Flujo de Loggin -->
                <section id="loggin" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Flujo de Inicio de Sesión</h3>
                    <div class="step-content">
                        <!--<p>Para los interesados en conocer el proceso tecnico, aqui explicamos que ocurre en el sistema durante el registro:</p>-->

                        <div class="process-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>1. Ir a la pantalla de Login</h6>
                                    <p>Puede ingresar a la pantalla de inicio de sesión de dos formas: <br> 1. Luego de haber completado su registro, en la pantalla de <strong>"Registro Exitoso"</strong>, haz clic en el botón <strong>"Ir al Login"</strong> <br>2. Desde la pantalla del Landing, haz en clic en <strong>"Ingresar"</strong> que se encuentra en el menú superior.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/registration-login.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>2. Ingresar Credenciales</h6>
                                    <p>Ingrese su correo y contraseña que proporcinó en el registro.</p>
                                    <div>
                                        <img  src="{{ asset('images/tutorial/register/login-form.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>3. Ingreso a la Plataforma</h6>
                                    <p>Luego de haber ingresado sus credenciales correctamente, podrá entrar al Sistema dónde vera el Dashboard principal, un mensaje en la parte superior que su activación está pendiente y el asistente de configuración.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/logged.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Asistente de Configuración -->
                <section id="assistant" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Asistente de Configuración</h3>
                    <div class="step-content">
                        <p>Si no sabe como configurar lo básico para comenzar a usar nuestro sistema, contamos con un asistente de configurarción que le guiará paso a paso:</p>

                        <div class="process-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>1. Pago de Suscripción</h6>
                                    <p>Para poder agendar citas y llenar sus consultas, deberá primero hacer el pago de la suscripción del plan que seleccionó, haga clic en <strong>+Ver Facturas</strong> y se desplegará la pantalla de Detalle Factura donde podrá registrar su pago.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/assistant/1step.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>2. Configurar Sucursal</h6>
                                    <p>Luego de registrar su pago, el asistente pasa automáticamente al paso 2 que es la configuración de una sucursal, haz clic en <strong>+Crear Sucursal</strong>, llena los datos y haga clic en <strong>"Registrar"</strong> para registrar su sucursal.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/assistant/2step.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>3. Configurar Consultorio</h6>
                                    <p>Luego de registrar su primera sucursal, el asistente pasa automáticamente al paso 3 que corresponde a configuración de un consultorio, haz clic en <strong>+Crear Consultorio</strong>, llena los datos y haga clic en <strong>"Registrar"</strong> para registrar su consultorio.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/assistant/3step.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>4. Regisrar Paciente</h6>
                                    <p>Luego de registrar su primer consultorio, el asistente pasa automáticamente al paso 4 que corresponde al registro de un paciente, haz clic en <strong>+Regisrar Paciente</strong>, llena los datos y haga clic en <strong>"Registrar"</strong> para registrar su paciente.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/assistant/4step.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Importante:</strong> Este tutorial cubre las <strong>Configuraciones Básicas</strong>, el <strong>Asistente de Configurción</strong> posee <strong>8 pasos</strong> en total que le recomendamos seguir para poder tener una mejor experiencia y pueda aprovechar al máximo el uso de nuestro sistema.
                            </div>
                        </div>
                    </div>
                </section>


                <!-- Proceso de Pago -->
                <section id="pago" class="step-card step-important">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-credit-card me-2"></i>Proceso de Pago</h3>
                    <div class="step-content">
                        <!--<p>Si tu plan no incluye periodo de prueba, deberas completar el pago para activar tu cuenta:</p>-->

                        <div>
                            <img src="{{ asset('images/tutorial/register/pago.png') }}" alt="" style="width: 100%;">
                        </div>

                        <h5 class="mt-4">Estados de tu Suscripcion:</h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded text-center">
                                    <span class="status-badge status-pending">PENDIENTE DE ACTIVACION</span>
                                    <p class="mt-2 mb-0 small">Esperando pago</p>
                                </div>
                            </div>
                            <!--<div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded text-center">
                                    <span class="status-badge status-trial">EN PERIODO DE PRUEBA</span>
                                    <p class="mt-2 mb-0 small">Prueba gratuita activa</p>
                                </div>
                            </div>-->
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded text-center">
                                    <span class="status-badge status-active">ACTIVA</span>
                                    <p class="mt-2 mb-0 small">Cuenta completamente activa</p>
                                </div>
                            </div>
                        </div>

                        <div class="sub-step">
                            <h6><i class="fab fa-whatsapp me-2"></i>Pago con Yappy</h6>
                            <ol>
                                <li>Haz clic en el boton <strong>"Pagar con Yappy"</strong></li>
                                <li>Se abrira la aplicacion de Yappy o recibiras una notificacion</li>
                                <li>Confirma el pago en tu telefono</li>
                                <li>Tu cuenta se activara automaticamente</li>
                            </ol>
                        </div>
                        <div class="info-box info-tip">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>Métodos de Pago Alternativos:</strong> Si necesitas pagar con otro metodo (transferencia, tarjeta, etc.), contacta a nuestro equipo de soporte.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Que Sucede Internamente -->
                <section id="que-sucede" class="step-card">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Flujo de pago con el botón de Yappy</h3>
                    <div class="step-content">
                        <!--<p>Para los interesados en conocer el proceso tecnico, aqui explicamos que ocurre en el sistema durante el registro:</p>-->

                        <div class="process-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>1. Clic en el Botón de Yappy</h6>
                                    <p>Puede procesar su pago de esta forma, puede acceder a este botón de dos formas: <br> 1. Luego de haber completado su registro, en la pantalla de <strong>"Registro Exitoso"</strong> <br>2. Debe iniciar sesión, ir a <strong>Suscripción</strong> → <strong>Facturas</strong> → <strong>Detalle</strong>(botón del ojo) y se despliega la pantalla de detalle de factura.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/yappy.png') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>2. Iniciar sesión en Banca en Línea</h6>
                                    <p>Deberá iniciar sesión y entrar a la sección de Yappy, dentro de Yappy, podrá visualizar una notificación que dice <strong>"!Te pidieron un Yappy! →"</strong>.</p>
                                    <div class="yappy-img">
                                        <img  class="yappy-imgsize" src="{{ asset('images/tutorial/register/yappy/notification.jpeg') }}" alt="" >
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>3. Notificación de Yappy</h6>
                                    <p>Se desplegará la pantalla de notificaciones, para pagar, deberá dar clic en <strong>"Ver más"</strong>.</p>
                                    <div class="yappy-img">
                                        <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/yappy/notification_detail.jpeg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>4. Pago de Suscripción</h6>
                                    <p>Esta pantalla usted procederá a realizar el pago de su suscripción, deberá dar clic en <strong>"Sí, pagar"</strong>.</p>
                                    <div class="yappy-img">
                                        <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/yappy/pre-confirmation.jpeg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>5. Confirmación de Pago</h6>
                                    <p>Cómo último paso, deberá confirmar la transacción para proceder con el pago de forma satisfactoria, de clic en <strong>"Confirmar"</strong>.</p>
                                    <div class="yappy-img">
                                        <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/yappy/confirmation.jpeg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>6. Pago de Suscripción</h6>
                                    <p>Se desplegará la factura final de Yappy, confirmando que ya se realizó el pago.</p>
                                    <div class="yappy-img">
                                        <img class="yappy-imgsize" src="{{ asset('images/tutorial/register/yappy/payment.jpeg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>7. Cambio de estado de factura</h6>
                                    <p>Luego de haber hecho su pago de forma satisfactoria, la factura en nuestro sistema cambia de pendiente a pagada de forma automática.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/yappy/invoice-status.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6>8. Notificación de Suscripción</h6>
                                    <p>Luego de haber hecho su pago de forma satisfactoria, se le notificará mediante un correo que su suscripción fue activada.</p>
                                    <div>
                                        <img src="{{ asset('images/tutorial/register/yappy/email.jpeg') }}" alt="" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!--<div class="info-box info-note">
                            <i class="fas fa-database"></i>
                            <div>
                                <strong>Modelos de Datos Creados:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><code>Client</code> - Tu organizacion/clinica</li>
                                    <li><code>User</code> - Tu cuenta de acceso</li>
                                    <li><code>UserClient</code> - Relacion usuario-organizacion</li>
                                    <li><code>Practitioner</code> - Tu perfil de medico (si aplica)</li>
                                    <li><code>PractitionerQualification</code> - Tu especialidad</li>
                                    <li><code>ClientSubscription</code> - Tu suscripcion</li>
                                    <li><code>ClientInvoice</code> - Tu factura</li>
                                    <li><code>ReferralCode</code> - Tu codigo de referido</li>
                                </ul>
                            </div>
                        </div>-->
                    </div>
                </section>

                <!-- Siguientes Pasos -->
                <section id="siguientes-pasos" class="step-card step-success">
                    <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguientes Pasos</h3>
                    <div class="step-content">
                        <p>Una vez completado el registro y activada tu cuenta, deberas configurar tu clinica para empezar a atender pacientes:</p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-building text-primary me-2"></i>
                                            Crear Sucursales
                                        </h5>
                                        <p class="card-text text-muted">Configura las ubicaciones fisicas donde atenderas pacientes.</p>
                                        <a href="{{ route('help.branches') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-arrow-right me-1"></i>Ver Guia
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-door-open text-success me-2"></i>
                                            Crear Consultorios
                                        </h5>
                                        <p class="card-text text-muted">Define los consultorios dentro de cada sucursal.</p>
                                        <a href="{{ route('help.consulting-rooms') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-arrow-right me-1"></i>Ver Guia
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-users text-warning me-2"></i>
                                            Registrar Pacientes
                                        </h5>
                                        <p class="card-text text-muted">Comienza a registrar a tus pacientes en el sistema.</p>
                                        <a href="{{ route('help.patients') }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-clock me-1"></i>Ver Guia
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-user-md text-info me-2"></i>
                                            Agendar Citas
                                        </h5>
                                        <p class="card-text text-muted">empieza a agandar citas a tus pacientes.</p>
                                        <a href="{{ route('help.appointments') }}" class="btn btn-info btn-sm text-white">
                                            <i class="fas fa-clock me-1"></i>Ver guia
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="info-box info-tip">
                            <i class="fas fa-bell"></i>
                            <div>
                                <strong>Notificacion de Configuracion:</strong> Despues del registro, recibiras una notificacion en el sistema indicandote que debes configurar tu sucursal y consultorio antes de poder agendar citas.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Contact Support -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><i class="fas fa-question-circle me-2 text-primary"></i>Tienes preguntas sobre el registro?</h5>
                                <p class="text-muted mb-0">Nuestro equipo de soporte esta disponible para ayudarte con cualquier duda.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="https://wa.me/5078316174" target="_blank" class="btn btn-success">
                                    <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
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

        // Highlight current section in TOC
        const sections = document.querySelectorAll('section[id]');
        const tocLinks = document.querySelectorAll('.toc-list a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 150;
                if (window.pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.style.background = '#e8eaf6';
                    link.style.fontWeight = '600';
                } else {
                    link.style.background = '';
                    link.style.fontWeight = '';
                }
            });
        });
    </script>
</body>
</html>
