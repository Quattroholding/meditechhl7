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

:root {
    --billing-color: #2e7d32;
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

/* Header */
.help-header {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.help-header h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.help-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.help-header .breadcrumb {
    background: rgba(255,255,255,0.15);
    padding: 10px 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.help-header .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
}

.help-header .breadcrumb-item.active {
    color: #fff;
}

/* Content Sections */
.content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.content-section h2 {
    color: #2e7d32;
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e8f5e9;
}

.content-section h3 {
    color: #333;
    font-size: 1.3rem;
    font-weight: 600;
    margin-top: 25px;
    margin-bottom: 15px;
}

.content-section h4 {
    color: #555;
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 20px;
    margin-bottom: 10px;
}

/* Steps */
.step-card {
    background: linear-gradient(135deg, #f1f8f4 0%, #fff 100%);
    border-left: 4px solid #2e7d32;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    background: #2e7d32;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    margin-right: 15px;
}

.step-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

/* Screenshot Placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border: 2px dashed #4caf50;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 20px 0;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.screenshot-placeholder i {
    font-size: 3rem;
    color: #2e7d32;
    margin-bottom: 15px;
}

.screenshot-placeholder p {
    color: #1b5e20;
    font-weight: 500;
    margin-bottom: 5px;
}

.screenshot-placeholder small {
    color: #2e7d32;
}

/* Info Boxes */
.info-box {
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
}

.info-box.note {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.info-box.warning {
    background: #fff3e0;
    border-left: 4px solid #ff9800;
}

.info-box.tip {
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
}

.info-box.danger {
    background: #ffebee;
    border-left: 4px solid #f44336;
}

.info-box-title {
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
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
    color: #2e7d32;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e8f5e9;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-list li {
    margin-bottom: 12px;
}

.toc-list a {
    color: #1b5e20;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    background: #e8f5e9;
    color: #2e7d32;
}

.toc-list a i {
    width: 20px;
    text-align: center;
}

/* Field Tables */
.field-table-wrapper {
    width: 100%;
    overflow-x: auto;
    margin: 20px 0;
}
.field-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.field-table th {
    background: #2e7d32;
    color: white;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
}

.field-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.field-table tr:hover {
    background: #f1f8f4;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    margin: 3px;
}

.status-issued { background: #e3f2fd; color: #1565c0; }
.status-balanced { background: #e8f5e9; color: #2e7d32; }
.status-cancelled { background: #ffebee; color: #c62828; }
.status-unpaid { background: #ffe5f6; color: #ff01a2; }
.status-partially-paid { background: #fef5e4; color: #ff9b01; }
.status-paid { background:rgba(0, 211, 199, 0.1); color: #00D3C7; }

/* Back to top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: #2e7d32;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
    transition: all 0.3s ease;
}

.back-to-top:hover {
    background: #1b5e20;
    color: white;
    transform: translateY(-3px);
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

@media (max-width: 480px) {
    .step-card, .info-box{
            border-left: none;
            padding: 25px;
            border-bottom: 5px solid #2e7d32;
            border-radius: 8px;
        }

        .info-box.note {
            border-left: none;
            border-bottom-color: #2196f3;
        }
        .info-box.warning {
            border-left: none;
            border-bottom-color: #ff9800;
        }
        .info-box.tip {
            border-left: none;
            border-bottom-color: #4caf50;
        }
        .info-box.danger {
            border-left: none;
            border-bottom-color: #f44336;
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

        /* Campo (título de cada tarjeta) */
        .field-table td[data-label="Estado"],  .field-table td[data-label="Campo"] {
            background: #2e7d32;
            color: white;
            font-size: 15px;
            padding: 12px;
            border-bottom: 2px solid #2e7931;
        }


        .field-table td[data-label="Estado"] strong,  .field-table td[data-label="Campo"] strong {
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
        .field-table td[data-label="Badge"], .field-table td[data-label="Valor"] {
            background: #f5f5f5;
            padding: 12px 15px;
            text-align: center;
            font-weight: 600;
        }

        .field-table td[data-label="Valor"]:before {
            content: "Valor por defecto: ";
            font-weight: 600;
            color: #2e7d32;
            margin-right: 5px;
        }

    }


    @media (max-width: 365px){
        .info-box-title, .content-section h4{
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .step-number {
            margin-right: 0;
        }
        .content-section, .help-content {
            padding:25px;
        }
        .info-box{
            padding: 20px;
        }
        .step-card{
            padding: 15px;
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

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'billing'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('help.billing') }}">Guias de Facturación</a></li>
            <li class="breadcrumb-item active">Facturación</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <!-- Header -->
    <header class="help-header">
        <h1><i class="fas fa-file-invoice-dollar me-3"></i>Guía de Facturación</h1>
        <p>Aprende cómo generar facturas automáticamente desde las consultas médicas en SAMI.</p>

    </header>
@stop
@section('table-content')
    <!-- Main Content Area -->
    <div class="row">
        <!-- Table of Contents -->
        <div class="col-lg-4">
            <div class="toc-card sticky-top" style="top: 20px;">
                <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                <ul class="toc-list">
                    <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                    <li><a href="#servicios"><i class="fas fa-plus-circle"></i> 2. Agregar Servicios</a></li>
                    <li><a href="#generacion"><i class="fas fa-file-invoice"></i> 3. Generación Automática</a></li>
                    <li><a href="#gestion"><i class="fas fa-tasks"></i> 4. Gestión de Facturas</a></li>
                    <li><a href="#estados"><i class="fas fa-exchange-alt"></i> 5. Estados</a></li>
                    <li><a href="#tips"><i class="fas fa-lightbulb"></i> 6. Tips</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-8">

    <!-- Section 1: Introduction -->
    <section id="introduccion" class="content-section">
        <h2><i class="fas fa-info-circle me-2"></i>1. Introducción a la Facturación</h2>

        <p>El módulo de <strong>Facturación</strong> en SAMI permite generar facturas automáticamente al finalizar las consultas médicas. El sistema crea facturas basándose en los servicios facturables que el médico agregó durante la consulta.</p>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-check-circle text-success"></i>
                        Características Principales
                    </div>
                    <ul class="mb-0">
                        <li>Generación automática al finalizar consulta</li>
                        <li>Basada en servicios del catálogo</li>
                        <li>Cálculo automático de impuestos (7% ITBMS)</li>
                        <li>Generación de PDF descargable</li>
                        <li>Seguimiento de estados de pago</li>
                        <li>Integración con cuentas de pacientes</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box note">
                    <div class="info-box-title">
                        <i class="fas fa-lightbulb text-primary"></i>
                        Flujo de Facturación
                    </div>
                    <ol class="mb-0">
                        <li>Durante la <a href="{{ route('help.consultation') }}">consulta</a>, agregar servicios facturables</li>
                        <li>Finalizar la consulta</li>
                        <li>El sistema genera automáticamente la factura</li>
                        <li>Descargar PDF de la factura</li>
                        <li>Registrar <a href="{{ route('help.payments') }}">pagos</a> contra la factura</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Importante
            </div>
            <p class="mb-0">Las facturas solo se generan si el médico ha agregado al menos un servicio facturable durante la consulta. Si no se agregan servicios, la consulta se finalizará sin generar factura.</p>
        </div>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_list.png') }}" alt="" style="width: 100%;">
        </div>
    </section>

    <!-- Section 2: Adding Billable Services -->
    <section id="servicios" class="content-section">
        <h2><i class="fas fa-plus-circle me-2"></i>2. Agregar Servicios Facturables en Consulta</h2>

        <p>Durante la consulta médica, el profesional de salud debe agregar los servicios que desea facturar al paciente. Estos servicios provienen del <strong>Catálogo de Servicios</strong> configurado previamente.</p>

        <!-- Step 1 -->
        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a la Sección de Servicios</span></h4>
            <p>Durante la consulta:</p>
            <ul>
                <li>Desplácese hacia abajo en la interfaz de consulta</li>
                <li>Localice y expanda la sección <strong>"Servicios Facturables"</strong></li>
                <li>Esta sección muestra los servicios ya agregados</li>
            </ul>

            <div>
                <img src="{{ asset('images/tutorial/invoices/invoice_cs.png') }}" alt="" style="width: 100%;">
            </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Creación de nuevos Servicios
            </div>
            <p class="mb-0">Para poder crear nuevos servicios facturables, deberá ir a la sección de <a href="{{ route('help.settings') }}">Configuraciones → Servicios</a> y crear los servicios que necesite para poder generar las facturas a los pacientes.</p>
        </div>
        </div>

        <!-- Step 2 -->
        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Buscar Servicio en el Catálogo</span></h4>
            <p>Para agregar un servicio:</p>
            <ul>
                <li>Seleccione la categoría de Servicio</li>
                <!--<li>Se abrirá un modal o panel de búsqueda</li>-->
                <li>Busque y seleccione el Servicio a facturar en la consulta</li>
                <li>Haga clic en <strong>"Agregar"</strong></li>
                <li>Los resultados muestran:
                    <ul>
                        <li>Nombre del servicio</li>
                        <li>Código CPT (si aplica)</li>
                        <li>Tipo de servicio</li>
                        <li>Descripción</li>
                        <li>Precio asignado al servicio</li>
                        <!--<li>Precio base</li>-->
                    </ul>
                </li>
            </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/invoice_add.png') }}" alt="" style="width: 100%;">
            </div>
            {{--}}
            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb text-success"></i>
                    Acceso Rápido
                </div>
                <p class="mb-0">Esta sección cuenta con un "Directorio de servicios por categoria" , para acceder a el haga clic en <strong>"Listado de Servicios"</strong>. Esto permite agregar servicios comunes con un sólo clic.</p>
            </div>
            {{--}}
        </div>

        <!-- Step 3 -->
        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Configurar Cantidad y Precio</span></h4>
            <p>Al seleccionar un servicio, puede personalizar:</p>
            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Valor por Defecto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Cantidad</strong></td>
                            <td data-label="Descripcion">Número de unidades del servicio</td>
                            <td data-label="Valor">1</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Precio Unitario</strong></td>
                            <td data-label="Descripcion">Precio por unidad (puede modificarse)</td>
                            <td data-label="Valor">Precio base del catálogo</td>
                        </tr>
                        <!--<tr>
                            <td data-label="Campo"><strong>Notas</strong></td>
                            <td data-label="Descripcion">Información adicional sobre el servicio</td>
                            <td data-label="Valor">Vacío (opcional)</td>
                        </tr>-->
                    </tbody>
                </table>
            </div>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Precios Personalizados
                </div>
                <p class="mb-0">Si modifica el precio de un servicio, asegúrese de que sea correcto antes de finalizar la consulta. Una vez generada la factura, no podrá modificar los precios.</p>
            </div>
        </div>

        <!-- Step 4 -->
        {{--}}<div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Confirmar y Agregar</span></h4>
            <p>Para confirmar el servicio:</p>
            <ul>
                <li>Revise la cantidad y precio</li>
                <li>Haga clic en <strong>"Agregar"</strong> o <strong>"Confirmar"</strong></li>
                <li>El servicio aparecerá en la lista de servicios agregados</li>
                <li>Puede agregar múltiples servicios repitiendo el proceso</li>
            </ul>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_modify.png') }}" alt="" style="width: 100%;">
        </div>
        </div>{{--}}

        <!-- Step 5 -->
        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Modificar o Eliminar Servicios</span></h4>
            <p>Antes de finalizar la consulta, puede:</p>
            <ul>
                <li><strong>Modificar cantidad:</strong> Cambiar el número de unidades</li>
                <li><strong>Modificar precio:</strong> Ajustar el precio unitario</li>
                <li><strong>Eliminar servicio:</strong> haga clic en el ícono rojo del basurero para quitar un servicio de la lista, el sistema mostrará un modal para que confrme la eliminación del mismo.</li>
            </ul>

            <div>
                <img src="{{ asset('images/tutorial/encounters/encounter_services.png') }}" alt="" style="width: 100%;">
            </div>

            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-ban text-danger"></i>
                    Restricción Importante
                </div>
                <p class="mb-0">Una vez que la consulta se finaliza y se genera la factura, <strong>NO podrá modificar ni eliminar servicios</strong>. Los servicios quedan marcados como "facturados" y están bloqueados para edición.</p>
            </div>
        </div>

        <!--<h3>Visualización del Total</h3>
        <p>Mientras agrega servicios, el sistema muestra:</p>
        <ul>
            <li><strong>Subtotal:</strong> Suma de todos los servicios (cantidad × precio)</li>
            <li><strong>Total estimado:</strong> Incluye impuestos (calculados al finalizar)</li>
        </ul>-->

        <!--<div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-calculator text-primary"></i>
                Cálculo de Impuestos
            </div>
            <p class="mb-0">El sistema aplica automáticamente el 7% de ITBMS (Impuesto de Transferencia de Bienes Muebles y Servicios) sobre el subtotal al generar la factura. Este cálculo se realiza al finalizar la consulta.</p>
        </div>-->
    </section>

    <!-- Section 3: Automatic Invoice Generation -->
    <section id="generacion" class="content-section">
        <h2><i class="fas fa-file-invoice me-2"></i>3. Generación Automática de Facturas</h2>

        <p>Cuando el médico finaliza la consulta, el sistema verifica si hay servicios facturables y genera automáticamente la factura correspondiente.</p>

        <!-- Step 1 -->
        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Finalizar la Consulta</span></h4>
            <p>Para generar la factura:</p>
            <ul>
                <li>Complete toda la documentación de la consulta</li>
                <li>Asegúrese de haber agregado todos los servicios facturables</li>
                <li>Haga clic en el botón <strong>"Finalizar Consulta"</strong></li>
                <!--<li>Confirme la acción cuando se le solicite</li>-->
            </ul>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Antes de Finalizar
                </div>
                <p class="mb-0">Revise cuidadosamente todos los servicios agregados antes de finalizar la consulta. Una vez finalizada, no podrá agregar, modificar o eliminar servicios facturables.</p>
            </div>
        </div>

        <!-- Step 2 -->
        {{--}}<div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Proceso Automático</span></h4>
            <p>Al finalizar la consulta, el sistema ejecuta automáticamente:</p>
            <ol>
                <li><strong>Verificación:</strong> Busca servicios con estado "billable"</li>
                <li><strong>Cuenta del Paciente:</strong> Crea o localiza la cuenta del paciente</li>
                <li><strong>Creación de Factura:</strong> Genera un nuevo registro de factura con:
                    <ul>
                        <li>Número de factura único (formato: INV-YYYYMMDD-XXXXXX)</li>
                        <li>Fecha de emisión</li>
                        <li>Fecha de vencimiento (30 días por defecto)</li>
                        <li>Estado: "Issued" (Emitida)</li>
                        <li>Estado de pago: "Unpaid" (No pagada)</li>
                    </ul>
                </li>
                <li><strong>Líneas de Factura:</strong> Crea una línea por cada servicio con:
                    <ul>
                        <li>Descripción del servicio</li>
                        <li>Código CPT</li>
                        <li>Cantidad</li>
                        <li>Precio unitario</li>
                        <li>Total de línea</li>
                    </ul>
                </li>
                <li><strong>Cálculos:</strong>
                    <ul>
                        <li>Subtotal = Suma de todas las líneas</li>
                        <li>Impuesto = Subtotal × 7%</li>
                        <li>Total = Subtotal + Impuesto</li>
                        <li>Monto adeudado = Total</li>
                    </ul>
                </li>
                <li><strong>Marcado de Servicios:</strong> Los ChargeItems se marcan como "billed"</li>
            </ol>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_encounter.png') }}" alt="" style="width: 100%;">
        </div>
        </div>{{--}}

        <!-- Step 3 -->
        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Descargar PDF de Factura</span></h4>
            <p>Una vez generada la factura:</p>
            <ul>
                <li>El sistema muestra un mensaje de éxito con el número de factura</li>
                <li>Aparece un botón <strong>"Descargar PDF"</strong></li>
                <li>Haga clic para descargar la factura en formato PDF</li>
                <li>El PDF incluye:
                    <ul>
                        <li>Información de la clínica/consultorio</li>
                        <li>Información del paciente</li>
                        <li>Información de factura</li>
                        <li>Información de la consulta</li>
                        <li>Detalle de servicios</li>
                        <li>Subtotal, impuestos y total</li>
                        <li>Términos de pago</li>
                    </ul>
                </li>
            </ul>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_pdf1.png') }}" alt="" style="width: 100%;">
        </div>
        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_pdf2.png') }}" alt="" style="width: 100%;">
        </div>
        </div>
        <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb text-success"></i>
                    Plantillas de Facturación
                </div>
                <p class="mb-0">En la sección de <a href="{{ route('help.settings') }}">Configuraciones → Plantilla de Factura</a> podrá elegir el diseño que más le guste para las facturas de sus clientes.</p>
            </div>

        <h3>¿Qué Pasa si No Hay Servicios Facturables?</h3>
        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Sin Servicios Facturables
            </div>
            <p>Si finaliza una consulta sin haber agregado servicios facturables:</p>
            <ul class="mb-0">
                <li>La consulta se finalizará normalmente</li>
                <li><strong>NO se generará factura</strong></li>
                <li>El sistema mostrará un mensaje: "(Sin servicios facturables)"</li>
                <li>La cita cambiará a estado "Finalizado"</li>
                <li>Esto es normal para consultas de seguimiento sin costo o consultas cubiertas por seguro</li>
            </ul>
        </div>
    </section>

    <!-- Section 4: Invoice Management -->
    <section id="gestion" class="content-section">
        <h2><i class="fas fa-tasks me-2"></i>4. Gestión de Facturas</h2>

        <p>El módulo de facturas permite ver, buscar y gestionar todas las facturas generadas en el sistema.</p>

        <!-- Access -->
        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder al Módulo de Facturas</span></h4>
            <p>Para ver las facturas:</p>
            <ul>
                <li>Navegue a <strong>Cuentas → Facturas</strong> desde el menú principal</li>
                <li>Se mostrará el listado de todas las facturas</li>
                <li>Puede filtrar por:
                    <ul>
                        <li>Nombre del Paciente</li>
                        <li>ID de Factura</li>
                    </ul>
                </li>
            </ul>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_list2.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <!-- View Details -->
        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Ver Detalle de Factura</span></h4>
            <p>Para ver el detalle completo de una factura:</p>
            <ul>
                <li>En el listado, haga clic en el número de factura o en el ícono de <i class="fas fa-eye text-primary"></i> "Ver"</li>
                <li>Se mostrará una página con:
                    <ul>
                        <li><strong>Información General:</strong> Número de factura, número identificador, estado de emisión, estado de pago, datos del centro clínico, consultorio o médico, datos del paciente, fecha de emisión y de vencimiento, id de consulta y términos de pago.</li>
                        <li><strong>Líneas de Factura:</strong> Detalle de cada servicio</li>
                        <li><strong>Totales:</strong> Precio unitario, el total de la línea, el subtotal, impuestos y monto total </li>
                        <li><strong>Estado de Pago:</strong> Monto pagado, monto pendiente</li>
                        <li><strong>Historial de Pagos:</strong> Pagos aplicados a esta factura</li>
                        <li><strong>Acciones:</strong> Descargar PDF, Vista Previa</li>
                    </ul>
                </li>
            </ul>

        <div>
            <img src="{{ asset('images/tutorial/invoices/invoice_detail.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <!-- Download PDF -->
        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Descargar PDF</span></h4>
            <p>Puede descargar el PDF de una factura en cualquier momento:</p>
            <ul>
                <li>Desde el listado: Haga clic en el ícono <i class="fas fa-download text-success"></i></li>
                <li>Desde el detalle: Haga clic en el botón <strong>"Descargar PDF"</strong></li>
                <li>El PDF se genera en tiempo real con la información actualizada</li>
                <li>Puede imprimir o enviar el PDF al paciente</li>
            </ul>
        </div>
    </section>

    <!-- Section 5: Invoice States -->
    <section id="estados" class="content-section">
        <h2><i class="fas fa-exchange-alt me-2"></i>5. Estados de Factura y Pago</h2>

        <h3>Estados de Factura</h3>
        <p>Las facturas pueden tener los siguientes estados:</p>
        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Descripción</th>
                        <th>Badge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Estado"><strong>Issued</strong></td>
                        <td data-label="Descripcion">Factura emitida y activa</td>
                        <td data-label="Badge"><span class="status-badge status-issued">Emitida</span></td>
                    </tr>
                    <!--<tr>
                        <td data-label="Estado"><strong>Balanced</strong></td>
                        <td data-label="Descripcion">Factura completamente pagada y balanceada</td>
                        <td data-label="Badge"><span class="status-badge status-balanced">Balanceada</span></td>
                    </tr>
                    <tr>
                        <td data-label="Estado"><strong>Cancelled</strong></td>
                        <td data-label="Descripcion">Factura cancelada o anulada</td>
                        <td data-label="Badge"><span class="status-badge status-cancelled">Cancelada</span></td>
                    </tr>-->
                </tbody>
            </table>
        </div>

        <h3 class="mt-4">Estados de Pago</h3>
        <p>Independientemente del estado de la factura, el estado de pago indica cuánto se ha pagado:</p>
        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Estado de Pago</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Badge"><span class="status-badge status-unpaid">{{ App\Enums\InvoivePatientStatus::getLabel('unpaid')  }}</span></td>
                        <td data-label="Descripcion">No se ha registrado ningún pago</td>

                    </tr>
                    <tr>
                        <td data-label="Badge"><span class="status-badge status-partially-paid">{{ App\Enums\InvoivePatientStatus::getLabel('partial')  }}</span></td>
                        <td data-label="Descripcion">Se ha pagado parte del monto total</td>

                    </tr>
                    <tr>
                        <td data-label="Badge"><span class="status-badge status-paid">{{ App\Enums\InvoivePatientStatus::getLabel('paid')  }}</span></td>
                        <td data-label="Descripcion">Monto total pagado completamente</td>

                    </tr>
                </tbody>
            </table>
        </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Relación entre Estados
            </div>
            <p class="mb-0">Una factura en estado "Issued" puede tener cualquier estado de pago (Unpaid, <!--Partially Paid,--> Paid).<!-- Cuando una factura está completamente pagada, puede cambiar a estado "Balanced". Las facturas canceladas no aceptan pagos.--></p>
        </div>
    </section>

    <!-- Section 6: Tips and Best Practices -->
    <section id="tips" class="content-section">
        <h2><i class="fas fa-lightbulb me-2"></i>6. Tips y Mejores Prácticas</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-check-circle text-success"></i>
                        Buenas Prácticas
                    </div>
                    <ul class="mb-0">
                        <li><strong>Revisar antes de finalizar:</strong> Verifique todos los servicios agregados antes de finalizar la consulta</li>
                        <li><strong>Catálogo actualizado:</strong> Mantenga el catálogo de servicios con precios actualizados</li>
                        <li><strong>Descripciones claras:</strong> Use descripciones detalladas en los servicios</li>
                        <li><strong>Códigos CPT:</strong> Configure códigos CPT para servicios que los requieran</li>
                        <li><strong>Descargar inmediatamente:</strong> Descargue el PDF de la factura al generarla</li>
                        <li><strong>Entregar al paciente:</strong> Proporcione copia de la factura al paciente</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Errores Comunes a Evitar
                    </div>
                    <ul class="mb-0">
                        <li>Finalizar consulta sin revisar servicios agregados</li>
                        <li>No verificar precios antes de agregar servicios</li>
                        <li>Olvidar agregar servicios realizados</li>
                        <!--<li>Duplicar servicios por error</li>-->
                        <li>No descargar la factura para el paciente</li>
                        <li>Intentar modificar servicios después de facturar</li>
                    </ul>
                </div>
            </div>
        </div>

        <h3 class="mt-4">Preguntas Frecuentes</h3>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        ¿Puedo modificar una factura después de generada?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No, las facturas generadas no pueden modificarse directamente. Esto es por razones de auditoría y cumplimiento legal. Si necesita hacer correcciones, debe contactar al administrador del sistema.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ¿Qué hago si olvidé agregar un servicio?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Si ya finalizó la consulta y generó la factura, puede agregar servicios a esa factura. 1) Primero, deberá entrar a la consulta y agregar el servicio que no pudo adjuntar anteriormente, y 2) Hacer clic en <strong>"finalizar consulta"</strong>, el sistema generará automáticamente una nueva factura para el servicio que acaba de ser agregado.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        ¿Cómo aplico descuentos a una factura?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Puede aplicar descuentos modificando el precio unitario del servicio antes de finalizar la consulta. Por ejemplo, si un servicio cuesta $100 y quiere dar 20% de descuento, cambie el precio a $80.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        ¿El paciente recibe la factura automáticamente?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No, la factura no se envía automáticamente por correo. Debe descargar el PDF y entregarlo al paciente en persona, por correo electrónico, o imprimirlo.
                    </div>
                </div>
            </div>

        </div>

        <div class="info-box tip mt-4">
            <div class="info-box-title">
                <i class="fas fa-question-circle text-success"></i>
                ¿Necesita Más Ayuda?
            </div>
            <p class="mb-0">Para información sobre cómo registrar pagos contra facturas, consulte la guía de <a href="{{ route('help.payments') }}">Procesar Pagos</a>. Si tiene preguntas adicionales, contacte al equipo de soporte de SAMI.</p>
        </div>
    </section>

        </div> <!-- End col-lg-8 -->
    </div> <!-- End row -->

    <!-- Navigation Footer -->
    <div class="row mt-5">
        <div class="col-md-6">
            <a href="{{ route('help.consultation') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Anterior: Realizar Consulta
            </a>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('help.payments') }}" class="btn btn-success btn-lg">
                Siguiente: Procesar Pagos<i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
@stop
