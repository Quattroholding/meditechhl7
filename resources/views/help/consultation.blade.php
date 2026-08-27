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
    background: linear-gradient(180deg, #d32f2f 0%, #f44336 100%);
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
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
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
    border-left: 5px solid #d32f2f;
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
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.3rem;
    box-shadow: 0 4px 10px rgba(211, 47, 47, 0.4);
}

.step-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #c62828;
    margin-bottom: 15px;
    padding-left: 45px;
}

.step-content {
    color: #555;
    line-height: 1.8;
}

/* Screenshot Placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    border: 3px dashed #f44336;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 25px 0;
    position: relative;
}

.screenshot-placeholder i {
    font-size: 3rem;
    color: #d32f2f;
    margin-bottom: 15px;
}

.screenshot-placeholder h5 {
    color: #d32f2f;
    font-weight: 600;
    margin-bottom: 10px;
}

.screenshot-placeholder p {
    color: #f44336;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.screenshot-placeholder .dimensions {
    position: absolute;
    bottom: 10px;
    right: 15px;
    font-size: 0.75rem;
    color: #ef9a9a;
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
    background: #c62828;
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
    color: #c62828;
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
    margin-bottom: 10px;
}

.toc-list a {
    color: #d32f2f;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    background: #ffebee;
    color: #c62828;
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
    border-left: 3px solid #d32f2f;
}

.sub-step h6 {
    color: #c62828;
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
    color: #d32f2f;
    margin-top: 3px;
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

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(211, 47, 47, 0.4);
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
        max-width: 100vw;
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
        text-align: center;
        padding: 10px 12px;
        font-size: 14px;
        border-bottom: 1px solid #f0f0f0;
    }

    .field-table td:last-child {
        border-bottom: none;
    }

    .field-table td[data-label="Campo"] {
        background: #c62828;
        color: white;
        font-size: 15px;
        padding: 12px;
        border-bottom: 2px solid #d32f2f;
    }

    .field-table td[data-label="Campo"] strong {
        color: white;
    }

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

    .step-card, .sub-step {
        border-left: none;
        padding: 25px;
        border-bottom: 5px solid #d32f2f;
    }

    .step-title {
        padding-left: 0;
        text-align: center;
        padding-top: 8%;
    }

    .info-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
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
    @include('help.sidebar', ['active' => 'consultation'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('help.consultation') }}">Guias de Consultas Medicas</a></li>
            <li class="breadcrumb-item active">Realizar Consulta</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-stethoscope me-3"></i>Guía para Realizar Consultas Médicas</h1>
        <p>Aprende a documentar consultas médicas de manera completa y eficiente en SAMI.</p>
    </div>
@stop
@section('table-content')
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
        <section id="introduccion" class="step-card step-info">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>1. Introducción al Módulo de Consultas</h3>
            <div class="step-content">
                <p>El módulo de <strong>Consultas Médicas</strong> es el corazón del sistema SAMI, donde los profesionales de salud documentan cada encuentro con sus pacientes. Este módulo permite registrar de manera estructurada toda la información clínica relevante.</p>

                <div>
                    <img src="{{ asset('images/tutorial/encounters/encounter_view.png') }}" alt="" style="width: 100%;">
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="info-box info-tip">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>Características Principales</strong>
                                <ul class="mb-0">
                                    <li>Interfaz intuitiva con secciones expandibles</li>
                                    <li>Autoguardado automático de información</li>
                                    <li>Plantillas personalizables por especialidad</li>
                                    <li>Acceso rápido a historia clínica del paciente</li>
                                    <li>Generación automática de documentos médicos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box info-note">
                            <i class="fas fa-lightbulb text-primary"></i>
                            <div>
                                <strong>Requisitos Previos</strong>
                                <ul class="mb-0">
                                    <li>El paciente debe estar <a href="{{ route('help.patients') }}">registrado</a> en el sistema</li>
                                    <li>Tener una <a href="{{ route('help.appointments') }}">cita agendada</a> para el paciente</li>
                                    <li>La cita debe estar en estado "LLegó"</li>
                                    <li>Tener configuradas las plantillas de consulta (opcional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Accessing Consultation -->
        <section id="acceder" class="step-card">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-sign-in-alt me-2"></i>2. Acceder a una Consulta</h3>
            <div class="step-content">
                <p>Existen varias formas de acceder al módulo de consulta para atender a un paciente:</p>

                <!-- Step 1 -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">1.</span> Desde el Calendario de Citas</h6>
                    <p>Esta es la forma más común de acceder a una consulta:</p>
                    <ul>
                        <li>Navegue a <strong>Citas → Calendario</strong></li>
                        <li>Estando en la vista hoy, localice la cita del paciente en el calendario</li>
                        <li>Registre la llegada del paciente en el botón que dice "Registrar llegada"</li>
                        <li>Luego de que la cita se haya actualizado, haga clic en el botón <strong>"Iniciar"</strong></li>
                        <li>Se desplegará la plantilla de consulta, para que pueda comenzar a llenar la información correspondiente</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_start.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">2.</span> Desde el Listado de Citas</h6>
                    <p>También puede acceder desde la vista de lista:</p>
                    <ul>
                        <li>Navegue a <strong>Citas → Lista Citas</strong></li>
                        <li>Busque la cita del paciente usando los filtros</li>
                        <li>En la columna de Estatus, haga clic en <strong>"Registrar Llegada"</strong></li>
                        <li>Nuevamente, en la columna de Estatus, haga clic en <strong>"Iniciar Consulta"</strong></li>
                        <li>Le saldrá una ventana emergente indicando que el paciente está listo para consulta, haga clic en el botón <strong>"Iniciar Consulta"</strong></li>
                        <li>Se desplegará la plantilla de consulta, para que pueda comenzar a llenar la información correspondiente</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_start2.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">3.</span> Desde el Perfil del Paciente</h6>
                    <p>Si está revisando el perfil de un paciente:</p>
                    <ul>
                        <li>En el perfil del paciente, vaya a la pestaña de <strong>"Citas"</strong></li>
                        <li>Localice la cita actual</li>
                        <li>En la columna de Estado, haga clic en <strong>"Registrar Llegada"</strong></li>
                        <li>Nuevamente, en la columna de Estatus, haga clic en <strong>"Iniciar Consulta"</strong></li>
                        <li>Le saldrá una ventana emergente indicando que el paciente está listo para consulta, haga clic en el botón <strong>"Iniciar Consulta"</strong></li>
                        <li>Se desplegará la plantilla de consulta, para que pueda comenzar a llenar la información correspondiente</li>
                    </ul>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_startenc.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <div class="info-box info-warning">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <div>
                        <strong>Importante</strong>
                        <p class="mb-0">Solo puede iniciar una consulta si la cita está en estado <strong>"LLegó"</strong>. Si la cita está en otro estado, primero debe actualizarla, ya sea que tenga que <strong>"Confirmar"</strong> o <strong>"Registrar Llegada"</strong>, todo dependerá del estado que tenga la cita.</p>
                    </div>
                </div>

                <!-- Notificación Doctor -->
                <div class="sub-step mt-4">
                    <h6><i class="fas fa-bell text-danger me-2"></i>Notificación de Paciente Listo (Tiempo Real)</h6>
                    <p>Para agilizar el flujo de atención, SAMI cuenta con un sistema de notificaciones en tiempo real. Cuando el personal de recepción marca la llegada de un paciente e inicia el proceso desde su terminal, si el médico tiene su sesión abierta en SAMI, se le desplegará automáticamente una ventana emergente (modal) con la información del paciente.</p>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-12 mb-3">
                            <img src="{{ asset('images/tutorial/encounters/patient_ready_modal.png') }}" alt="Modal de Paciente Listo" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                        </div>
                        <div class="col-md-12">
                            <p>Desde esta ventana, el doctor puede visualizar el nombre del paciente, la hora de la cita y el consultorio asignado, pudiendo elegir una de las siguientes acciones:</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light h-100 text-center">
                                        <h6 class="fw-bold"><i class="fas fa-play text-primary me-2"></i>Iniciar Consulta</h6>
                                        <p class="small mb-0">Abre de forma inmediata el módulo de consulta para empezar la atención médica.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light h-100 text-center">
                                        <h6 class="fw-bold"><i class="fas fa-times text-success me-2"></i>Cerrar</h6>
                                        <p class="small mb-0">Cierra el aviso. El doctor podrá iniciar la consulta manualmente más tarde desde su agenda.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light h-100 text-center">
                                        <h6 class="fw-bold"><i class="fas fa-clock text-warning me-2"></i>Recordar en 5 min</h6>
                                        <p class="small mb-0">Pospone el aviso para que vuelva a aparecer automáticamente después de 5 minutos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Consultation Sections -->
        <section id="secciones" class="step-card">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-list me-2"></i>3. Secciones de la Consulta</h3>
            <div class="step-content">
                <p>La interfaz de consulta está organizada en secciones expandibles (popup) que se cargan dinámicamente. Cada sección contiene campos específicos para documentar diferentes aspectos de la consulta médica.</p>

                <div class="info-box info-note">
                    <i class="fas fa-info-circle text-primary"></i>
                    <div>
                        <strong>Secciones Personalizables</strong>
                        <p class="mb-0">Las secciones que ve pueden variar según su especialidad médica y las plantillas configuradas en <a href="{{ route('help.settings') }}">Configuraciones</a>. Los administradores pueden personalizar qué secciones aparecen para cada profesional.</p>
                    </div>
                </div>

                <h4 style="margin-top: 25px;">Secciones Comunes</h4>

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
                            <p>Indicaciones médicas, medicamentos prescritos, órdenes de laboratorio, imágenes, procedimientos y referencia a especialistas.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="icon-circle">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5>Servicios Facturables</h5>
                            <p>Servicios personalizados y Servicios asociados a CPTs.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="icon-circle">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h5>Notas Generales</h5>
                            <p>Nota General de la consulta.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="icon-circle">
                                <i class="fas fa-cubes"></i>
                            </div>
                            <h5>Suministro</h5>
                            <p>Gestión del inventario médico. Permite registrar y entregar suministros, medicamentos o materiales del inventario al paciente durante la consulta, con seguimiento de cantidades y documentación.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="section-card">
                            <div class="icon-circle">
                                <i class="fas fa-cloud"></i>
                            </div>
                            <h5>Archivos</h5>
                            <p>Respaldo seguro de imágenes y documentos. Se integra con Dropbox para almacenar copias de seguridad de archivos asociados a la consulta en su cuenta privada. Requiere configuración previa de credenciales de Dropbox.</p>
                        </div>
                    </div>
                </div>
                {{--}}

                <div class="info-box info-tip">
                    <i class="fas fa-lightbulb text-success"></i>
                    <div>
                        <strong>Carga Progresiva</strong>
                        <p class="mb-0">Las secciones se cargan de forma progresiva para mejorar el rendimiento. Cada sección se carga cuando la expande por primera vez, lo que hace que la interfaz sea más rápida y fluida.</p>
                    </div>
                </div>
                {{--}}
            </div>
        </section>

        <!-- Section 4: Documenting Consultation -->
        <section id="documentar" class="step-card">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-edit me-2"></i>4. Documentar la Consulta</h3>
            <div class="step-content">
                <p>A continuación, se describe el proceso paso a paso para documentar una consulta médica completa:</p>

                <!-- Step 1: Patient Info -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">1.</span> Revisar Información general del Paciente y de la Cita</h6>
                    <p>Al iniciar la consulta, verá un encabezado con la información básica del paciente y de la cita:</p>
                    <ul>
                        <li><strong>Detalle de atención:</strong> fecha, consultorio, # de consulta y tipo de servicio</li>
                        <li><strong>Datos del paciente:</strong> nombre, edad, género, tipo y número de identificación</li>
                        <li><strong>Equipo médico:</strong> nombre y especialidad</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_patientinfo.png') }}" alt="" style="width: 100%;">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <strong>Menú Inferior</strong>
                            <p class="mb-0">
                                En la parte inferior de la pantalla encontrará un boton de acceso rápido a la historia clínica del paciente y documentos previos haciendo clic en <strong>Ver información del paciente</strong>. Además, encontrará una guía, ubicada al centro de este menú, que muestra las secciones en amarillo, si las secciones que son requeridas para poder finalizar la consulta, se encuentran vacías.</p>
                        </div>
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_sidemenu.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 2: Servicios Facturables -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">2.</span> Agregar Servicios Facturables</h6>
                    <p>Expanda la primera sección <strong>"Servicios Facturables"</strong>, elija la categoría y agregue los servicios necesarios que fueron previamente registrados en la sección de Configuraciones → Catálogo de Servicios.</p>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_services.png') }}" alt="" style="width: 100%;">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <strong>Servicios Facturables</strong>
                            <p class="mb-0">Estos son los servicios que serán cargados en la factura al finalizar la consulta, esta factura se le cobrará al paciente.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Chief Complaint -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">3.</span> Registrar Motivo de Consulta</h6>
                    <p>Expanda la primera sección <strong>"Motivo de Consulta"</strong> y documente:</p>
                    <ul>
                        <li>La razón principal de la visita en palabras del paciente</li>
                        <li>Síntomas principales que presenta</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_mot.png') }}" alt="" style="width: 100%;">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-save text-success"></i>
                        <div>
                            <strong>Autoguardado</strong>
                            <p class="mb-0">El sistema guarda automáticamente la información mientras escribe. No es necesario presionar un botón de guardar después de cada sección.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Vital Signs -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">4.</span> Registrar Signos Vitales</h6>
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
                                <td data-label="Campo"><strong>Temperatura</strong></td>
                                <td data-label="Descripcion">Temperatura corporal</td>
                                <td data-label="Unidad">°C</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Frecuencia Cardíaca</strong></td>
                                <td data-label="Descripcion">Pulsaciones por minuto</td>
                                <td data-label="Unidad">lpm</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Presión Arterial</strong></td>
                                <td data-label="Descripcion">Sistólica</td>
                                <td data-label="Unidad">mmHg</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Presión Arterial</strong></td>
                                <td data-label="Descripcion">Diastólica</td>
                                <td data-label="Unidad">mmHg</td>
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
                            <tr>
                                <td data-label="Campo"><strong>Glicemia Capilar</strong></td>
                                <td data-label="Descripcion">Medición del nivel de glucosa</td>
                                <td data-label="Unidad">mg/dL</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_vitalsigns.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 5: Current Illness -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">5.</span> Llenar datos de Enfermedad Actual</h6>
                    <p>En la sección de <strong>"Enfermedad Actual"</strong>, complete los campos correspondientes:</p>

                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Sección</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Campo"><strong>Ubicación</strong></td>
                                <td data-label="Descripcion">Seleccione el área anatómica relacionada con el (los) síntoma(s) que le informe el paciente.</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Gravedad</strong></td>
                                <td data-label="Descripcion">Indique la severidad del(los) síntoma(s).</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Duración</strong></td>
                                <td data-label="Descripcion">Indique desde hace cuánto tiempo aproximadamente presenta el(los) síntoma(s).</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Momento</strong></td>
                                <td data-label="Descripcion">Indique el predominio horario en el que se presenta el(los) síntoma(s).</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Factores Agravantes</strong></td>
                                <td data-label="Descripcion">Indique los factores, actividades o situaciones que empeoran los síntomas.</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Atenuantes</strong></td>
                                <td data-label="Descripcion">Indique los factores o medidas que alivian o disminuyen la intensidad de los síntomas.</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Síntomas Asociados</strong></td>
                                <td data-label="Descripcion">Registre otros síntomas que se presentan de forma conjunta con el síntoma principal.</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Descripción</strong></td>
                                <td data-label="Descripcion">Detalle narrativo adicional sobre la evolución y características de la enfermedad actual.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ai11.png') }}" alt="" style="width: 100%;">
                    </div>
                    {{--}}
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ai12.png') }}" alt="" style="width: 100%;">
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ai13.png') }}" alt="" style="width: 100%;">
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ai.png') }}" alt="" style="width: 100%;">
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ai2.png') }}" alt="" style="width: 100%;">
                    </div>
                    {{--}}
                </div>

                <!-- Step 6: Physical Exam -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">6.</span> Documentar Examen Físico</h6>
                    <p>Complete los hallazgos del examen físico en las áreas correspondientes:</p>
                    <ul>
                        <li><strong>Apariencia General:</strong> Estado general del paciente</li>
                        <li><strong>Examen por Sistemas:</strong> Hallazgos específicos por cada sistema</li>
                        <li>Utilice las sugerencias cuando quiera indicar que el aparato o sistema evaluado está "normal"</li>
                        <li>Agregue notas adicionales según sea necesario</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_pe.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 7: Diagnosis -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">7.</span> Agregar Diagnósticos</h6>
                    <p>En la sección de <strong>"Diagnóstico"</strong>:</p>
                    <ul>
                        <li>Haga clic en <strong>"Seleccionar Diagnóstico"</strong></li>
                        <li>Busque el diagnóstico por nombre o código CIE-10</li>
                        <li>Seleccione la gravedad (leve, moderado, severo, critico)</li>
                        <li>Agregue notas adicionales si es necesario</li>
                        <li>Puede agregar múltiples diagnósticos</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_diag.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 8: Medical Orders -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">8.</span> Solicitar Imágenes, Laboratorios y Procedimiento</h6>
                    <p>Tipos de órdenes médicas:</p>
                    <ul>
                        <li>En caso de solicitar exámenes, seleccione la sección de <strong>"Laboratorios"</strong></li>
                        <div>
                            <img src="{{ asset('images/tutorial/encounters/encounter_labs.png') }}" alt="" style="width: 100%;">
                        </div>
                        <li>En caso de solicitar imágenes, seleccione la sección de <strong>"Imágenes"</strong></li>
                        <div>
                            <img src="{{ asset('images/tutorial/encounters/encounter_imgs.png') }}" alt="" style="width: 100%;">
                        </div>
                        <li>En caso de solicitar algún procedimiento, seleccione la sección de <strong>"Procedimientos"</strong></li>
                        <div>
                            <img src="{{ asset('images/tutorial/encounters/encounter_proc.png') }}" alt="" style="width: 100%;">
                        </div>
                    </ul>
                    <p>En caso de solicitar algún tipo de orden médica:</p>
                    <ol>
                        <li>Seleccione el estudio requerido buscando por descripción, código CPT o buscándolo desde el listado de acceso rápido.</li>
                        <li>Agregue instrucciones adicionales (si se requiere).</li>
                        <li>Si desea eliminar un ítem agregado, haga clic en el botón <strong>"Borrar"</strong> que aparece en la parte superior del recuadro del estudio.</li>
                    </ol>

                    <div class="info-box info-tip">
                        <i class="fas fa-bolt text-success"></i>
                        <div>
                            <strong>Listado de Acceso Rápido</strong>
                            <p class="mb-0">
                                Para agilizar la solicitud de estudios frecuentes, puede utilizar el botón <strong>"Listado de Acceso Rápido"</strong>.
                                Este listado le permite seleccionar rápidamente estudios previamente marcados como favoritos.
                                Puede configurar sus propios accesos rápidos en la sección de <a href="{{ route('help.settings') }}">Configuraciones → Accesos Rápidos</a>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 9: Referral -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">9.</span> Agregar Referencia Especialista</h6>
                    <p>En caso de que usted considere que el paciente debe ser evaluado o referido a algún especialista, puede agregar una referencia especialista:</p>
                    <ol>
                        <li>Escriba la <strong>Especialidad</strong> a la que referirá al paciente</li>
                        <li>Agregue una <strong>Nota de Referencia</strong></li>
                        <li>En caso de que desee referir a alguien y que sea usuario de nuestro sistema puede agregarlo a la referencia dando clic a <strong>Ver Directorio Médico</strong></li>
                        <li>En caso de que desee referir a alguien que no sea usuario de nuestro sistema puede agregarlo usando el botón especialista externo donde se le pedira el nombre , teléfono y la clinica de atención.</li>
                        <li>Si desea eliminar un ítem agregado, haga clic en el botón <strong>"Borrar"</strong> que aparece en la parte superior del recuadro de la referencia agregada.</li>
                    </ol>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_refesp.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 10: Prescription -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">10.</span> Crear Prescripción Médica</h6>
                    <p>Para prescribir medicamentos, siga estos pasos:</p>
                    <ol>
                        <li>Vaya a la sección <strong>"Medicamentos"</strong>.</li>
                        <li>Haga clic en el campo de búsqueda <strong>"Buscar Medicamento por nombre, código NDC o nombre genérico"</strong>.</li>
                        <li>Busque el medicamento por nombre comercial o genérico y selecciónelo de la lista desplegable.</li>
                        <li>Complete los campos del formulario de prescripción detallados en la tabla a continuación.</li>
                        <li>Repita el proceso para todos los medicamentos adicionales que requiera el paciente.</li>
                        <li>Si desea eliminar un ítem agregado, haga clic en el botón <strong>"Borrar"</strong> que aparece en la parte superior del recuadro del medicamento agregado.</li>
                    </ol>

                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Campo"><strong>Cantidad</strong></td>
                                <td data-label="Descripcion">Indique la cantidad o dosis por cada toma (ej: 1 tableta, 5ml).</td>
                            </tr>
                            {{--}}
                            <tr>
                                <td data-label="Campo"><strong>Frecuencia</strong></td>
                                <td data-label="Descripcion">Establezca el intervalo de tiempo entre cada dosis (ej: cada 8 horas).</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Vía</strong></td>
                                <td data-label="Descripcion">Seleccione la vía de administración (Oral, Intravenosa, Tópica, etc.).</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Duración</strong></td>
                                <td data-label="Descripcion">Indique el número total de días que durará el tratamiento médico.</td>
                            </tr>
                            {{--}}
                            <tr>
                                <td data-label="Campo"><strong>Indicaciones</strong></td>
                                <td data-label="Descripcion">Prescripción completada automáticamente al llenar los campos previos.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_mp.png') }}" alt="" style="width: 100%;">
                    </div>

                    <h5 style="margin-top: 25px;">Uso del Historial de Medicamentos</h5>
                    <p>Esta potente herramienta permite agilizar el proceso de prescripción al permitirle consultar y reutilizar tratamientos previos del paciente:</p>
                    <ul>
                        <li><strong>Acceso:</strong> Presione el botón <strong>"Historial de Medicamentos"</strong> ubicado al final de la sección de prescripción.</li>
                        <li><strong>Visualización:</strong> Se desplegará un panel lateral con el registro cronológico de todas las recetas emitidas previamente. Cada entrada muestra la fecha y el médico tratante.</li>
                        <li><strong>Selección Inteligente:</strong> Puede marcar medicamentos individuales de diferentes fechas o seleccionar una receta completa mediante la opción <strong>"Seleccionar Receta"</strong>.</li>
                        <li><strong>Acción de Copiado:</strong> El botón superior <strong>"Copiar Seleccionados"</strong> indica cuántos medicamentos ha marcado. Al presionarlo, el sistema importará automáticamente estos medicamentos a su prescripción actual, incluyendo dosis, frecuencia e indicaciones.</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_medhis.png') }}" alt="" style="width: 100%;">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-bolt text-success"></i>
                        <div>
                            <strong>Listado de Acceso Rápido</strong>
                            <p class="mb-0">
                                Para agilizar la solicitud de medicamentos frecuentes, puede utilizar el botón <strong>"Listado de Acceso Rápido"</strong>.
                                Este listado le permite seleccionar rápidamente medicamentos previamente marcados como favoritos.
                                <br>Puede configurar sus propios accesos rápidos haciendo clic a la estrella que sale en lista desplegable de medicamentos cuando está en la consulta.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 11: Supplies -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">11.</span> Registrar Suministros Entregados</h6>
                    <p>En la sección de <strong>"Suministro"</strong>, registre cualquier medicamento, material o insumo del inventario que haya entregado al paciente durante la consulta:</p>
                    <ol>
                        <li>Haga clic en <strong>"Agregar Suministro"</strong></li>
                        <li>Busque el suministro en el inventario disponible</li>
                        <li>Indique la cantidad entregada</li>
                        <li>Agregue notas adicionales si es necesario (instrucciones de uso, cuidados, etc.)</li>
                        <li>Puede agregar múltiples suministros si lo requiere</li>
                        <li>Si desea eliminar un ítem agregado, haga clic en el botón <strong>"Borrar"</strong> que aparece en la parte superior del recuadro del suministro</li>
                    </ol>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <strong>Gestión de Inventario</strong>
                            <p class="mb-0">El registro de suministros entregados se integra automáticamente con su sistema de inventario, actualizando las cantidades disponibles en la clínica. Esto ayuda a mantener el control de existencias y a realizar reorden automático cuando sea necesario.</p>
                        </div>
                    </div>

                    <div class="screenshot-placeholder">
                        <i class="fas fa-image"></i>
                        <h5>Sección de Suministros</h5>
                        <p>Captura de pantalla: Formulario de registro de suministros entregados</p>
                        <span class="dimensions">Espacio reservado para captura</span>
                    </div>
                </div>

                <!-- Step 12: Files -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">12.</span> Respaldar Archivos en la Nube</h6>
                    <p>En la sección de <strong>"Archivos"</strong>, puede respaldar de forma segura imágenes, documentos y archivos asociados a la consulta en su cuenta de Dropbox:</p>
                    <ol>
                        <li>Primero, asegúrese de tener configuradas sus credenciales de Dropbox en <a href="{{ route('help.settings') }}">Configuraciones</a></li>
                        <li>En la sección de <strong>"Archivos"</strong>, haga clic en <strong>"Seleccionar Archivo"</strong></li>
                        <li>Elija los documentos, imágenes o archivos que desee respaldar</li>
                        <li>El sistema comprimirá automáticamente los archivos y los enviará a su carpeta privada en Dropbox</li>
                        <li>Todos los archivos se etiquetan con la fecha y información de la consulta para fácil identificación</li>
                        <li>Los archivos respaldados quedan organizados automáticamente en carpetas por fecha y paciente</li>
                    </ol>

                    <div class="info-box info-tip">
                        <i class="fas fa-cloud text-success"></i>
                        <div>
                            <strong>Ventajas del Respaldo en Dropbox</strong>
                            <ul class="mb-0">
                                <li>Acceso seguro desde cualquier dispositivo</li>
                                <li>Almacenamiento ilimitado en su cuenta personal</li>
                                <li>Cumple con estándares de seguridad y privacidad</li>
                                <li>Sincronización automática entre dispositivos</li>
                                <li>Historial de versiones y recuperación de archivos</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <div>
                            <strong>Requisito de Configuración</strong>
                            <p class="mb-0">Para utilizar esta función, debe <strong>configurar previamente sus credenciales de Dropbox</strong> en <a href="{{ route('help.settings') }}">Configuraciones → Integración Dropbox</a>. Si no ha configurado Dropbox, esta sección aparecerá deshabilitada.</p>
                        </div>
                    </div>


                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_db_uploaded_file.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>

                <!-- Step 12: General Notes (Updated number) -->
                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">13.</span> Añadir Notas Generales</h6>
                    <p>Complete el plan de manejo del paciente:</p>
                    <ul>
                        <li><strong>Indicaciones generales:</strong> Reposo, dieta, actividad física</li>
                        <li><strong>Recomendaciones:</strong> Cuidados en casa, signos de alarma</li>
                        <li><strong>Seguimiento:</strong> Cuándo debe regresar a control</li>
                        <li><strong>Educación al paciente:</strong> Información sobre su condición</li>
                    </ul>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_ng.png') }}" alt="" style="width: 100%;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 5: Finish Consultation -->
        <section id="finalizar" class="step-card">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-check-circle me-2"></i>5. Finalizar la Consulta</h3>
            <div class="step-content">
                <p>Una vez que haya documentado toda la información necesaria, debe finalizar formalmente la consulta:</p>

                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">1.</span> Revisar la Información</h6>
                    <p>Antes de finalizar, revise que toda la información esté completa:</p>
                    <ul>
                        <li>Motivo de consulta documentado</li>
                        <li>Enfermedad Actual registrada</li>
                        <li>Signos vitales registrados</li>
                        <li>Examen físico completado</li>
                        <li>Diagnósticos agregados</li>
                        <li>Prescripciones y órdenes generadas</li>
                        <li>Plan de tratamiento definido</li>
                    </ul>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <strong>Validación de Campos Automática</strong>
                            <p class="mb-0">Si al llenar la consulta, usted deja algún campo obligatorio vacío, el sistema le mostrará qué campos les hace falta llenar en el menú inferior, que se encuentra ubicado en la parte inferior central de la pantalla.</p>
                        </div>
                    </div>
                </div>

                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">2.</span> Finalizar Consulta</h6>
                    <p>Para cerrar formalmente la consulta:</p>
                    <ul>
                        <li>Haga clic en el botón <strong>"Finalizar Consulta"</strong> que se encuentra en la parte inferior del menú inferior.</li>
                        <li>El sistema actualizará el estado de la cita a <strong>"Finalizado"</strong></li>
                        <li>La información quedará guardada en la historia clínica del paciente</li>
                    </ul>
                </div>

                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">3.</span> Generar Documentos</h6>
                    <p>El sistema puede generar automáticamente varios documentos:</p>
                    <ul>
                        <li><strong>Resumen de Consulta:</strong> Documento completo con toda la información</li>
                        <li><strong>Receta Médica:</strong> Prescripción para el paciente</li>
                        <li><strong>Órdenes Médicas:</strong> Solicitudes de exámenes y procedimientos</li>
                        <li><strong>Incapacidad Médica:</strong> Si aplica</li>
                    </ul>

                    <div class="info-box info-tip">
                        <i class="fas fa-file-pdf text-success"></i>
                        <div>
                            <strong>Documentos en PDF</strong>
                            <p class="mb-0">Todos los documentos se generan en formato PDF con su firma y sello digital (si están configurados). Puede descargarlos, imprimirlos o enviarlos por correo electrónico al paciente.</p>
                        </div>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/encounters/encounter_finished.png') }}" alt="" style="width: 100%;">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle text-primary"></i>
                        <div>
                            <strong>Descarga de Documentos</strong>
                            <p class="mb-0">Puede descargar los documentos generados en la consulta en la sección de Consultas → Lista Consultas → clic en el botón de <strong>"Detalle"</strong>. <br>
                                Allí podrá visualizar la historia clínica digital separadas por secciones, así como los botones para descargar los PDF del resumen de consulta, recetas, órdenes, etc.</p>
                        </div>
                    </div>
                </div>

                <div class="sub-step">
                    <h6><span style="color: #d32f2f; font-weight: 700;">4.</span> Después de Finalizar</h6>
                    <p>Después de finalizar la consulta, puede:</p>
                    <ul>
                        <li>Enviar la receta al paciente por correo</li>
                        <li>Agendar una cita de seguimiento si es necesario</li>
                        <li>Regresar al calendario para atender al siguiente paciente</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Section 6: Tips and Best Practices -->
        <section id="tips" class="step-card">
            <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-lightbulb me-2"></i>6. Tips y Mejores Prácticas</h3>
            <div class="step-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box info-tip">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>Buenas Prácticas</strong>
                                <ul class="mb-0">
                                    <li><strong>Sea específico:</strong> Documente de manera clara y detallada</li>
                                    <li><strong>Use plantillas:</strong> Configure plantillas para agilizar el proceso</li>
                                    <li><strong>Revise la historia:</strong> Consulte encuentros previos antes de iniciar</li>
                                    <li><strong>Verifique medicamentos:</strong> Confirme dosis y contraindicaciones</li>
                                    <li><strong>Documente todo:</strong> Incluya hallazgos negativos relevantes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box info-warning">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <div>
                                <strong>Errores Comunes a Evitar</strong>
                                <ul class="mb-0">
                                    <li>No documentar signos vitales</li>
                                    <li>Omitir el examen físico</li>
                                    <li>Finalizar sin revisar la información</li>
                                    <li>No generar los documentos para el paciente</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 style="margin-top: 25px;">Preguntas Frecuentes</h4>

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                ¿Qué pasa si cierro la ventana sin finalizar la consulta?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                No se preocupe, toda la información se guarda automáticamente. Puede volver a abrir la consulta más tarde y continuará donde la dejó. Sin embargo, la cita permanecerá en estado "En Progreso" hasta que la finalice formalmente.
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

                <div class="info-box info-tip mt-4">
                    <i class="fas fa-question-circle text-success"></i>
                    <div>
                        <strong>¿Necesita Más Ayuda?</strong>
                        <p class="mb-0">Si tiene preguntas adicionales o necesita asistencia, contacte al equipo de soporte de SAMI o consulte con el administrador de su clínica.</p>
                    </div>
                </div>
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
            <a href="{{ route('help.index') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
@stop
