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
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
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
    border-left: 5px solid #667eea;
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
    color: #764ba2;
    margin-bottom: 15px;
    padding-left: 45px;
}

.step-content {
    color: #555;
    line-height: 1.8;
}

/* Screenshot Placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 3px dashed #2196f3;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 25px 0;
    position: relative;
}

.screenshot-placeholder i {
    font-size: 3rem;
    color: #1976d2;
    margin-bottom: 15px;
}

.screenshot-placeholder h5 {
    color: #1565c0;
    font-weight: 600;
    margin-bottom: 10px;
}

.screenshot-placeholder p {
    color: #1976d2;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.screenshot-placeholder .dimensions {
    position: absolute;
    bottom: 10px;
    right: 15px;
    font-size: 0.75rem;
    color: #64b5f6;
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
    background: #764ba2;
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
    color: #764ba2;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e3f2fd;
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
    color: #667eea;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    background: #e3f2fd;
    color: #764ba2;
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
    color: #764ba2;
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
    color: #667eea;
    margin-top: 3px;
}

/* Flow Diagram */
.flow-diagram {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    margin: 30px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e3f2fd 100%);
    border-radius: 15px;
}

.flow-step {
    flex: 1;
    min-width: 100px;
    text-align: center;
    padding: 15px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.flow-step i {
    font-size: 2rem;
    color: #667eea;
    margin-bottom: 10px;
}

.flow-step h6 {
    color: #764ba2;
    font-weight: 600;
    margin: 10px 0 5px;
    font-size: 0.9rem;
}

.flow-step p {
    color: #666;
    font-size: 0.8rem;
    margin: 0;
}

.flow-arrow {
    color: #667eea;
    font-size: 1.5rem;
}

/* Pricing Table */
.pricing-table-wrapper {
    overflow-x: auto;
    margin: 20px 0;
}

.pricing-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-radius: 10px;
    overflow: hidden;
}

.pricing-table thead th {
    background: #764ba2;
    color: #fff;
    padding: 15px;
    font-weight: 600;
    text-align: center;
}

.pricing-table tbody td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
}

.pricing-table tbody tr.recommended td {
    background: #e8f5e9;
    font-weight: 600;
}

.pricing-table .badge-recommended {
    background: #4caf50;
    color: #fff;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Code Block */
.code-block {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-left: 3px solid #667eea;
    padding: 10px 15px;
    border-radius: 5px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    color: #333;
    margin: 10px 0;
    display: inline-block;
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
        max-width: 100vw;
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

    .pricing-table thead {
        display: none;
    }

    .pricing-table tbody {
        display: block;
    }

    .pricing-table tr {
        display: block;
        margin-bottom: 20px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .pricing-table td {
        display: block;
        text-align: left;
        padding: 12px;
    }

    .pricing-table td:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #764ba2;
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
    @include('help.sidebar', ['active' => 'registration'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="#">Registro</a></li>
            <li class="breadcrumb-item active">Guía de Registro</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-user-plus me-3"></i>Guía de Registro en SAMI</h1>
        <p>Aprende a crear tu cuenta en SAMI paso a paso. Desde la selección del plan hasta el primer inicio de sesión.</p>
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
                            Paso 6: Código de Referido
                        </a>
                    </li>
                    <li>
                        <a href="#paso-6">
                            <i class="fas fa-check-square"></i>
                            Paso 7: Términos y Envío
                        </a>
                    </li>
                    <li>
                        <a href="#post-registro">
                            <i class="fas fa-flag-checkered"></i>
                            Después del Registro
                        </a>
                    </li>
                    <li>
                        <a href="#loggin">
                            <i class="fas fa-sign-in-alt"></i>
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
                            <i class="fas fa-mobile-alt"></i>
                            Flujo de pago con Yappy
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
                    <p>El registro en SAMI es un proceso sencillo que te permite crear tu cuenta y comenzar a gestionar tu práctica médica en pocos minutos.</p>

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
                    <p>Antes de comenzar el registro, asegúrate de tener a la mano:</p>

                    <ul class="checklist">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Documento de Identidad</strong><br>
                                <small class="text-muted">Cédula (CC), Pasaporte (PA), Carnet de Extranjería (CE) o Permiso Temporal (PT)</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Correo Electrónico Válido</strong><br>
                                <small class="text-muted">Se enviarán tus credenciales de acceso a este correo</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Número de Teléfono</strong><br>
                                <small class="text-muted">Preferiblemente con WhatsApp para notificaciones</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Especialidad Médica</strong><br>
                                <small class="text-muted">Conocer tu especialidad para seleccionarla en el formulario</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Logo (Opcional)</strong><br>
                                <small class="text-muted">Imagen en formato JPG, JPEG o PNG (máximo 2MB)</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Paso 0: Seleccionar Plan desde Landing -->
            <section id="paso-0" class="step-card step-important">
                <div class="step-number">1</div>
                <h3 class="step-title">Seleccionar Plan desde la Página Principal</h3>
                <div class="step-content">
                    <div class="sub-step">
                        <h6><i class="fas fa-home me-2"></i>Pasos para Iniciar</h6>
                        <ol>
                            <li>Visita la página principal: <div class="code-block">https://sami.meditecpty.com</div></li>
                            <li>Navega a la sección <strong>"Planes"</strong> (menú o scroll hacia abajo)</li>
                            <li>Revisa los planes disponibles y sus características</li>
                            <li>Haz clic en el botón <strong>"Suscribirse Ahora"</strong> del plan que desees</li>
                        </ol>
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/register/fplans.png') }}" alt="Planes en la página principal" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </section>

            <!-- Planes Disponibles -->
            <section id="planes-disponibles" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-box me-2"></i>Planes Disponibles</h3>
                <div class="step-content">
                    <p>SAMI ofrece tres planes para registro público (el plan Empresarial solo está disponible contactando ventas):</p>

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
                                    <td data-label="Plan"><strong>Básico</strong></td>
                                    <td data-label="Precio/mes">$49.99</td>
                                    <td data-label="Usuarios">1</td>
                                    <td data-label="Citas">30/mes</td>
                                    <td data-label="SAMI"><i class="fas fa-check text-success"></i> Activo</td>
                                    <td data-label="Directorio"><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr class="recommended">
                                    <td data-label="Plan">
                                        <strong>Estándar ⭐</strong><br>
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

                    <h5 class="mt-4">Diferencias Clave Entre Planes:</h5>

                    <div class="sub-step">
                        <h6><i class="fas fa-balance-scale me-2"></i>Básico vs Estándar</h6>
                        <p><strong>ÚNICA DIFERENCIA:</strong></p>
                        <ul>
                            <li><strong>Básico:</strong> Límite de 30 citas por mes</li>
                            <li><strong>Estándar:</strong> Citas ilimitadas</li>
                        </ul>
                        <p><strong>Todo lo demás es EXACTAMENTE IGUAL</strong> (gestión de pacientes, historia clínica, dashboard, etc.)</p>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-balance-scale me-2"></i>Estándar vs Premium</h6>
                        <p><strong>ÚNICA DIFERENCIA:</strong></p>
                        <ul>
                            <li><strong>Estándar:</strong> 1 usuario (médico solo)</li>
                            <li><strong>Premium:</strong> 4 usuarios (Admin + Doctor + Recepcionista + Asistente)</li>
                        </ul>
                        <p><strong>Todo lo demás es EXACTAMENTE IGUAL</strong> (citas ilimitadas, SAMI activo, todas las funcionalidades)</p>
                    </div>
                </div>
            </section>

            <!-- Paso 1: Completar Formulario -->
            <section id="paso-1" class="step-card">
                <div class="step-number">2</div>
                <h3 class="step-title">Completar Formulario de Registro</h3>
                <div class="step-content">
                    <p>Una vez hayas seleccionado tu plan desde el landing page, serás redirigido al formulario de registro con tu plan pre-seleccionado:</p>

                    <div>
                        <img src="{{ asset('images/tutorial/register/regform.png') }}" alt="Formulario de registro" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Plan pre-seleccionado:</strong> El formulario ya tendrá marcado el plan que elegiste en el landing page. Puedes cambiarlo si lo deseas antes de enviar.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 2: Datos Personales -->
            <section id="paso-2" class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Completar Datos Personales</h3>
                <div class="step-content">
                    <p>Completa los campos de información personal:</p>

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
                                <td data-label="Descripción">Tu nombre completo como aparece en tu identificación</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Tipo de Documento</strong></td>
                                <td data-label="Descripción">Selecciona: CC (Cédula), PA (Pasaporte), CE (Carnet de Extranjería), PT (Permiso Temporal)</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Número de Documento</strong></td>
                                <td data-label="Descripción">Número de identificación sin guiones ni espacios</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Correo Electrónico</strong></td>
                                <td data-label="Descripción">Email válido. Recibirás tus credenciales aquí</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Teléfono</strong></td>
                                <td data-label="Descripción">Número de contacto con código de país (ej: +507 6123-4567)</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Email importante:</strong> Usa un correo al que tengas acceso permanente. Todas las notificaciones del sistema se enviarán a este correo.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 3: Información Profesional -->
            <section id="paso-3" class="step-card">
                <div class="step-number">4</div>
                <h3 class="step-title">Información Profesional</h3>
                <div class="step-content">
                    <p>Proporciona tu información médica profesional:</p>

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
                                <td data-label="Campo"><strong>Especialidad Médica</strong></td>
                                <td data-label="Descripción">Selecciona tu especialidad del desplegable (ej: Medicina General, Cardiología, Pediatría)</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Número de Licencia</strong></td>
                                <td data-label="Descripción">Tu número de registro médico (opcional pero recomendado)</td>
                                <td data-label="Requerido"><span class="optional">Opcional</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Nombre de la Clínica</strong></td>
                                <td data-label="Descripción">Nombre de tu consultorio, clínica u hospital (opcional)</td>
                                <td data-label="Requerido"><span class="optional">Opcional</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Especialidad:</strong> Si no encuentras tu especialidad en la lista, selecciona la más cercana. Puedes contactar soporte después para añadir tu especialidad específica.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 4: Logo -->
            <section id="paso-4" class="step-card">
                <div class="step-number">5</div>
                <h3 class="step-title">Subir Logo (Opcional)</h3>
                <div class="step-content">
                    <p>Puedes subir el logo de tu clínica o consultorio:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-image me-2"></i>Requisitos del Logo</h6>
                        <ul>
                            <li><strong>Formato:</strong> JPG, JPEG o PNG</li>
                            <li><strong>Tamaño máximo:</strong> 2 MB</li>
                            <li><strong>Dimensiones recomendadas:</strong> 300x300 píxeles (cuadrado)</li>
                            <li><strong>Fondo:</strong> Preferiblemente transparente (PNG)</li>
                        </ul>
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Opcional pero recomendado:</strong> Tu logo aparecerá en el directorio médico, facturas, y reportes del sistema. Da profesionalismo a tu práctica.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 5: Código de Referido -->
            <section id="paso-5" class="step-card">
                <div class="step-number">6</div>
                <h3 class="step-title">Código de Referido (Opcional)</h3>
                <div class="step-content">
                    <p>Si tienes un código de referido de otro usuario SAMI, ingrésalo aquí:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-gift me-2"></i>Beneficios del Referido</h6>
                        <ul>
                            <li>Descuento en tu primera mensualidad</li>
                            <li>El usuario que te refirió también recibe beneficios</li>
                            <li>Puedes obtener tu propio código después de registrarte</li>
                        </ul>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Encuentra códigos:</strong> Pregunta a colegas que ya usen SAMI si tienen un código de referido para compartir.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 6: Términos y Envío -->
            <section id="paso-6" class="step-card step-success">
                <div class="step-number">7</div>
                <h3 class="step-title">Aceptar Términos y Enviar</h3>
                <div class="step-content">
                    <p>Para finalizar el registro:</p>

                    <ol>
                        <li>Lee los <strong>Términos y Condiciones</strong> del servicio</li>
                        <li>Marca la casilla <strong>"Acepto los términos y condiciones"</strong></li>
                        <li>Haz clic en el botón <strong>"Registrarse"</strong> o <strong>"Crear Cuenta"</strong></li>
                        <li>Espera la confirmación del sistema</li>
                    </ol>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Importante:</strong> Debes aceptar los términos y condiciones para poder crear tu cuenta. Asegúrate de leerlos antes de continuar.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Después del Registro -->
            <section id="post-registro" class="step-card step-info">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-flag-checkered me-2"></i>Después del Registro</h3>
                <div class="step-content">
                    <p>Una vez enviado el formulario, el sistema procesará tu solicitud:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-check-circle me-2"></i>Qué Sucede Ahora</h6>
                        <ol>
                            <li>El sistema crea tu cuenta automáticamente</li>
                            <li>Se genera un usuario con tu correo electrónico</li>
                            <li>Recibes un email con tus credenciales de acceso</li>
                            <li>Tu cuenta queda activa inmediatamente</li>
                        </ol>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Revisa tu email:</strong> Busca el correo de bienvenida de SAMI. Si no lo ves, revisa tu carpeta de spam o correo no deseado.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Flujo de Inicio de Sesión -->
            <section id="loggin" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-sign-in-alt me-2"></i>Flujo de Inicio de Sesión</h3>
                <div class="step-content">
                    <p>Después de recibir tus credenciales por email:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Pasos para Iniciar Sesión</h6>
                        <ol>
                            <li>Ve a <div class="code-block">https://sami.meditecpty.com/login</div></li>
                            <li>Ingresa tu <strong>correo electrónico</strong></li>
                            <li>Ingresa tu <strong>contraseña</strong> (enviada por email)</li>
                            <li>Haz clic en <strong>"Iniciar Sesión"</strong></li>
                        </ol>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/register/login.png') }}" alt="Pantalla de inicio de sesión" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-key"></i>
                        <div>
                            <strong>Contraseña inicial:</strong> La contraseña que recibes por email es temporal. El sistema te pedirá cambiarla en tu primer inicio de sesión.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Asistente de Configuración -->
            <section id="assistant" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-cogs me-2"></i>Asistente de Configuración Inicial</h3>
                <div class="step-content">
                    <p>Al entrar por primera vez, SAMI te guiará con un asistente de configuración:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-list-ol me-2"></i>Pasos del Asistente</h6>
                        <ol>
                            <li><strong>Cambiar contraseña:</strong> Establece una contraseña segura</li>
                            <li><strong>Configurar perfil:</strong> Completa tu información profesional</li>
                            <li><strong>Configurar clínica:</strong> Datos de tu consultorio/clínica</li>
                            <li><strong>Añadir sucursales:</strong> Si tienes más de una ubicación</li>
                            <li><strong>Tour guiado:</strong> Recorrido rápido por el sistema</li>
                        </ol>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Puedes omitir pasos:</strong> El asistente es opcional. Puedes configurarlo todo más tarde desde tu perfil.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Proceso de Pago -->
            <section id="pago" class="step-card step-important">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-credit-card me-2"></i>Proceso de Pago</h3>
                <div class="step-content">
                    <p>Para activar tu plan, debes completar el pago:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-money-check-alt me-2"></i>Métodos de Pago Disponibles</h6>
                        <ul>
                            <li><strong>Yappy:</strong> Pago móvil instantáneo (Panamá)</li>
                            <li><strong>Transferencia bancaria:</strong> Información de cuenta proporcionada</li>
                            <li><strong>Tarjeta de crédito:</strong> Procesamiento seguro (próximamente)</li>
                        </ul>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/register/activarpago.png') }}" alt="Pantalla de activación de pago" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Activación del plan:</strong> Tu suscripción se activa automáticamente una vez confirmado el pago. Esto puede tomar de minutos a 24 horas según el método de pago.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Flujo de Pago con Yappy -->
            <section id="que-sucede" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-mobile-alt me-2"></i>Flujo de Pago con el Botón de Yappy</h3>
                <div class="step-content">
                    <p>Si eliges pagar con Yappy (método recomendado en Panamá):</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Pasos del Pago con Yappy</h6>
                        <ol>
                            <li>Haz clic en el botón <strong>"Pagar con Yappy"</strong></li>
                            <li>Se abre un <strong>código QR</strong> en pantalla</li>
                            <li>Abre tu app de <strong>Yappy</strong> en tu teléfono</li>
                            <li>Escanea el <strong>código QR</strong> con la app</li>
                            <li>Confirma el monto en la app Yappy</li>
                            <li>Completa el pago con tu PIN de Yappy</li>
                            <li>El sistema detecta el pago automáticamente</li>
                            <li>Tu suscripción se activa al instante</li>
                        </ol>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/register/qr.png') }}" alt="Código QR de Yappy" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-bolt"></i>
                        <div>
                            <strong>Pago instantáneo:</strong> Yappy es el método más rápido. Tu plan se activa automáticamente en segundos después de completar el pago.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Siguientes Pasos -->
            <section id="siguientes-pasos" class="step-card step-success">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguientes Pasos</h3>
                <div class="step-content">
                    <p>Una vez completado el registro y activado tu plan, puedes comenzar a usar SAMI:</p>

                    <div class="info-box info-note">
                        <i class="fas fa-rocket"></i>
                        <div>
                            <strong>Listo para comenzar!</strong>
                            <ul class="mb-0 mt-2">
                                <li>Configura tu perfil y clínica</li>
                                <li>Registra tus pacientes</li>
                                <li>Comienza a agendar citas</li>
                                <li>Explora las funcionalidades del sistema</li>
                                <li>Contacta soporte si tienes dudas</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('help.index') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-home me-2"></i>Volver al Centro de Ayuda
                        </a>
                    </div>
                </div>
            </section>

            <!-- Contact Support -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #667eea;"></i>¿Tienes preguntas sobre el registro?</h5>
                            <p class="text-muted mb-0">Nuestro equipo de soporte está disponible para ayudarte.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="mailto:business@meditecpty.com" class="btn text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-envelope me-2"></i>Contactar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
