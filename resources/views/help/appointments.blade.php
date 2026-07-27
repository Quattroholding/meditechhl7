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
    background: linear-gradient(180deg, #e65100 0%, #ff6f00 100%);
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
    background: linear-gradient(135deg, #e65100 0%, #bf360c 100%);
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
    border-left: 5px solid var(--calendar-color);
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
    background: linear-gradient(135deg, #e65100 0%, #bf360c 100%);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.3rem;
    box-shadow: 0 4px 10px rgba(230, 81, 0, 0.4);
}

.step-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #bf360c;
    margin-bottom: 15px;
    padding-left: 45px;
}

.step-content {
    color: #555;
    line-height: 1.8;
}

/* Screenshot Placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border: 3px dashed #ff9800;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 25px 0;
    position: relative;
}

.screenshot-placeholder i {
    font-size: 3rem;
    color: #f57c00;
    margin-bottom: 15px;
}

.screenshot-placeholder h5 {
    color: #e65100;
    font-weight: 600;
    margin-bottom: 10px;
}

.screenshot-placeholder p {
    color: #ff9800;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.screenshot-placeholder .dimensions {
    position: absolute;
    bottom: 10px;
    right: 15px;
    font-size: 0.75rem;
    color: #ffb74d;
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
    background: #bf360c;
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
    color: #bf360c;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #fff3e0;
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
    color: #e65100;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    background: #fff3e0;
    color: #bf360c;
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
    border-left: 3px solid #e65100;
}

.sub-step h6 {
    color: #bf360c;
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
    color: #e65100;
    margin-top: 3px;
}

/* Status Badge Cards */
.status-badge-card {
    background: #fff;
    border: 2px solid #ffe0b2;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    height: 100%;
}

.status-badge-card .badge-display {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 20px;
    margin-bottom: 10px;
    display: inline-block;
}

.status-badge-card .status-name {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
    font-weight: 600;
}

.status-badge-card .status-desc {
    font-family: monospace;
    background: #fff3e0;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.75rem;
    color: #e65100;
}

/* Calendar View Cards */
.calendar-view-card {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
}

.calendar-view-card h6 {
    color: #bf360c;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.calendar-view-card ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.calendar-view-card li {
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
    background: linear-gradient(135deg, #e65100 0%, #bf360c 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(230, 81, 0, 0.4);
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
.appointment-flow-timeline {
    position: relative;
    padding: 20px 0;
}

.appointment-flow-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #e65100 0%, #ff8f00 100%);
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
    border: 3px solid #e65100;
    border-radius: 50%;
}

.timeline-item.completed::before {
    background: #e65100;
}

.timeline-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.timeline-content h6 {
    color: #bf360c;
    font-weight: 600;
    margin-bottom: 8px;
}

.timeline-content p {
    color: #666;
    margin: 0;
    font-size: 0.9rem;
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
        text-align: left;
        padding: 10px 12px;
        font-size: 14px;
        border-bottom: 1px solid #f0f0f0;
    }

    .field-table td:last-child {
        border-bottom: none;
    }

    .field-table td[data-label="Campo"] {
        background: #bf360c;
        color: white;
        font-size: 15px;
        padding: 12px;
        border-bottom: 2px solid #d84315;
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

    .field-table td[data-label="Requerido"] {
        background: #f5f5f5;
        padding: 12px 15px;
        text-align: center;
        font-weight: 600;
    }

    .field-table td[data-label="Requerido"]:before {
        content: "Requerido: ";
        font-weight: 600;
        color: #bf360c;
        margin-right: 5px;
    }

    .step-card, .sub-step {
        border-left: none;
        padding: 25px;
        border-bottom: 5px solid var(--calendar-color);
    }

    .step-title {
        padding-left: 0;
        text-align: center;
        padding-top: 8%;
    }

    .info-box, .calendar-view-card h6 {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

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

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'appointments'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="#">Gestión de Citas</a></li>
            <li class="breadcrumb-item active">Agendar Citas</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-calendar-plus me-3"></i>Guía para Agendar Citas</h1>
        <p>Aprende a gestionar el calendario de citas de tu clínica. Programa, confirma, cancela y administra las citas de tus pacientes de forma eficiente.</p>
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
                            Sobre las Citas
                        </a>
                    </li>
                    <li>
                        <a href="#estados">
                            <i class="fas fa-tag"></i>
                            Estados de las Citas
                        </a>
                    </li>
                    <li>
                        <a href="#vistas">
                            <i class="fas fa-eye"></i>
                            Vistas del Calendario
                        </a>
                    </li>
                    <li>
                        <a href="#paso-1">
                            <i class="fas fa-mouse-pointer"></i>
                            Paso 1: Acceder al Calendario
                        </a>
                    </li>
                    <li>
                        <a href="#paso-2">
                            <i class="fas fa-plus-circle"></i>
                            Paso 2: Nueva Cita
                        </a>
                    </li>
                    <li>
                        <a href="#paso-3">
                            <i class="fas fa-user-injured"></i>
                            Paso 3: Seleccionar Paciente
                        </a>
                    </li>
                    <li>
                        <a href="#paso-4">
                            <i class="fas fa-user-md"></i>
                            Paso 4: Elegir Médico
                        </a>
                    </li>
                    <li>
                        <a href="#paso-5">
                            <i class="fas fa-clock"></i>
                            Paso 5: Fecha y Hora
                        </a>
                    </li>
                    <li>
                        <a href="#paso-6">
                            <i class="fas fa-notes-medical"></i>
                            Paso 6: Información Adicional
                        </a>
                    </li>
                    <li>
                        <a href="#paso-7">
                            <i class="fas fa-save"></i>
                            Paso 7: Guardar Cita
                        </a>
                    </li>
                    <li>
                        <a href="#gestionar">
                            <i class="fas fa-cogs"></i>
                            Gestionar Citas Existentes
                        </a>
                    </li>
                    <li>
                        <a href="#notificaciones">
                            <i class="fas fa-bell"></i>
                            Notificaciones
                        </a>
                    </li>
                    <li>
                        <a href="#waitlist">
                            <i class="fas fa-hourglass-half"></i>
                            Lista de Espera
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
            <!-- Sobre las Citas -->
            <section id="concepto" class="step-card step-info">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Sobre las Citas en SAMI</h3>
                <div class="step-content">
                    <p>El módulo de <strong>citas</strong> es el corazón de la gestión clínica en SAMI. Permite organizar el flujo de pacientes, controlar la disponibilidad de médicos y gestionar los horarios de tu clínica.</p>

                    <ul class="checklist">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Calendario visual</strong><br>
                                <small class="text-muted">Interfaz intuitiva con vista de mes, semana y día</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Gestión de estados</strong><br>
                                <small class="text-muted">Sigue el ciclo completo desde la solicitud hasta la completación</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Notificaciones automáticas</strong><br>
                                <small class="text-muted">Recordatorios por email a pacientes y médicos</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Historial completo</strong><br>
                                <small class="text-muted">Registro de todos los cambios de estado y notas</small>
                            </div>
                        </li>
                    </ul>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Integración con consultas:</strong> Una vez completada la cita, puedes iniciar directamente la consulta médica desde el mismo calendario, vinculando el encuentro clínico con la cita agendada.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Estados de las Citas -->
            <section id="estados" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-tag me-2"></i>Estados de las Citas</h3>
                <div class="step-content">
                    <p>Las citas pasan por diferentes estados durante su ciclo de vida:</p>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-warning text-dark">Pendiente</div>
                                <div class="status-name">Pendiente de Confirmación</div>
                                <div class="status-desc">Estado inicial, requiere confirmación</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-info text-white">Confirmada</div>
                                <div class="status-name">Confirmada</div>
                                <div class="status-desc">Paciente y médico confirmados</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-primary text-white">En Curso</div>
                                <div class="status-name">En Curso</div>
                                <div class="status-desc">Consulta en progreso</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-success text-white">Completada</div>
                                <div class="status-name">Completada</div>
                                <div class="status-desc">Consulta finalizada</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-danger text-white">Cancelada</div>
                                <div class="status-name">Cancelada</div>
                                <div class="status-desc">Cita cancelada por paciente o médico</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="status-badge-card">
                                <div class="badge-display bg-secondary text-white">No Show</div>
                                <div class="status-name">No Asistió</div>
                                <div class="status-desc">Paciente no se presentó</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Flujo típico:</strong> Pendiente → Confirmada → En Curso → Completada. Los estados cancelada y no show son alternativos cuando la cita no se realiza.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vistas del Calendario -->
            <section id="vistas" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-eye me-2"></i>Vistas del Calendario</h3>
                <div class="step-content">
                    <p>El calendario ofrece tres vistas principales para adaptarse a tus necesidades:</p>

                    <div class="calendar-view-card">
                        <h6><i class="fas fa-calendar-day me-2"></i>Vista de Día</h6>
                        <ul>
                            <li><strong>Uso:</strong> Gestionar el día actual con detalle</li>
                            <li><strong>Muestra:</strong> Horario completo del día con bloques de 30 minutos</li>
                            <li><strong>Ideal para:</strong> Seguimiento de consultas en tiempo real</li>
                        </ul>
                    </div>

                    <div class="calendar-view-card">
                        <h6><i class="fas fa-calendar-week me-2"></i>Vista de Semana</h6>
                        <ul>
                            <li><strong>Uso:</strong> Planificar y organizar la semana laboral</li>
                            <li><strong>Muestra:</strong> 7 días completos con todas las citas</li>
                            <li><strong>Ideal para:</strong> Distribución de carga de trabajo</li>
                        </ul>
                    </div>

                    <div class="calendar-view-card">
                        <h6><i class="fas fa-calendar-alt me-2"></i>Vista de Mes</h6>
                        <ul>
                            <li><strong>Uso:</strong> Vista panorámica mensual</li>
                            <li><strong>Muestra:</strong> Resumen de citas del mes completo</li>
                            <li><strong>Ideal para:</strong> Planificación a largo plazo</li>
                        </ul>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/calendar-views.png') }}" alt="Vistas del calendario" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </section>

            <!-- Paso 1: Acceder al Calendario -->
            <section id="paso-1" class="step-card">
                <div class="step-number">1</div>
                <h3 class="step-title">Acceder al Módulo de Citas</h3>
                <div class="step-content">
                    <p>Para comenzar a gestionar citas:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Navegación</h6>
                        <ol>
                            <li>Inicia sesión en tu cuenta SAMI</li>
                            <li>En el menú lateral, busca <strong>"Citas"</strong></li>
                            <li>Haz clic en <strong>Calendario</strong> para ver todas las citas</li>
                        </ol>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/access-calendar.png') }}" alt="Acceso al calendario" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Dashboard directo:</strong> En tu dashboard principal también verás un resumen de las citas del día actual con acceso rápido al calendario.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 2: Nueva Cita -->
            <section id="paso-2" class="step-card step-important">
                <div class="step-number">2</div>
                <h3 class="step-title">Crear Nueva Cita</h3>
                <div class="step-content">
                    <p>Hay dos formas de crear una nueva cita:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-mouse-pointer me-2"></i>Opción 1: Botón Nueva Cita</h6>
                        <p>En la parte superior del calendario, haz clic en el botón <strong class="text-primary">+ Nueva Cita</strong></p>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-hand-pointer me-2"></i>Opción 2: Clic Directo en Calendario</h6>
                        <p>Haz clic directamente en el horario deseado dentro del calendario. El sistema abrirá automáticamente el formulario con la fecha y hora preseleccionadas.</p>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/new-appointment-button.png') }}" alt="Crear nueva cita" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Atajo rápido:</strong> La opción de clic directo es más rápida si ya sabes el horario exacto que necesitas.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 3: Seleccionar Paciente -->
            <section id="paso-3" class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Seleccionar el Paciente</h3>
                <div class="step-content">
                    <p>El primer campo del formulario es la selección del paciente:</p>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/select-patient.png') }}" alt="Seleccionar paciente" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <table class="field-table">
                        <thead>
                        <tr>
                            <th>Acción</th>
                            <th>Descripción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td data-label="Acción"><strong>Buscar por nombre</strong></td>
                            <td data-label="Descripción">Escribe el nombre del paciente. El sistema mostrará coincidencias en tiempo real</td>
                        </tr>
                        <tr>
                            <td data-label="Acción"><strong>Buscar por documento</strong></td>
                            <td data-label="Descripción">Puedes buscar también por número de cédula o identificación</td>
                        </tr>
                        <tr>
                            <td data-label="Acción"><strong>Paciente nuevo</strong></td>
                            <td data-label="Descripción">Si el paciente no existe, hay un enlace rápido para registrarlo</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Paciente requerido:</strong> No puedes crear una cita sin seleccionar primero un paciente registrado en el sistema.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 4: Elegir Médico -->
            <section id="paso-4" class="step-card">
                <div class="step-number">4</div>
                <h3 class="step-title">Elegir el Médico/Profesional</h3>
                <div class="step-content">
                    <p>Selecciona el médico o profesional de salud que atenderá la cita:</p>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/select-doctor.png') }}" alt="Seleccionar médico" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-filter me-2"></i>Filtros disponibles</h6>
                        <ul>
                            <li><strong>Por especialidad:</strong> Filtra médicos según su especialidad</li>
                            <li><strong>Por consultorio:</strong> Muestra solo médicos de un consultorio específico</li>
                            <li><strong>Por disponibilidad:</strong> El sistema puede destacar médicos con horarios disponibles</li>
                        </ul>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Disponibilidad en tiempo real:</strong> El sistema mostrará la disponibilidad del médico en la fecha seleccionada para evitar solapamientos.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 5: Fecha y Hora -->
            <section id="paso-5" class="step-card step-important">
                <div class="step-number">5</div>
                <h3 class="step-title">Establecer Fecha y Hora</h3>
                <div class="step-content">
                    <p>Define cuándo se realizará la cita:</p>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/date-time.png') }}" alt="Fecha y hora" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
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
                            <td data-label="Campo"><strong>Fecha</strong></td>
                            <td data-label="Descripción">Selecciona el día de la cita usando el calendario</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Hora de Inicio</strong></td>
                            <td data-label="Descripción">Hora exacta en que comienza la cita (formato 24h o 12h)</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Duración</strong></td>
                            <td data-label="Descripción">Tiempo estimado de la consulta (15, 30, 45 o 60 minutos)</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="info-box info-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Validación de conflictos:</strong> El sistema alertará si ya existe otra cita para el mismo médico en el horario seleccionado.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 6: Información Adicional -->
            <section id="paso-6" class="step-card">
                <div class="step-number">6</div>
                <h3 class="step-title">Completar Información Adicional</h3>
                <div class="step-content">
                    <p>Agrega detalles complementarios de la cita:</p>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/additional-info.png') }}" alt="Información adicional" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
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
                            <td data-label="Campo"><strong>Consultorio</strong></td>
                            <td data-label="Descripción">Sala o consultorio donde se realizará la cita</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Motivo de Consulta</strong></td>
                            <td data-label="Descripción">Razón de la visita (ej: Control, Primera vez, Urgencia)</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Notas</strong></td>
                            <td data-label="Descripción">Información adicional sobre la cita (alergias, preparación necesaria, etc.)</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Enviar Recordatorio</strong></td>
                            <td data-label="Descripción">Checkbox para activar el envío de email de recordatorio al paciente</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Notas útiles:</strong> Usa el campo de notas para información que el médico deba saber antes de la consulta, como "Paciente requiere intérprete" o "Traer resultados de laboratorio".
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 7: Guardar Cita -->
            <section id="paso-7" class="step-card step-success">
                <div class="step-number">7</div>
                <h3 class="step-title">Guardar la Cita</h3>
                <div class="step-content">
                    <p>Una vez completada toda la información:</p>

                    <ol>
                        <li>Revisa que todos los datos obligatorios estén completos</li>
                        <li>Verifica que la fecha, hora y médico sean correctos</li>
                        <li>Haz clic en el botón <strong>"Guardar Cita"</strong> o <strong>"Agendar"</strong></li>
                        <li>Espera la confirmación del sistema</li>
                    </ol>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/save-appointment.png') }}" alt="Guardar cita" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Éxito!</strong> La cita ha sido agendada. Ahora puedes:
                            <ul class="mb-0 mt-2">
                                <li>Verla en el calendario en el horario seleccionado</li>
                                <li>Editarla si necesitas hacer cambios</li>
                                <li>Enviar notificaciones al paciente</li>
                                <li>Cambiar su estado según avance el día</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Notificación automática:</strong> Si activaste "Enviar recordatorio", el paciente recibirá un email con los detalles de su cita próximamente.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Gestionar Citas Existentes -->
            <section id="gestionar" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Gestionar Citas Existentes</h3>
                <div class="step-content">
                    <p>Una vez creadas las citas, puedes gestionarlas desde el calendario:</p>

                    <div class="appointment-flow-timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6>Ver Detalles</h6>
                                <p>Haz clic en cualquier cita del calendario para ver su información completa en un modal o panel lateral.</p>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/view-details.png') }}" alt="Ver detalles" style="width: 100%;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6>Editar Cita</h6>
                                <p>Desde el detalle, puedes hacer clic en <strong>"Editar"</strong> para modificar la fecha, hora, médico o cualquier información de la cita.</p>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/edit-appointment.png') }}" alt="Editar cita" style="width: 100%;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6>Cambiar Estado</h6>
                                <p>Usa los botones de acción rápida para cambiar el estado: Confirmar, Cancelar, Marcar como No Show, o Completar.</p>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/change-status.png') }}" alt="Cambiar estado" style="width: 100%;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6>Iniciar Consulta</h6>
                                <p>Cuando el paciente llegue, puedes hacer clic en <strong>"Iniciar Consulta"</strong> para pasar directamente al módulo de consulta médica, donde se registrará la historia clínica, diagnósticos y tratamiento.</p>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/start-consultation.png') }}" alt="Iniciar consulta" style="width: 100%;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6>Eliminar Cita</h6>
                                <p>Si necesitas eliminar una cita (por error o duplicado), usa el botón de eliminar. El sistema pedirá confirmación antes de borrarla permanentemente.</p>
                                <div class="info-box info-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Importante:</strong> Eliminar una cita es permanente. Si solo necesitas cancelarla temporalmente, es mejor cambiar su estado a "Cancelada".
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notificaciones -->
            <section id="notificaciones" class="step-card step-info">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-bell me-2"></i>Sistema de Notificaciones</h3>
                <div class="step-content">
                    <p>SAMI envía notificaciones automáticas para mantener informados a pacientes y médicos:</p>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #e65100 !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #e65100;"><i class="fas fa-calendar-plus me-2"></i>Confirmación de Cita</h6>
                                    <p class="small text-muted mb-2">Se envía al crear una nueva cita.</p>
                                    <ul class="small mb-0">
                                        <li><strong>Destinatario:</strong> Paciente y médico</li>
                                        <li><strong>Contenido:</strong> Fecha, hora, lugar y médico asignado</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #0288d1 !important;">
                                <div class="card-body">
                                    <h6 class="text-primary fw-bold"><i class="fas fa-clock me-2"></i>Recordatorio</h6>
                                    <p class="small text-muted mb-2">Se envía 24 horas antes de la cita.</p>
                                    <ul class="small mb-0">
                                        <li><strong>Destinatario:</strong> Paciente</li>
                                        <li><strong>Contenido:</strong> Recordatorio de la cita con instrucciones</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #f57c00 !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #f57c00;"><i class="fas fa-edit me-2"></i>Cambios en la Cita</h6>
                                    <p class="small text-muted mb-2">Se envía si se modifica la cita.</p>
                                    <ul class="small mb-0">
                                        <li><strong>Destinatario:</strong> Paciente y médico</li>
                                        <li><strong>Contenido:</strong> Nueva fecha/hora o cambios realizados</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #d32f2f !important;">
                                <div class="card-body">
                                    <h6 class="text-danger fw-bold"><i class="fas fa-times-circle me-2"></i>Cancelación</h6>
                                    <p class="small text-muted mb-2">Se envía si se cancela la cita.</p>
                                    <ul class="small mb-0">
                                        <li><strong>Destinatario:</strong> Paciente y médico</li>
                                        <li><strong>Contenido:</strong> Notificación de cancelación y motivo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-cog"></i>
                        <div>
                            <strong>Configuración:</strong> Puedes personalizar las plantillas de email y los tiempos de envío de recordatorios desde la configuración de tu clínica.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lista de Espera (Waitlist) -->
            <section id="waitlist" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-hourglass-half me-2"></i>Sistema de Lista de Espera</h3>
                <div class="step-content">
                    <p>Cuando un médico no tiene disponibilidad en el horario solicitado, el paciente puede ser agregado a una <strong>lista de espera</strong>. El sistema notificará automáticamente al paciente cuando se libere un espacio.</p>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>¿Cuándo usar la lista de espera?</strong> Úsala cuando los pacientes soliciten citas en horarios ocupados. Esto mejora la experiencia del paciente porque no se pierden sus solicitudes y pueden ser asignados automáticamente cuando se libera un espacio.
                        </div>
                    </div>

                    <div class="appointment-flow-timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6><i class="fas fa-plus-circle me-2"></i>Paso 1: Intentar Agendar</h6>
                                <p>El usuario intenta agendar una cita en un horario ocupado del médico. El sistema detecta que no hay disponibilidad.</p>

                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6><i class="fas fa-hourglass-start me-2"></i>Paso 2: Agregar a Lista de Espera</h6>
                                <p>Se abre un modal con opciones para agregar el paciente a la lista de espera. Aquí se capturan las preferencias del paciente:</p>
                                <ul style="margin-top: 10px;">
                                    <li><strong>Nivel de Urgencia:</strong> Rutinaria, Urgente, Muy Urgente, Emergencia</li>
                                    <li><strong>Máximo de días esperando:</strong> Cuántos días está dispuesto a esperar el paciente</li>
                                    <li><strong>Fecha preferida:</strong> Si desea una fecha específica (opcional)</li>
                                    <li><strong>Hora preferida:</strong> Si desea una hora específica (opcional)</li>
                                    <li><strong>Flexibilidad de fecha/hora:</strong> Si está flexible con ambos parámetros</li>
                                    <li><strong>Notas:</strong> Observaciones adicionales sobre la solicitud</li>
                                </ul>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/waitlist-modal.png') }}" alt="Modal de lista de espera" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 15px;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6><i class="fas fa-bell me-2"></i>Paso 3: Confirmación y Notificación</h6>
                                <p>Una vez agregado a la lista de espera, el paciente recibe una notificación informando que está en la lista. El sistema calcula automáticamente su <strong>score de prioridad</strong> basado en:</p>
                                <ul style="margin-top: 10px;">
                                    <li><strong>Urgencia:</strong> Emergencias y urgencias tienen mayor prioridad</li>
                                    <li><strong>Tiempo esperando:</strong> Pacientes que esperan más tiempo suben en la lista</li>
                                    <li><strong>Flexibilidad:</strong> Pacientes flexibles con horarios tienen mayor probabilidad de asignación</li>
                                    <li><strong>Historial:</strong> Pacientes recurrentes (con citas previas) ganan puntos</li>
                                </ul>
                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/waitlist-notification.png') }}" alt="Notificación de lista de espera" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 15px;">
                                </div>
                            </div>
                        </div>


                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6><i class="fas fa-hand-holding me-2"></i>Paso 4: Asignación Manual de Espacio</h6>
                                <p><strong>Proceso Principal (Manual):</strong> Cuando se libera un espacio, el sistema muestra los candidatos sugeridos <strong>POR SCORE</strong> de coincidencia, pero TÚ decides quién asignar.</p>

                                <div style="background: #e3f2fd; border-left: 4px solid #1976d2; padding: 15px; margin: 15px 0; border-radius: 8px;">
                                    <h6 style="margin-top: 0; color: #1976d2;">
                                        <i class="fas fa-info-circle me-2"></i>Flujo de Asignación Manual
                                    </h6>
                                    <ol style="margin: 10px 0;">
                                        <li><strong>Se libera un espacio</strong> → Paciente cancela o no asiste</li>
                                        <li><strong>Panel muestra candidatos</strong> → El sistema sugiere los 5-10 mejores por score</li>
                                        <li><strong>Tú seleccionas quién</strong> → Puedes elegir cualquiera, no solo el top 1</li>
                                        <li><strong>Confirmas la asignación</strong> → El paciente es notificado automáticamente</li>
                                    </ol>
                                </div>

                                <p><strong>Cómo ver y asignar:</strong></p>
                                <ul style="margin-top: 10px;">
                                    <li>📍 Abre el calendario en el horario donde se liberó el espacio</li>
                                    <li>🎯 Se mostrará un botón <strong>"Asignar Espacio"</strong> o una notificación visual</li>
                                    <li>👥 Se despliega el modal con candidatos ordenados por score de coincidencia</li>
                                    <li>✅ Selecciona el paciente que desees (basándote en score sugerido o tu criterio)</li>
                                    <li>📤 Confirma la asignación</li>
                                </ul>

                                <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin: 15px 0; border-radius: 8px;">
                                    <h6 style="margin-top: 0; color: #e65100;">
                                        <i class="fas fa-cog me-2"></i>Configuración por Cliente (Opcional)
                                    </h6>
                                    <p style="margin: 0;">Tu clínica puede configurar si desea <strong>asignación automática</strong> para ciertos casos. Por defecto, <strong>la asignación es manual</strong> para que tengas control total.</p>
                                </div>

                                <div>
                                    <img src="{{ asset('images/tutorial/appointments/manual-assignment-modal.png') }}" alt="Modal de asignación manual" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 15px;">
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6><i class="fas fa-sync-alt me-2"></i>Paso 5: Expiración Automática</h6>
                                <p>El sistema automáticamente:</p>
                                <ul style="margin-top: 10px;">
                                    <li>Expira entradas cuando se alcanza el máximo de días configurado</li>
                                    <li>Notifica al paciente cuando su entrada expira</li>
                                    <li>Ejecuta recálculos de prioridad diarios para mantener la lista actualizada</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-warning" style="margin-top: 30px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Acceso a Lista de Espera:</strong> Solo recepcionistas y doctores pueden acceder al panel de gestión. Los pacientes verán el estado de su solicitud en su perfil y recibirán notificaciones automáticas.
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #ffa10b !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #ffa10b;"><i class="fas fa-users me-2"></i>Visión de Recepcionista</h6>
                                    <p class="small text-muted mb-2">Gestión centralizada de toda la lista de espera.</p>
                                    <ul class="small mb-0">
                                        <li>✅ Ver lista priorizada completa</li>
                                        <li>✅ Asignar pacientes a espacios</li>
                                        <li>✅ Cancelar entradas</li>
                                        <li>✅ Filtrar por criterios</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #ff6f00 !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #ff6f00;"><i class="fas fa-user-injured me-2"></i>Visión de Paciente</h6>
                                    <p class="small text-muted mb-2">Seguimiento transparente de su solicitud.</p>
                                    <ul class="small mb-0">
                                        <li>✅ Recibir confirmación de entrada</li>
                                        <li>✅ Notificaciones de espacios</li>
                                        <li>✅ Notificación de expiración</li>
                                        <li>✅ Aceptar/rechazar ofertas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-tip" style="margin-top: 20px;">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Configuración de lista de espera:</strong> Desde tu configuración clínica, puedes ajustar tiempos de expiración, patrones de priorización y plantillas de notificación para adaptarlos a tu flujo específico.
                        </div>
                    </div>

                    <!-- Sección: Cómo Ver la Lista de Espera -->
                    <hr style="margin: 30px 0;">
                    <h5 style="color: #bf360c; margin-bottom: 15px;"><i class="fas fa-eye me-2"></i>Acceder a la Lista de Espera</h5>

                    <p>Para ver todos los pacientes en lista de espera (no solo cuando se libera un espacio):</p>

                    <div class="step-card" style="border-left: none; margin: 20px 0; padding: 20px; background: #f8f9fa; box-shadow: none;">
                        <ol style="margin-left: 20px;">
                            <li><strong>Abre el menú principal</strong> → Haz clic en <strong>"Citas"</strong></li>
                            <li><strong>Selecciona "Lista de Espera"</strong> en el submenú</li>
                            <li><strong>Verás el panel completo</strong> con:
                                <ul style="margin-top: 10px;">
                                    <li>📊 <strong>Estadísticas</strong> - Total, urgentes, expirando pronto</li>
                                    <li>🔍 <strong>Filtros</strong> - Por doctor, especialidad, urgencia, búsqueda</li>
                                    <li>📋 <strong>Tabla de pacientes</strong> - Ordenados por score de prioridad</li>
                                    <li>⚡ <strong>Acciones rápidas</strong> - Asignar o cancelar para cada paciente</li>
                                </ul>
                            </li>
                        </ol>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/waitlist-manager-view.png') }}" alt="Vista del panel de lista de espera" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin: 20px 0;">
                    </div>

                    <!-- Sección: Columnas de la Tabla -->
                    <h5 style="color: #bf360c; margin: 30px 0 15px 0;"><i class="fas fa-table me-2"></i>Entendiendo la Tabla de Lista de Espera</h5>

                    <table class="field-table" style="margin: 20px 0;">
                        <thead>
                            <tr>
                                <th>Columna</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Columna"><strong>Score Prioridad</strong></td>
                                <td data-label="Descripción">Número 0-100 que indica la posición en la fila. Score más alto = más prioridad</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Paciente</strong></td>
                                <td data-label="Descripción">Nombre completo del paciente + identificador</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Especialidad</strong></td>
                                <td data-label="Descripción">Especialidad médica solicitada</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Urgencia</strong></td>
                                <td data-label="Descripción">Nivel de urgencia (Rutinaria, Urgente, Muy Urgente, Emergencia)</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Fecha Preferida</strong></td>
                                <td data-label="Descripción">Fecha que el paciente solicitó (si es específica)</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Días Esperando</strong></td>
                                <td data-label="Descripción">Cuántos días lleva esperando en la lista</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Expira En</strong></td>
                                <td data-label="Descripción">Días restantes antes de que su solicitud expire</td>
                            </tr>
                            <tr>
                                <td data-label="Columna"><strong>Acciones</strong></td>
                                <td data-label="Descripción">Botones para Asignar (si hay espacio) o Cancelar</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Sección: Score de Prioridad Explicado -->
                    <div class="info-box info-note" style="margin: 30px 0;">
                        <i class="fas fa-star"></i>
                        <div>
                            <strong>¿Cómo se calcula el Score?</strong><br>
                            El sistema calcula automáticamente puntos basándose en:
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li><strong>Urgencia</strong> (0-40 pts) - Emergencias = 40 pts, Rutinarias = 0 pts</li>
                                <li><strong>Tiempo esperando</strong> (0-30 pts) - Cada día suma puntos (máx 30)</li>
                                <li><strong>Flexibilidad</strong> (0-20 pts) - Si es flexible con fecha/hora = más puntos</li>
                                <li><strong>Historial</strong> (0-10 pts) - Si tuvo citas previas completas = más puntos</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sección: Cómo Asignar desde el Panel -->
                    <h5 style="color: #bf360c; margin: 30px 0 15px 0;"><i class="fas fa-plus-circle me-2"></i>Asignar Paciente Manualmente</h5>

                    <p>Cuando haces clic en el botón <strong>"Asignar"</strong> en cualquier fila:</p>

                    <div class="step-card" style="border-left: none; margin: 20px 0; padding: 20px; background: #f8f9fa; box-shadow: none;">
                        <ol style="margin-left: 20px;">
                            <li><strong>Se abre un modal</strong> donde puedes elegir:
                                <ul style="margin-top: 10px;">
                                    <li>📅 <strong>Fecha</strong> - El día disponible</li>
                                    <li>🕐 <strong>Hora</strong> - La hora exacta</li>
                                    <li>⏱️ <strong>Duración</strong> - Cuánto durará la consulta</li>
                                    <li>🏥 <strong>Consultorio</strong> - Dónde se hará la consulta</li>
                                </ul>
                            </li>
                            <li><strong>Confirma la asignación</strong> → El paciente recibe una notificación automáticamente</li>
                            <li><strong>El calendario se actualiza</strong> → La cita aparece como "Agendada"</li>
                        </ol>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/appointments/assign-from-waitlist.png') }}" alt="Modal de asignación" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin: 20px 0;">
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Importante:</strong> Cuando asignas desde la lista de espera, el sistema valida que NO haya conflicto con otras citas del mismo médico en ese horario.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Siguiente Paso -->
            <section id="siguiente" class="step-card step-success">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguiente Paso: Consulta Médica</h3>
                <div class="step-content">
                    <p>Ahora que sabes agendar y gestionar citas, el siguiente paso es aprender a realizar la <strong>consulta médica</strong> cuando el paciente llega.</p>

                    <div class="info-box info-note">
                        <i class="fas fa-stethoscope"></i>
                        <div>
                            <strong>La consulta médica incluye:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Registro de signos vitales</li>
                                <li>Motivo de consulta y enfermedad actual</li>
                                <li>Examen físico y hallazgos</li>
                                <li>Diagnósticos con código ICD-10</li>
                                <li>Prescripción de medicamentos</li>
                                <li>Órdenes de laboratorio e imágenes</li>
                                <li>Plan de tratamiento y seguimiento</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('help.consultation') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #e65100 0%, #bf360c 100%);">
                            <i class="fas fa-stethoscope me-2"></i>Continuar: Consulta Médica
                        </a>
                    </div>
                </div>
            </section>

            <!-- Contact Support -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #e65100;"></i>¿Tienes preguntas sobre el agendamiento de citas?</h5>
                            <p class="text-muted mb-0">Nuestro equipo de soporte está disponible para ayudarte.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="mailto:business@meditecpty.com" class="btn text-white" style="background: linear-gradient(135deg, #e65100 0%, #bf360c 100%);">
                                <i class="fas fa-envelope me-2"></i>Contactar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
