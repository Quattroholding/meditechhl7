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
    background: linear-gradient(180deg, #00897b 0%, #00695c 100%);
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
    background: linear-gradient(135deg, #00897b 0%, #00695c 100%);
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
    border-left: 5px solid #00897b;
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
    background: linear-gradient(135deg, #00897b 0%, #00695c 100%);
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
    color: #00695c;
    margin-bottom: 15px;
    padding-left: 45px;
}

.step-content {
    color: #555;
    line-height: 1.8;
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
    background: #00695c;
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
    color: #00695c;
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
    color: #00695c;
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
    color: #00695c;
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

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #00897b 0%, #00695c 100%);
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
}

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'inventory'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="#">Gestión de Inventario</a></li>
            <li class="breadcrumb-item active">Módulo de Inventario</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-boxes me-3"></i>Guía del Módulo de Inventario</h1>
        <p>Aprende a gestionar el inventario médico de tu clínica: artículos, stock, suministros clínicos, alertas de reabastecimiento y control completo de existencias.</p>
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
                            Sobre el Inventario
                        </a>
                    </li>
                    <li>
                        <a href="#diferencia">
                            <i class="fas fa-balance-scale"></i>
                            Stock Total vs Disponible
                        </a>
                    </li>
                    <li>
                        <a href="#paso-1">
                            <i class="fas fa-box"></i>
                            Paso 1: Crear Item
                        </a>
                    </li>
                    <li>
                        <a href="#paso-2">
                            <i class="fas fa-warehouse"></i>
                            Paso 2: Recibir Stock
                        </a>
                    </li>
                    <li>
                        <a href="#paso-3">
                            <i class="fas fa-exchange-alt"></i>
                            Paso 3: Transferir Stock
                        </a>
                    </li>
                    <li>
                        <a href="#paso-4">
                            <i class="fas fa-pills"></i>
                            Paso 4: Solicitar Suministros
                        </a>
                    </li>
                    <li>
                        <a href="#paso-5">
                            <i class="fas fa-chart-line"></i>
                            Paso 5: Monitorear Stock Bajo
                        </a>
                    </li>
                    <li>
                        <a href="#paso-6">
                            <i class="fas fa-undo-alt"></i>
                            Paso 6: Devolver Suministros
                        </a>
                    </li>
                    <li>
                        <a href="#historial">
                            <i class="fas fa-history"></i>
                            Historial de Transacciones
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
            <!-- Sobre el Inventario -->
            <section id="concepto" class="step-card step-info">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-info-circle me-2"></i>Sobre el Módulo de Inventario</h3>
                <div class="step-content">
                    <p>El módulo de <strong>Inventario</strong> te permite gestionar todos los suministros médicos, medicamentos, equipos y consumibles de tu clínica de manera eficiente y trazable.</p>

                    <ul class="checklist">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Catálogo completo</strong><br>
                                <small class="text-muted">Organiza todos tus artículos con SKU, códigos de barras y precios</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Control multi-ubicación</strong><br>
                                <small class="text-muted">Gestiona inventario por sucursal o por profesional individual</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Trazabilidad completa</strong><br>
                                <small class="text-muted">Rastreo de lotes, números de serie y fechas de vencimiento</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Alertas automáticas</strong><br>
                                <small class="text-muted">Notificaciones cuando el stock llega al punto de reorden</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Integración clínica</strong><br>
                                <small class="text-muted">Solicita y dispensa suministros directamente desde las consultas</small>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Cumplimiento FHIR</strong><br>
                                <small class="text-muted">Compatible con estándares internacionales de salud</small>
                            </div>
                        </li>
                    </ul>

                    <div class="info-box info-note">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Ventaja clave:</strong> Cada movimiento de inventario queda registrado permanentemente, proporcionando auditoría completa para cumplimiento normativo y control de costos.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stock Total vs Disponible -->
            <section id="diferencia" class="step-card step-important">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-balance-scale me-2"></i>Stock Total vs Stock Disponible</h3>
                <div class="step-content">
                    <p>Es crucial entender la diferencia entre estas dos métricas:</p>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #00897b !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #00897b;"><i class="fas fa-box me-2"></i>Stock Total</h6>
                                    <p class="mb-2"><code>quantity_on_hand</code></p>
                                    <p class="small text-muted mb-2">Cantidad física total que existe en el almacén.</p>
                                    <div class="alert alert-light mb-0">
                                        <strong>Ejemplo:</strong> 100 guantes en el almacén
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #388e3c !important;">
                                <div class="card-body">
                                    <h6 class="text-success fw-bold"><i class="fas fa-check-circle me-2"></i>Stock Disponible</h6>
                                    <p class="mb-2"><code>quantity_available</code></p>
                                    <p class="small text-muted mb-2">Cantidad que puedes usar inmediatamente.</p>
                                    <div class="alert alert-light mb-0">
                                        <strong>Fórmula:</strong><br>
                                        Disponible = Total - Reservado<br>
                                        70 = 100 - 30
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Stock Reservado:</strong> Son artículos que físicamente existen pero están comprometidos para:
                            <ul class="mb-0 mt-2">
                                <li>Solicitudes de suministro pendientes</li>
                                <li>Citas médicas programadas</li>
                                <li>Kits de procedimiento pre-armados</li>
                            </ul>
                        </div>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-calculator me-2"></i>Ejemplo Práctico</h6>
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Stock Total</th>
                                    <th>Reservado</th>
                                    <th>Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Guantes Talla L</td>
                                    <td>100</td>
                                    <td>30</td>
                                    <td class="text-success fw-bold">70</td>
                                </tr>
                                <tr>
                                    <td>Jeringas 5ml</td>
                                    <td>50</td>
                                    <td>45</td>
                                    <td class="text-warning fw-bold">5</td>
                                </tr>
                                <tr>
                                    <td>Mascarillas N95</td>
                                    <td>20</td>
                                    <td>20</td>
                                    <td class="text-danger fw-bold">0</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="small text-muted mb-0">Solo puedes dispensar de la columna "Disponible"</p>
                    </div>
                </div>
            </section>

            <!-- Paso 1: Crear Artículos -->
            <section id="paso-1" class="step-card">
                <div class="step-number">1</div>
                <h3 class="step-title">Crear Artículos de Inventario</h3>
                <div class="step-content">
                    <p>El primer paso es registrar los artículos en el catálogo de inventario:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Navegación</h6>
                        <ol>
                            <li>Inicia sesión en SAMI</li>
                            <li>En el menú lateral, busca <strong>"Inventario"</strong></li>
                            <li>Haz clic en <strong>"Artículos"</strong></li>
                            <li>Clic en el botón <strong>"+ Nuevo Artículo"</strong></li>
                        </ol>
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
                            <td data-label="Descripción">Nombre descriptivo del artículo (ej: "Guantes de Látex Talla M")</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>SKU</strong></td>
                            <td data-label="Descripción">Código único del artículo (ej: "GLV-LAT-M"). Debe ser único</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Tipo de Artículo</strong></td>
                            <td data-label="Descripción">Categoría: Suministro, Consumible, Equipo, Medicamento, Dispositivo</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Unidad de Medida</strong></td>
                            <td data-label="Descripción">Unidad, Caja, Vial, Mililitro (ml), Miligramo (mg), Gramo</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Costo Base</strong></td>
                            <td data-label="Descripción">Precio de compra del artículo</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Precio Base</strong></td>
                            <td data-label="Descripción">Precio de venta predeterminado</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Punto de Reorden</strong></td>
                            <td data-label="Descripción">Nivel mínimo antes de recibir alerta (ej: 20 unidades)</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Cantidad de Reorden</strong></td>
                            <td data-label="Descripción">Cantidad sugerida para reabastecer (ej: 100 unidades)</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Código de Barras</strong></td>
                            <td data-label="Descripción">Código de barras del producto para escaneo rápido</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Rastreo de Lote</strong></td>
                            <td data-label="Descripción">Checkbox: ¿Requiere número de lote?</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Rastreo de Serie</strong></td>
                            <td data-label="Descripción">Checkbox: ¿Requiere número de serie?</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Fecha de Vencimiento</strong></td>
                            <td data-label="Descripción">Checkbox: ¿Tiene fecha de vencimiento?</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/inventory/new_article.png') }}" alt="Vistas del calendario" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Consejo:</strong> Activa el rastreo de lotes y vencimientos para medicamentos y vacunas. Esto es crucial para cumplimiento normativo y retiro de productos.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 2: Recibir Stock -->
            <section id="paso-2" class="step-card step-success">
                <div class="step-number">2</div>
                <h3 class="step-title">Recibir Stock (Compras)</h3>
                <div class="step-content">
                    <p>Cuando recibes mercancía nueva, debes registrarla en el sistema:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Navegación</h6>
                        <ol>
                            <li>Ve a <strong>Inventario → Gestión de Stock</strong></li>
                            <li>Selecciona la operación <strong>"Recibir"</strong></li>
                            <li>Busca el artículo que recibiste</li>
                            <li>Selecciona la ubicación (sucursal o profesional)</li>
                        </ol>
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
                            <td data-label="Campo"><strong>Artículo</strong></td>
                            <td data-label="Descripción">Busca el artículo por nombre, SKU o código de barras</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Ubicación</strong></td>
                            <td data-label="Descripción">Sucursal donde se almacenará o profesional si tiene inventario individual</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Cantidad</strong></td>
                            <td data-label="Descripción">Cantidad recibida en la unidad de medida del artículo</td>
                            <td data-label="Requerido"><span class="required">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Costo Unitario</strong></td>
                            <td data-label="Descripción">Costo de cada unidad recibida (para control de costos)</td>
                            <td data-label="Requerido"><span class="optional">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Número de Lote</strong></td>
                            <td data-label="Descripción">Lote del fabricante (si el artículo requiere rastreo)</td>
                            <td data-label="Requerido"><span class="optional">Condicional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Fecha de Vencimiento</strong></td>
                            <td data-label="Descripción">Fecha de expiración del lote (si aplica)</td>
                            <td data-label="Requerido"><span class="optional">Condicional</span></td>
                        </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/inventory/stock_managment.png') }}" alt="Recibir Articulos de Inventario" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-clipboard-check"></i>
                        <div>
                            <strong>Resultado:</strong> Al guardar, el sistema:
                            <ul class="mb-0 mt-2">
                                <li>Incrementa el stock total en la ubicación seleccionada</li>
                                <li>Crea una transacción de tipo PURCHASE en el historial</li>
                                <li>Registra quién recibió el stock y cuándo</li>
                                <li>Si hay un punto de reorden configurado, actualiza las alertas</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Importante:</strong> Si el artículo requiere rastreo de lote o vencimiento, estos campos se vuelven obligatorios. No podrás guardar sin completarlos.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 3: Transferir Stock -->
            <section id="paso-3" class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Transferir Stock entre Ubicaciones</h3>
                <div class="step-content">
                    <p>Mueve stock entre sucursales o de la sucursal a un profesional:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Navegación</h6>
                        <ol>
                            <li>Ve a <strong>Inventario → Gestión de Stock</strong></li>
                            <li>Selecciona la operación <strong>"Transferir"</strong></li>
                            <li>Elige el artículo y las ubicaciones origen/destino</li>
                        </ol>
                    </div>

                    <table class="field-table">
                        <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Ubicación Origen</strong></td>
                            <td data-label="Descripción">De dónde sale el stock (sucursal o profesional)</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Ubicación Destino</strong></td>
                            <td data-label="Descripción">A dónde va el stock (sucursal o profesional)</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Cantidad</strong></td>
                            <td data-label="Descripción">Cantidad a transferir (debe ser ≤ disponible en origen)</td>
                        </tr>
                        </tbody>
                    </table>

                    <div>
                        <img src="{{ asset('images/tutorial/inventory/stock_transfer.png') }}" alt="Transferir Articulo de Inventario de un destino a otro" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-sitemap me-2"></i>Casos de Uso Comunes</h6>
                        <ul>
                            <li><strong>Sucursal → Profesional:</strong> Asignar stock personal a un médico que tiene inventario individual</li>
                            <li><strong>Profesional → Sucursal:</strong> Devolver stock no usado al almacén central</li>
                            <li><strong>Sucursal → Sucursal:</strong> Redistribuir inventario entre clínicas</li>
                            <li><strong>Profesional → Profesional:</strong> Transferir entre médicos</li>
                        </ul>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-check-double"></i>
                        <div>
                            <strong>Validación automática:</strong> El sistema verifica que haya stock suficiente disponible en el origen antes de permitir la transferencia.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 4: Solicitar Suministros -->
            <section id="paso-4" class="step-card step-primary">
                <div class="step-number">4</div>
                <h3 class="step-title">Solicitar Suministros en Consultas</h3>
                <div class="step-content">
                    <p>Durante una consulta médica, puedes solicitar suministros que se deducirán automáticamente del inventario:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Desde la Consulta</h6>
                        <ol>
                            <li>Abre una consulta médica (Encounter)</li>
                            <li>Navega a la pestaña <strong>"Suministros Médicos"</strong></li>
                            <li>Busca el artículo que necesitas</li>
                            <li>El sistema mostrará el stock disponible en tiempo real</li>
                            <li>Ingresa la cantidad y opciones de precio</li>
                        </ol>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #0288d1 !important;">
                                <div class="card-body">
                                    <h6 class="text-primary fw-bold"><i class="fas fa-dollar-sign me-2"></i>Precio Normal</h6>
                                    <p class="small mb-0">Usa el precio base configurado en el artículo. Se facturará al paciente.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #f57c00 !important;">
                                <div class="card-body">
                                    <h6 class="fw-bold" style="color: #f57c00;"><i class="fas fa-edit me-2"></i>Precio Personalizado</h6>
                                    <p class="small mb-0">Define un precio específico para esta dispensación (sobreescribe el base).</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #388e3c !important;">
                                <div class="card-body">
                                    <h6 class="text-success fw-bold"><i class="fas fa-gift me-2"></i>Gratuito</h6>
                                    <p class="small mb-0">Marca como cortesía. Se registra el uso pero no se factura.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <img src="{{ asset('images/tutorial/inventory/encounter_supply.png') }}" alt="Suministro de Articulo de inventario en consulta" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-sync-alt"></i>
                        <div>
                            <strong>Flujo completo:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Se crea una <strong>Solicitud de Suministro</strong> (SupplyRequest) en estado DRAFT</li>
                                <li>Al cumplir la solicitud, se crea una <strong>Entrega</strong> (SupplyDelivery)</li>
                                <li>El stock se deduce automáticamente del inventario</li>
                                <li>Se registra la transacción de tipo SUPPLY_DELIVERY</li>
                                <li>Si es facturable, se genera un cargo (ChargeItem)</li>
                            </ol>
                        </div>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Validación de Stock:</strong> No puedes solicitar más cantidad de la disponible. El sistema te alertará si intentas exceder el stock.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 5: Monitorear Stock Bajo -->
            <section id="paso-5" class="step-card step-important">
                <div class="step-number">5</div>
                <h3 class="step-title">Monitorear Stock Bajo</h3>
                <div class="step-content">
                    <p>El sistema alerta automáticamente cuando los artículos necesitan reabastecimiento:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Ver Reporte</h6>
                        <ol>
                            <li>Ve a <strong>Inventario → Reportes → Stock Bajo</strong></li>
                            <li>Verás todos los artículos con stock ≤ punto de reorden</li>
                            <li>Ordenados por urgencia (menor stock primero)</li>
                        </ol>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-table me-2"></i>Información Mostrada</h6>
                        <ul>
                            <li><strong>Artículo:</strong> Nombre y SKU</li>
                            <li><strong>Stock Disponible:</strong> Cantidad actual disponible</li>
                            <li><strong>Punto de Reorden:</strong> Nivel mínimo configurado</li>
                            <li><strong>Déficit:</strong> Cuánto te falta para llegar al punto de reorden</li>
                            <li><strong>Cantidad Sugerida:</strong> Cantidad de reorden configurada</li>
                        </ul>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm bg-danger bg-opacity-10">
                                <div class="card-body">
                                    <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                                    <h6 class="text-danger fw-bold">Sin Stock</h6>
                                    <p class="small mb-0">Disponible = 0<br>Requiere atención inmediata</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-2"></i>
                                    <h6 class="text-warning fw-bold">Stock Bajo</h6>
                                    <p class="small mb-0">Disponible ≤ Punto Reorden<br>Planificar reabastecimiento</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm bg-success bg-opacity-10">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                    <h6 class="text-success fw-bold">Stock OK</h6>
                                    <p class="small mb-0">Disponible > Punto Reorden<br>No requiere acción</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-bell"></i>
                        <div>
                            <strong>Notificaciones:</strong> Puedes configurar el sistema para enviar emails automáticos cuando un artículo llega al punto de reorden.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 6: Devolver Suministros -->
            <section id="paso-6" class="step-card step-primary">
                <div class="step-number">6</div>
                <h3 class="step-title">Devolver Suministros Dispensados</h3>
                <div class="step-content">
                    <p>Si dispensaste un suministro incorrecto durante una consulta, puedes devolverlo al inventario:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>¿Cuándo Devolver?</h6>
                        <ul>
                            <li><strong>Artículo Incorrecto:</strong> Se dispensó el item equivocado</li>
                            <li><strong>Cantidad Incorrecta:</strong> Se registró más de lo que se usó</li>
                            <li><strong>No Usado:</strong> El paciente no requirió el suministro finalmente</li>
                            <li><strong>Paciente Rechazó:</strong> El paciente decidió no usar el suministro</li>
                            <li><strong>Vencido/Dañado:</strong> Se detectó que el producto estaba en mal estado</li>
                        </ul>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-mouse-pointer me-2"></i>Proceso de Devolución</h6>
                        <ol>
                            <li>Abre la <strong>consulta donde se dispensó el suministro</strong></li>
                            <li>Ve a la pestaña <strong>"Suministros Médicos"</strong></li>
                            <li>Busca la sección <strong>"Suministros Dispensados"</strong> (card verde)</li>
                            <li>Encuentra el suministro que deseas devolver</li>
                            <li>Haz clic en el botón <strong>"Devolver"</strong></li>
                        </ol>
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Importante:</strong> Solo puedes devolver suministros desde la consulta donde fueron dispensados. Los suministros deben estar en estado "Completado".
                        </div>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-edit me-2"></i>Completar el Formulario de Devolución</h6>
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
                                <td data-label="Campo"><strong>Cantidad a Devolver</strong></td>
                                <td data-label="Descripción">Cantidad que deseas devolver. Puede ser total o parcial.</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Razón de Devolución</strong></td>
                                <td data-label="Descripción">Selecciona el motivo: Artículo Incorrecto, Cantidad Incorrecta, No Usado, Paciente Rechazó, Vencido, Dañado, Otro</td>
                                <td data-label="Requerido"><span class="required">Sí</span></td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Notas Adicionales</strong></td>
                                <td data-label="Descripción">Detalles adicionales sobre la devolución (opcional)</td>
                                <td data-label="Requerido"><span class="optional">Opcional</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-cogs me-2"></i>¿Qué Sucede al Procesar la Devolución?</h6>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #00897b !important;">
                                    <div class="card-body">
                                        <h6 class="fw-bold" style="color: #00897b;"><i class="fas fa-box me-2"></i>1. Stock Restaurado</h6>
                                        <p class="small mb-0">La cantidad devuelta se suma automáticamente al inventario de donde se tomó (sucursal o profesional).</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #0288d1 !important;">
                                    <div class="card-body">
                                        <h6 class="text-primary fw-bold"><i class="fas fa-exchange-alt me-2"></i>2. Transacción Registrada</h6>
                                        <p class="small mb-0">Se crea una transacción de tipo RETURN en el historial de inventario.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #f57c00 !important;">
                                    <div class="card-body">
                                        <h6 class="fw-bold" style="color: #f57c00;"><i class="fas fa-dollar-sign me-2"></i>3. Cargo Ajustado</h6>
                                        <p class="small mb-0">Si era facturable, el cargo se ajusta (parcial) o anula (total).</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm" style="border-left: 4px solid #388e3c !important;">
                                    <div class="card-body">
                                        <h6 class="text-success fw-bold"><i class="fas fa-clipboard-check me-2"></i>4. Auditoría Completa</h6>
                                        <p class="small mb-0">Se registra quién, cuándo, por qué y cuánto se devolvió.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-percent me-2"></i>Devoluciones Parciales vs Totales</h6>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning bg-opacity-10">
                                        <strong>Devolución Parcial</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="small"><strong>Ejemplo:</strong> Dispensaste 10 guantes, solo usaste 6</p>
                                        <p class="small mb-2"><strong>Devuelves:</strong> 4 guantes</p>
                                        <p class="small mb-0">
                                            <i class="fas fa-check text-success"></i> Stock: +4 unidades<br>
                                            <i class="fas fa-dollar-sign text-warning"></i> Cargo: Ajustado a 6 unidades<br>
                                            <i class="fas fa-undo text-info"></i> Puedes devolver más después
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger bg-opacity-10">
                                        <strong>Devolución Total</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="small"><strong>Ejemplo:</strong> Dispensaste 10 gasas por error</p>
                                        <p class="small mb-2"><strong>Devuelves:</strong> 10 gasas (todo)</p>
                                        <p class="small mb-0">
                                            <i class="fas fa-check text-success"></i> Stock: +10 unidades<br>
                                            <i class="fas fa-ban text-danger"></i> Cargo: Anulado completamente<br>
                                            <i class="fas fa-lock text-secondary"></i> No se puede devolver más
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-history me-2"></i>Ver Historial de Devoluciones</h6>
                        <p>Si un suministro tiene devoluciones, verás:</p>
                        <ul>
                            <li>Cantidad total devuelta (en color amarillo)</li>
                            <li>Cantidad neta = Dispensado - Devuelto (en color verde)</li>
                            <li>Botón "Ver devoluciones (X)" que despliega el detalle completo</li>
                            <li>Cada devolución muestra: cantidad, razón, fecha, usuario y notas</li>
                        </ul>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Validaciones Importantes:</strong>
                            <ul class="mb-0 mt-2">
                                <li>No puedes devolver más de lo dispensado</li>
                                <li>La cantidad debe ser mayor a 0</li>
                                <li>La razón es obligatoria</li>
                                <li>Si ya devolviste todo, el botón "Devolver" se deshabilitará</li>
                                <li>Las devoluciones quedan registradas permanentemente</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-box info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Mejores Prácticas:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Revisa antes de finalizar:</strong> Verifica los suministros en la consulta ANTES de finalizarla</li>
                                <li><strong>Devuelve rápido:</strong> Procesa devoluciones lo antes posible para mantener el inventario actualizado</li>
                                <li><strong>Usa notas:</strong> Agrega detalles útiles que expliquen el contexto de la devolución</li>
                                <li><strong>Verifica el stock:</strong> Confirma en Inventario que el stock se restauró correctamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Historial de Transacciones -->
            <section id="historial" class="step-card">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-history me-2"></i>Historial de Transacciones</h3>
                <div class="step-content">
                    <p>Todas las transacciones de inventario quedan registradas permanentemente para auditoría:</p>

                    <div class="sub-step">
                        <h6><i class="fas fa-route me-2"></i>Ver Historial</h6>
                        <ol>
                            <li>Ve a <strong>Inventario → Reportes → Historial de Transacciones</strong></li>
                            <li>Filtra por artículo, tipo de transacción o rango de fechas</li>
                            <li>Cada registro muestra quién, qué, cuándo y por qué</li>
                        </ol>
                    </div>

                    <div class="sub-step">
                        <h6><i class="fas fa-tags me-2"></i>Tipos de Transacciones</h6>
                        <ul>
                            <li><strong>PURCHASE:</strong> Recepción de mercancía nueva</li>
                            <li><strong>ADJUSTMENT:</strong> Corrección de niveles de stock (inventario físico)</li>
                            <li><strong>TRANSFER:</strong> Movimiento entre ubicaciones</li>
                            <li><strong>SUPPLY_DELIVERY:</strong> Dispensación a pacientes</li>
                            <li><strong>RETURN:</strong> Devoluciones de stock</li>
                            <li><strong>DISPOSAL:</strong> Baja de inventario (vencimiento, daño)</li>
                            <li><strong>COUNT:</strong> Conteos de inventario</li>
                        </ul>
                    </div>


                    <div>
                        <img src="{{ asset('images/tutorial/inventory/transaction_history.png') }}" alt="Recibir Articulos de Inventario" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    </div>

                    <div class="info-box info-note">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Auditoría Completa:</strong> Cada transacción registra:
                            <ul class="mb-0 mt-2">
                                <li>Usuario que realizó la operación</li>
                                <li>Fecha y hora exacta</li>
                                <li>Cantidad antes y después</li>
                                <li>Ubicaciones origen/destino (si aplica)</li>
                                <li>Paciente y consulta (si aplica)</li>
                                <li>Lote y vencimiento (si aplica)</li>
                                <li>Costo unitario y total (si aplica)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-box info-warning">
                        <i class="fas fa-lock"></i>
                        <div>
                            <strong>Inmutabilidad:</strong> Las transacciones NO se pueden editar ni eliminar. Son permanentes para garantizar la integridad del historial.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Siguiente Paso -->
            <section id="siguiente" class="step-card step-success">
                <h3 class="step-title" style="padding-left: 0;"><i class="fas fa-arrow-right me-2"></i>Siguiente Paso: Integración con Facturación</h3>
                <div class="step-content">
                    <p>Ahora que dominas la gestión de inventario, aprende cómo los suministros se integran con la <strong>facturación</strong> automática.</p>

                    <div class="info-box info-note">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <div>
                            <strong>Integración automática:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Los suministros facturables generan automáticamente cargos (ChargeItem)</li>
                                <li>Puedes revisar y ajustar los cargos antes de crear la factura</li>
                                <li>El sistema vincula cada cargo con la consulta y el paciente</li>
                                <li>Los precios personalizados se respetan en la facturación</li>
                                <li>Los suministros gratuitos se registran pero no se facturan</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('help.billing') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #00897b 0%, #00695c 100%);">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Continuar: Facturación
                        </a>
                    </div>
                </div>
            </section>

            <!-- Contact Support -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2"><i class="fas fa-question-circle me-2" style="color: #00897b;"></i>¿Tienes preguntas sobre el inventario?</h5>
                            <p class="text-muted mb-0">Nuestro equipo de soporte está disponible para ayudarte.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="mailto:business@meditecpty.com" class="btn text-white" style="background: linear-gradient(135deg, #00897b 0%, #00695c 100%);">
                                <i class="fas fa-envelope me-2"></i>Contactar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
