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
    background: linear-gradient(180deg, #5e35b1 0%, #7c4dff 100%);
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
    background: linear-gradient(135deg, #5e35b1 0%, #7c4dff 100%);
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

/* Content Section */
.content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.content-section h2 {
    color: #5e35b1;
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ede7f6;
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

/* Step Cards */
.step-card {
    background: linear-gradient(135deg, #faf5ff 0%, #fff 100%);
    border-left: 4px solid #5e35b1;
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
    background: #5e35b1;
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
    color: #5e35b1;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ede7f6;
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
    color: #4527a0;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.toc-list a:hover {
    background: #ede7f6;
    color: #5e35b1;
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
    background: #5e35b1;
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
    background: #faf5ff;
}

.required-badge {
    background: #dc3545;
    color: white;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 5px;
}

.optional-badge {
    background: #6c757d;
    color: white;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 5px;
}

/* Feature cards */
.feature-card {
    background: linear-gradient(135deg, #fff 0%, #faf5ff 100%);
    border: 1px solid #d1c4e9;
    border-radius: 12px;
    padding: 25px;
    height: 100%;
    transition: all 0.3s ease;
}

.feature-card:hover {
    border-color: #7c4dff;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(124, 77, 255, 0.2);
}

.feature-card .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.feature-card h5 {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.feature-card p {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0;
}

/* Day Schedule Cards */
.day-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
}

.day-card.active {
    border-color: #5e35b1;
    background: #faf5ff;
}

.day-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

/* Complexity badges */
.complexity-low { background: #c8e6c9; color: #2e7d32; }
.complexity-medium { background: #fff3e0; color: #ef6c00; }
.complexity-high { background: #ffcdd2; color: #c62828; }

.complexity-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Transfer list visualization */
.transfer-container {
    display: flex;
    gap: 20px;
    align-items: stretch;
}

.transfer-list {
    flex: 1;
    background: #f5f5f5;
    border-radius: 10px;
    padding: 15px;
    min-height: 200px;
}

.transfer-list h6 {
    font-weight: 600;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.transfer-list.available {
    background: #fff3e0;
}

.transfer-list.selected {
    background: #e8f5e9;
}

.transfer-arrows {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
}

.transfer-arrows button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: #5e35b1;
    color: white;
    cursor: pointer;
}

/* Back to top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #522c9f 0%, #5e35b1  100%);
    color: white;
    border-radius: 50%;
    display: flex;
    border: none;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(94, 53, 177, 0.4);
    transition: all 0.3s ease;
    opacity: 0;
    cursor: pointer;
    visibility: hidden;
    z-index: 9999;
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}


.back-to-top:hover {
    background: #4527a0;
    color: white;
    transform: translateY(-3px);
}


@media print {
    .back-to-top {
        display: none;
    }}

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

    .transfer-container {
        flex-direction: column;
    }
}

        @media (max-width: 768px) {
    .help-content {
        padding: 15px;
    }

    .back-to-top {
        right: 15px;
        bottom: 15px;
    }
}


@media (max-width: 480px) {
    .step-card, .info-box{
            border-left: none;
            padding: 25px;
            border-bottom: 5px solid #5e35b1;
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
        .field-table td[data-label="Seccion"],  .field-table td[data-label="Campo"], .field-table td[data-label="Funcionalidad"] {
            background: #5e35b1;
            color: white;
            font-size: 15px;
            padding: 12px;
            border-bottom: 2px solid #552fa1;
        }


        .field-table td[data-label="Seccion"] strong,  .field-table td[data-label="Campo"] strong, .field-table td[data-label="Funcionalidad"] strong {
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
        .field-table td[data-label="Validacion"], .field-table td[data-label="Requerido"], .field-table td[data-label="Opcional"] {
            background: #f5f5f5;
            padding: 12px 15px;
            text-align: center;
            font-weight: 600;
        }

        .field-table td[data-label="Valor"]:before {
            content: "Valor por defecto: ";
            font-weight: 600;
            color: #5e35b1;
            margin-right: 5px;
        }

    }


    @media (max-width: 365px){
        .info-box-title, .content-section h4, .help-header h1 {
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
    @include('help.sidebar', ['active' => 'settings'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('help.settings') }}">Guias de Configuraciones</a></li>
            <li class="breadcrumb-item active">Configuraciones</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-cogs me-3"></i>Guia de Configuraciones</h1>
        <p>Aprende a configurar tu cuenta: servicios, horarios, accesos rapidos y plantillas de consulta.</p>
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
                    <li><a href="#catalogo-servicios"><i class="fas fa-clipboard-list"></i> 2. Catálogo de Servicios</a></li>
                    <li><a href="#firma-sello"><i class="fas fa-pen-fancy"></i> 3. Firma y Sello</a></li>
                    <li><a href="#plantilla-consulta"><i class="fas fa-file-medical"></i> 4. Plantilla de Consulta</a></li>
                    <li><a href="#accesos-rapidos"><i class="fas fa-bolt"></i> 5. Accesos Rápidos</a></li>
                    <li><a href="#horarios-laborales"><i class="fas fa-clock"></i> 6. Horarios Laborales</a></li>
                    <li><a href="#plantilla-factura"><i class="fas fa-file-invoice"></i> 7. Plantilla de Factura</a></li>
                    <li><a href="#plantilla-incapacidad"><i class="fas fa-file-prescription"></i> 8. Plantilla de Incapacidad Médica</a></li>
                    <li><a href="#codigo-referidos"><i class="fas fa-user-plus"></i> 9. Código de Referidos</a></li>
                    <li><a href="#almacenamiento-externo"><i class="fas fa-cloud"></i> 10. Almacenamiento Externo</a></li>
                    <li><a href="#lista-espera"><i class="fas fa-hourglass-half"></i> 11. Lista de Espera</a></li>
                    <li><a href="#tips"><i class="fas fa-lightbulb"></i> 12. Tips y Mejores Prácticas</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-8">

    <!-- Section 1: Introduction -->
    <section id="introduccion" class="content-section">
        <h2><i class="fas fa-info-circle me-2"></i>1. Introduccion al Modulo de Configuraciones</h2>

        <p>El modulo de <strong>Configuraciones</strong> permite personalizar SAMI segun las necesidades especificas de su practica medica. Aqui puede definir sus servicios, horarios de atencion, accesos rapidos y la estructura de sus consultas.</p>

        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-clipboard-list" style="color: #4caf50;"></i>
                    </div>
                    <h5>Catalogo de Servicios</h5>
                    <p>Define los procedimientos y servicios que ofreces</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-sign" style="color: #4caf50;"></i>
                    </div>
                    <h5>Firma y Sello</h5>
                    <p>La firma y sello se utilizarán en las recetas médicas y órdenes médicas generadas</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-clock" style="color: #2196f3;"></i>
                    </div>
                    <h5>Horarios Laborales</h5>
                    <p>Configura tus dias y horas de atencion</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-bolt" style="color: #ff9800;"></i>
                    </div>
                    <h5>Accesos Rapidos</h5>
                    <p>Procedimientos frecuentes a un click</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-file-medical" style="color: #9c27b0;"></i>
                    </div>
                    <h5>Plantilla Consulta</h5>
                    <p>Personaliza las secciones de tus consultas</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-file-invoice" style="color: #9c27b0;"></i>
                    </div>
                    <h5>Plantilla de Factura</h5>
                    <p>Selecciona tu diseño favorito para tus facturas</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-file-prescription" style="color: #9c27b0;"></i>
                    </div>
                    <h5>Plantilla de Incapacidad Médica</h5>
                    <p>Elige una plantilla para las Incapacidades Médicas</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-user-plus" style="color: #4caf50;"></i>
                    </div>
                    <h5>Código de Referidos</h5>
                    <p>Genera y gestiona tus códigos de invitación</p>
                </div>
            </div>
        </div>

        <h3>Acceso al Modulo de Configuraciones</h3>
        <p>Para acceder a las configuraciones:</p>
        <ol>
            <li>Inicie sesion en SAMI</li>
            <li>En el menu lateral izquierdo, busque <strong>"Configuraciones"</strong></li>
            <li>Seleccione la opcion que desea configurar</li>
        </ol>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_index.png') }}" alt="" style="width: 100%;">
        </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-shield-alt text-primary"></i>
                Permisos Requeridos
            </div>
            <p class="mb-0">Cada sección de configuración requiere permisos específicos y pueden variar dependiendo del rol que tenga el usuario. Si tiene dudas sobre el acceso de cada rol, puede consultar el módulo de <a href="{{ route('help.roles') }}">Roles y Permisos</a> en el centro de ayuda.</p>
        </div>
    </section>

    <!-- Section 2: Service Catalog -->
    <section id="catalogo-servicios" class="content-section">
        <h2><i class="fas fa-clipboard-list me-2"></i>2. Catalogo de Servicios</h2>

        <p>El <strong>Catalogo de Servicios</strong> le permite definir todos los procedimientos, consultas y servicios que ofrece en su practica. Estos servicios se utilizan para facturación.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Servicios</strong><br><!--URL: <code>/settings/create_user_procedures</code>--></p>
        </div>

        <h3>Tipos de Servicios</h3>
        <p>Puede crear dos tipos de servicios:</p>

        <div class="row mt-3 mb-4">
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-code me-2 text-primary"></i>Basados en Codigos CPT</h5>
                    <p>Servicios vinculados a codigos CPT estandarizados. Ideal para procedimientos medicos reconocidos internacionalmente.</p>
                    <ul class="mb-0 mt-2">
                        <li>Busqueda por codigo o nombre</li>
                        <li>Codigos estandarizados</li>
                        <li>Compatibilidad con aseguradoras</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-edit me-2 text-warning"></i>Servicios Personalizados</h5>
                    <p>Servicios creados manualmente sin vinculacion a codigos CPT. Util para servicios especificos de su practica.</p>
                    <ul class="mb-0 mt-2">
                        <li>Nombre y descripcion libres</li>
                        <li>Flexibilidad total</li>
                        <li>Para servicios unicos</li>
                    </ul>
                </div>
            </div>
        </div>

        <h3>Crear un Servicio Basado en CPT</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Buscar Codigo CPT</span></h4>
            <p>En la seccion "Agregar Servicio desde CPT", utilice el buscador para encontrar el codigo:</p>
            <ul>
                <li>Escriba el codigo CPT (ej: 99213) o escriba parte del nombre del procedimiento</li>
                <li>Seleccione de la lista desplegable</li>
            </ul>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_cpt.png') }}" alt="" style="width: 100%;">
        </div>

        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Completar Informacion del Servicio</span></h4>
            <p>Una vez seleccionado el codigo CPT, complete los campos adicionales:</p>
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
                        <!--<tr>
                            <td data-label="Campo"><strong>Codigo CPT</strong></td>
                            <td data-label="Descripcion">Codigo seleccionado del buscador</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>-->
                        <tr>
                            <td data-label="Campo"><strong>Precio</strong></td>
                            <td data-label="Descripcion">Precio base del servicio en la moneda configurada</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Complejidad</strong></td>
                            <td data-label="Descripcion">Nivel de complejidad: Baja, Media o Alta</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>
                        <!--<tr>
                            <td data-label="Campo"><strong>Especialidad</strong></td>
                            <td data-label="Descripcion">Especialidad medica asociada</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Duracion (min)</strong></td>
                            <td data-label="Descripcion">Tiempo estimado del procedimiento</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Copago</strong></td>
                            <td data-label="Descripcion">Monto de copago para pacientes con seguro</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Requiere Autorizacion</strong></td>
                            <td data-label="Descripcion">Indica si requiere autorizacion previa del seguro</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Cubierto por Seguro</strong></td>
                            <td data-label="Descripcion">Indica si el servicio esta cubierto por seguros</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>-->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Guardar el Servicio</span></h4>
            <p>Haga click en <strong>"Registrar"</strong> para guardar. El sistema:</p>
            <ul>
                <li>Genera automaticamente un codigo de servicio (ej: PROC_0001)</li>
                <li>Vincula el servicio a su cuenta</li>
                <li>Lo hace disponible para servicios facturables</li>
            </ul>
        </div>

        <h3>Crear un Servicio Personalizado</h3>


        <p>Para servicios que no tienen codigo CPT:</p>
        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Completar Informacion del Servicio</span></h4>
            <p>Complete los campos adicionales:</p>
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
                            <td data-label="Campo"><strong>Nombre de Servicio</strong></td>
                            <td data-label="Descripcion">Nombre descriptivo del servicio (max 255 caracteres)</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Descripcion</strong></td>
                            <td data-label="Descripcion">Texto para detallar el servicio</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Tipo de Servicio</strong></td>
                            <td data-label="Descripcion">Categoria del servicio</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Precio Base</strong></td>
                            <td data-label="Descripcion">Precio base del servicio en la moneda configurada</td>
                            <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                        </tr>
                        <!--<tr>
                            <td data-label="Campo"><strong>Especialidad</strong></td>
                            <td data-label="Descripcion">Especialidad medica asociada</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Duracion (min)</strong></td>
                            <td data-label="Descripcion">Tiempo estimado del procedimiento</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Copago</strong></td>
                            <td data-label="Descripcion">Monto de copago para pacientes con seguro</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Requiere Autorizacion</strong></td>
                            <td data-label="Descripcion">Indica si requiere autorizacion previa del seguro</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Cubierto por Seguro</strong></td>
                            <td data-label="Descripcion">Indica si el servicio esta cubierto por seguros</td>
                            <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                        </tr>-->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Guardar el Servicio</span></h4>
            <p>Haga click en <strong>"Registrar"</strong> para guardar. El sistema:</p>
            <ul>
                <li>Genera automaticamente un codigo de servicio (ej: PROC_0001)</li>
                <li>Vincula el servicio a su cuenta</li>
                <li>Lo hace disponible para servicios facturables</li>
            </ul>
        </div>
        <!--<div class="field-table-wrapper">
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
                        <td data-label="Campo"><strong>Nombre</strong></td>
                        <td data-label="Descripcion">Nombre descriptivo del servicio (max 255 caracteres)</td>
                        <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td data-label="Campo"><strong>Descripcion</strong></td>
                        <td data-label="Descripcion">Descripcion detallada del servicio</td>
                        <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                    </tr>
                    <tr>
                        <td data-label="Campo"><strong>Tipo de Servicio</strong></td>
                        <td data-label="Descripcion">Categoria del servicio</td>
                        <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td data-label="Campo"><strong>Precio</strong></td>
                        <td data-label="Descripcion">Precio base del servicio</td>
                        <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td data-label="Campo"><strong>Complejidad</strong></td>
                        <td data-label="Descripcion">Nivel: Baja, Media o Alta</td>
                        <td data-label="Requerido"><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td data-label="Campo"><strong>Codigo de Ingreso</strong></td>
                        <td data-label="Descripcion">Codigo contable para facturacion</td>
                        <td data-label="Opcional"><span class="optional-badge">Opcional</span></td>
                    </tr>
                </tbody>
            </table>
        </div>-->
        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Código de Servicios
            </div>
            <p class="mb-0">Cada Servicio, tanto el servicio personalizado como los servicios con CPT generan un código de forma automática, este código tiene como función identificar el servicio.</p>
        </div>

        <h4>Tipos de Servicios Disponibles</h4>
        <div class="row">
            <div class="col-md-4">
                <ul>
                    <li><strong>Consulta</strong> - Consultas medicas</li>
                    <li><strong>Procedimiento</strong> - Procedimientos menores</li>
                    <!--<li><strong>Diagnostico</strong> - Estudios diagnosticos</li>-->
                </ul>
            </div>
            <div class="col-md-4">
                <ul>
                    <li><strong>Terapeutico</strong> - Tratamientos</li>
                    <li><strong>Quirurgico</strong> - Procedimientos quirurgicos</li>
                    <li><strong>Laboratorio</strong> - Examenes de laboratorio</li>
                </ul>
            </div>
            <div class="col-md-4">
                <ul>
                    <li><strong>Imagenologia</strong> - Rayos X, US, etc.</li>
                    <li><strong>Suministros</strong> - Materiales y suministros</li>
                    <li><strong>Otro</strong> - Servicios varios</li>
                </ul>
            </div>
        </div>

        <!--<h4>Niveles de Complejidad</h4>
        <p>
            <span class="complexity-badge complexity-low">Baja</span>
            <span class="complexity-badge complexity-medium">Media</span>
            <span class="complexity-badge complexity-high">Alta</span>
        </p>-->

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_catalog.png') }}" alt="" style="width: 100%;">
        </div>


        <h3>Búsqueda de Servicios</h3>
        <p>El catálogo cuenta con una potente barra de búsqueda que le permite localizar servicios rápidamente por diferentes criterios:</p>
        <ul>
            <li><strong>Nombre del Servicio:</strong> Escriba parte del nombre (ej: "Sutura") para filtrar los resultados.</li>
            <li><strong>Código CPT:</strong> Ingrese el código numérico estándar (ej: "99213").</li>
            <!--<li><strong>Código de Servicio:</strong> Busque por el código interno generado por SAMI (ej: "PROC_0001").</li>-->
        </ul>
        <div>
            <img src="{{ asset('images/tutorial/settings/setting_servicesearch.png') }}" alt="" style="width: 100%;">
        </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Elementos del Catálogo de Servicios
            </div>
            <p class="mb-0">En la lista de catálogo de servicios se incluyen todos los servicios creados, tanto los que se agregan con CPTs como los personalizados.</p>
        </div>
        <h3>Gestionar Servicios Existentes (Columna Acciones)</h3>
        <p>En el listado de servicios, la última columna le permite realizar las siguientes acciones:</p>

        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Botón</th>
                        <th>Acción</th>
                        <th>Funcionalidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Botón" class="text-center"><i class="fas fa-edit text-primary"></i></td>
                        <td data-label="Acción"><strong>Editar</strong></td>
                        <td data-label="Funcionalidad">Abre un formulario para modificar el precio o nombre (si es personalizado). Los cambios se aplican a futuras facturaciones.</td>
                    </tr>
                    <tr>
                        <td data-label="Botón" class="text-center"><i class="fas fa fa-pause"></i></td>
                        <td data-label="Acción"><strong>Activar/Desactivar</strong></td>
                        <td data-label="Funcionalidad">Cambia la visibilidad del servicio. Los servicios desactivados no aparecerán como opciones al crear facturas, pero se mantienen en el sistema para registros históricos.</td>
                    </tr>
                    <tr>
                        <td data-label="Botón" class="text-center"><i class="fas fa-trash text-danger"></i></td>
                        <td data-label="Acción"><strong>Eliminar</strong></td>
                        <td data-label="Funcionalidad">Antes de eliminar, el usuario debe confirmar si desea eliminar el servicio, luego de confirmar, se realiza un borrado lógico del servicio. El servicio dejará de ser accesible en el catálogo, aunque la información se conserva internamente para la integridad de sus registros pasados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_actionsbtn.png') }}" alt="" style="width: 100%;">
        </div>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Importante
            </div>
            <p class="mb-0">Los servicios eliminados no se borran permanentemente (eliminacion suave). El historial de facturacion y ordenes previas se mantiene intacto.</p>
        </div>
    </section>

    <!-- Section 3: Signature and Seal -->
    <section id="firma-sello" class="content-section">
        <h2><i class="fas fa-pen-fancy me-2"></i>3. Firma y Sello</h2>

        <p>Configure su <strong>firma y sello digital</strong> que se utilizarán automáticamente en los documentos médicos generados por SAMI, como recetas médicas, órdenes médicas, incapacidades y otros documentos oficiales.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Firma y Sello</strong></p>
        </div>

        <h3>¿Por qué es Importante la Firma y Sello?</h3>
        <div class="row mt-3 mb-4">
            <div class="col-md-6">
                <div class="info-box note">
                    <div class="info-box-title">
                        <i class="fas fa-shield-alt text-primary"></i>
                        Validez Oficial
                    </div>
                    <p class="mb-0">Su firma y sello digital validan todos los documentos médicos generados, dándoles autenticidad y legalidad ante instituciones de salud, aseguradoras y autoridades sanitarias.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-check-circle text-success"></i>
                        Automatización
                    </div>
                    <p class="mb-0">Una vez configurados, se aplicarán automáticamente en todos los documentos que genere, sin necesidad de agregarlos manualmente cada vez.</p>
                </div>
            </div>
        </div>

        <h3>Cargar Firma Digital</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a la Sección de Firma y Sello</span></h4>
            <p>Navegue a <strong>Configuraciones → Firma y Sello</strong>. Verá dos áreas separadas: una para la firma y otra para el sello.</p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Preparar la Imagen</h4>
            <p>Antes de cargar, asegúrese de que su firma/sello cumpla con estos requisitos:</p>
            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Requisito</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Requisito"><strong>Formato de Archivo</strong></td>
                            <td data-label="Detalles">JPEG, PNG, JPG, GIF o SVG</td>
                        </tr>
                        <tr>
                            <td data-label="Requisito"><strong>Tamaño Máximo</strong></td>
                            <td data-label="Detalles">2 MB (2048 KB)</td>
                        </tr>
                        <tr>
                            <td data-label="Requisito"><strong>Fondo Recomendado</strong></td>
                            <td data-label="Detalles">Fondo transparente para mejor integración en documentos</td>
                        </tr>
                        <tr>
                            <td data-label="Requisito"><strong>Resolución</strong></td>
                            <td data-label="Detalles">Mínimo 150x50 píxeles (recomendado 300x100)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb text-primary"></i>
                    Consejo: Preparar Firmas Digitales
                </div>
                <p class="mb-0">Si tiene una firma física, puede digitalizarla usando un escáner o aplicación de escaneo móvil. Luego, use un editor de imágenes (como Photoshop, GIMP o Paint.NET) para recortar el fondo transparente y guardar como PNG.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Cargar la Firma</span></h4>
            <p>En la sección de <strong>"Firma"</strong>:</p>
            <ol>
                <li>Haga clic en el área de carga o use el botón <strong>"Seleccionar Archivo"</strong></li>
                <li>Elija el archivo de imagen de su firma desde su computadora</li>
                <li>El sistema validará el formato y tamaño automáticamente</li>
                <li>Si es válido, verá una vista previa de su firma</li>
                <li>Haga clic en <strong>"Guardar"</strong> o <strong>"Actualizar"</strong> para confirmar</li>
            </ol>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Cargar el Sello</span></h4>
            <p>Repita el mismo proceso en la sección de <strong>"Sello"</strong>:</p>
            <ol>
                <li>Haga clic en el área de carga del sello</li>
                <li>Seleccione la imagen de su sello profesional</li>
                <li>Valide el formato y tamaño</li>
                <li>Confirme la carga</li>
            </ol>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Tanto Firma como Sello
                </div>
                <p class="mb-0">Puede cargar ambos archivos independientemente. Si solo desea usar uno de ellos, simplemente cargue la firma o el sello sin completar el otro.</p>
            </div>
        </div>

        <h3>Gestionar Firma y Sello Existentes</h3>

        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>Cuándo Usar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Acción"><strong>Ver Previsualizacion</strong></td>
                        <td data-label="Descripcion">Muestra una vista previa de la firma/sello cargado actual</td>
                        <td data-label="Cuando usar">Para verificar que se vea correctamente antes de usar en documentos</td>
                    </tr>
                    <tr>
                        <td data-label="Acción"><strong>Actualizar</strong></td>
                        <td data-label="Descripcion">Reemplaza la firma/sello actual por una nueva imagen</td>
                        <td data-label="Cuando usar">Cuando desee cambiar su firma o sello</td>
                    </tr>
                    <tr>
                        <td data-label="Acción"><strong>Eliminar</strong></td>
                        <td data-label="Descripcion">Elimina la firma/sello cargado (requiere confirmación)</td>
                        <td data-label="Cuando usar">Si ya no desea que se agreguen en los documentos</td>
                    </tr>
                    <tr>
                        <td data-label="Acción"><strong>Descargar</strong></td>
                        <td data-label="Descripcion">Descarga el archivo de su firma/sello</td>
                        <td data-label="Cuando usar">Para hacer respaldo o usar en otras aplicaciones</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Dónde se Utilizan</h3>
        <p>Una vez configurados, su firma y sello aparecerán automáticamente en:</p>
        <ul>
            <li><strong>Recetas Médicas:</strong> Firma al pie del documento</li>
            <li><strong>Órdenes Médicas:</strong> Firma de autorización</li>
            <li><strong>Incapacidades Médicas:</strong> Firma del médico responsable</li>
            <li><strong>Certificados Médicos:</strong> Firma de validación</li>
            <li><strong>Documentos Oficiales:</strong> Cualquier documento generado por el sistema que requiera validación profesional</li>
        </ul>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Importante
            </div>
            <ul class="mb-0">
                <li>La firma/sello digital es solo para propósitos de identificación visual, no es una firma criptográfica legalmente vinculante</li>
                <li>Asegúrese de que su imagen sea clara y profesional</li>
                <li>Los cambios se aplican inmediatamente a los nuevos documentos generados</li>
                <li>Los documentos anteriores mantendrán la firma/sello que tenían al momento de su generación</li>
            </ul>
        </div>
    </section>

    <!-- Section 4: Consultation Template -->
    <section id="plantilla-consulta" class="content-section">
        <h2><i class="fas fa-file-medical me-2"></i>4. Plantilla de Consulta</h2>

        <p>La <strong>Plantilla de Consulta</strong> le permite personalizar que secciones aparecen durante sus consultas medicas y en que orden. Esto agiliza su flujo de trabajo mostrando solo las secciones relevantes para su especialidad.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Plantilla Consulta</strong><br><!--URL: <code>/settings/create_consultation_template</code>--></p>
        </div>

        <h3>Secciones Disponibles</h3>
        <p>SAMI ofrece multiples secciones que puede incluir en sus consultas:</p>

        <div class="row">
            <div class="col-md-12">
                <div class="field-table-wrapper">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Seccion</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                                <td data-label="Seccion"><strong>Servicios Facturables</strong></td>
                                <td data-label="Descripcion">Servicios asociados a CPTs, Servicios personalizados, etc.</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Motivo de Consulta</strong></td>
                                <td data-label="Descripcion">Descripción detallada de la condición/movito de visita del paciente.</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Signos Vitales</strong></td>
                                <td data-label="Descripcion">Presion arterial, frecuencia cardiaca, temperatura, etc.</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Enfermedad Actual</strong></td>
                                <td data-label="Descripcion">Descripcion de la enfermedad presente</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Examen Fisico</strong></td>
                                <td data-label="Descripcion">Hallazgos del examen fisico por sistemas</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Diagnósticos</strong></td>
                                <td data-label="Descripcion">Diagnosticos y condiciones del paciente</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Ordenes Medicas</strong></td>
                                <td data-label="Descripcion">Laboratorios, imagenes y procedimientos</td>
                            </tr>
                             <tr>
                                <td data-label="Seccion"><strong>Referencia Especialista</strong></td>
                                <td data-label="Descripcion">Referir a Médico Especializado</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Medicamentos</strong></td>
                                <td data-label="Descripcion">Medicamentos recetados</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Nota General</strong></td>
                                <td data-label="Descripcion">Nota General de la Consulta</td>
                            </tr>
                            <!--<tr>
                                <td data-label="Seccion"><strong>Incapacidades Médicas</strong></td>
                                <td data-label="Descripcion">Incapacidades y justificantes</td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>

            </div>
            <!--<div class="col-md-6">
                <div class="field-table-wrapper">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Seccion</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Seccion"><strong>Antecedentes</strong></td>
                                <td data-label="Descripcion">Historial medico del paciente</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Notas Medicas</strong></td>
                                <td data-label="Descripcion">Notas clinicas generales</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Notas Personales</strong></td>
                                <td data-label="Descripcion">Notas privadas del medico</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Licencias Medicas</strong></td>
                                <td data-label="Descripcion">Incapacidades y justificantes</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Plan</strong></td>
                                <td data-label="Descripcion">Plan de tratamiento y seguimiento</td>
                            </tr>
                            <tr>
                                <td data-label="Seccion"><strong>Especialidad</strong></td>
                                <td data-label="Descripcion">Secciones especificas de su especialidad</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>-->
        </div>

        <h3>Configurar la Plantilla</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Entender la Interfaz de Transferencia</span></h4>
            <p>La configuracion usa una interfaz de doble lista:</p>

            <div class="transfer-container my-4">
                <div class="transfer-list available">
                    <h6><i class="fas fa-list me-2"></i>Secciones Disponibles</h6>
                    <p class="text-muted small">Secciones que puede agregar a su plantilla</p>
                    <div class="border rounded p-2 mb-2 bg-white">Seccion A</div>
                    <div class="border rounded p-2 mb-2 bg-white">Seccion B</div>
                    <div class="border rounded p-2 bg-white">Seccion C</div>
                </div>
                <div class="transfer-arrows">
                    <button title="Agregar"><i class="fas fa-arrow-right"></i></button>
                    <button title="Quitar"><i class="fas fa-arrow-left"></i></button>
                </div>
                <div class="transfer-list selected">
                    <h6><i class="fas fa-check-circle me-2"></i>Secciones Seleccionadas</h6>
                    <p class="text-muted small">Secciones que apareceran en sus consultas</p>
                    <div class="border rounded p-2 mb-2 bg-white">Signos Vitales</div>
                    <div class="border rounded p-2 mb-2 bg-white">Examen Fisico</div>
                    <div class="border rounded p-2 bg-white">Prescripciones</div>
                </div>
            </div>
            <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-user-md text-primary"></i>
                Primera Configuracion
            </div>
            <p class="mb-0">Si es la primera vez que accede, el sistema cargara automaticamente todas las secciones generales como base. Puede personalizar desde ahi.</p>
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Agregar Secciones</span></h4>
            <p>Para agregar una seccion a su plantilla:</p>
            <ol>
                <li>Busque la seccion deseada en la lista <strong>"Secciones Disponibles"</strong></li>
                <li>Haga clic en la flecha de la sección para moverla a la lista de seleccionadas.</li>
                <li>La seccion aparecera inmediatamente en <strong>"Secciones Seleccionadas"</strong></li>
            </ol>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_template.png') }}" alt="" style="width: 100%;">
        </div>
        <!--<div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Secciones Disponibles
            </div>
            <p class="mb-0">En la mayoría de los casos, por defecto, salen todas las secciones de la consulta en <strong>"Secciones Seleccionadas"</strong>, en los casos de especialidades que poseen secciones orientadas a su especialidad, por ejemplo, Dermatología la sección especializada aparecerá del lado de las <strong>"Secciones Disponibles"</strong>.</p>
        </div>-->
        </div>

        <!--<div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Ordenar Secciones</span></h4>
            <p>Puede cambiar el orden de las secciones arrastrando:</p>
            <ol>
                <li>En la lista de <strong>"Secciones Seleccionadas"</strong></li>
                <li>Haga click sostenido en una seccion</li>
                <li>Arrastre hacia arriba o abajo para cambiar su posicion</li>
                <li>Suelte para confirmar el nuevo orden</li>
            </ol>
            <p>El orden define como aparecen las secciones durante la consulta.</p>
        </div>-->

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Quitar Secciones</span></h4>
            <p>Para remover una seccion de su plantilla:</p>
            <ol>
                <li>Ubique la seccion en <strong>"Secciones Seleccionadas"</strong></li>
                <li>Haga click en el boton para moverla de vuelta a disponibles</li>
            </ol>

            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-lock text-danger"></i>
                    Secciones Obligatorias
                </div>
                <p class="mb-0">Algunas secciones son <strong>obligatorias</strong> y no pueden ser removidas. Son necesarias para el funcionamiento correcto del sistema y apareceran siempre en sus consultas. Estas secciones son: <strong>Motivo de Consulta, Enfermedad Actual, Diagnósticos, Laboratorios e Imágenes.</strong></p>
            </div>
        </div>

        <h3>Secciones por Especialidad</h3>
        <p>Dependiendo de su especialidad medica, vera secciones adicionales especificas:</p>
        <ul>
            <li><strong>Dermatologia:</strong> Secciones para lesiones de piel, fotografia clinica</li>
            <li><strong>Urología:</strong> índice Internacional de Síntomas Prostáticos</li>
            <!--<li><strong>Ginecologia:</strong> Historia ginecologica, examenes especializados</li>
            <li><strong>Pediatria:</strong> Curvas de crecimiento, desarrollo</li>
            <li><strong>Cardiologia:</strong> Electrocardiograma, evaluacion cardiovascular</li>
            <li>Y mas segun su especialidad...</li>-->
        </ul>


        <div>
            <img src="{{ asset('images/tutorial/settings/setting_templderm.png') }}" alt="" style="width: 100%;">
        </div>
        <div>
            <img src="{{ asset('images/tutorial/settings/setting_enderm.png') }}" alt="" style="width: 100%;">
        </div>
    </section>

  <!-- Section 5: Rapid Access -->
    <section id="accesos-rapidos" class="content-section">
        <h2><i class="fas fa-bolt me-2"></i>5. Accesos Rapidos</h2>

        <p>Los <strong>Accesos Rapidos</strong> le permiten tener a la mano los procedimientos, laboratorios e imagenes que solicita con mayor frecuencia. Durante las consultas, puede agregar estos items con un solo click.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Accesos Rapidos</strong><br><!--URL: <code>/settings/create_rapid_access</code>--></p>
        </div>

        <h3>Categorias de Acceso Rapido</h3>
        <p>Los accesos rapidos se organizan en tres categorias:</p>

        <div class="row mt-4 mb-4">
            <div class="col-md-4 mb-3">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-flask" style="color: #4caf50;"></i>
                    </div>
                    <h5>Laboratorios</h5>
                    <p>Examenes de sangre, orina, y otros estudios de laboratorio clinico</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-x-ray" style="color: #2196f3;"></i>
                    </div>
                    <h5>Imagenologia</h5>
                    <p>Rayos X, ultrasonidos, tomografias, resonancias y otros estudios de imagen</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-procedures" style="color: #ff9800;"></i>
                    </div>
                    <h5>Procedimientos</h5>
                    <p>Procedimientos medicos, intervenciones y tratamientos especiales</p>
                </div>
            </div>
        </div>

        <h3>Configurar Accesos Rapidos</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Seleccionar Categoria</span></h4>
            <p>En la pagina de configuracion, encontrara tres secciones separadas para cada tipo de acceso rapido.</p>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_raccess.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Buscar y Agregar Items</span></h4>
            <p>En cada seccion:</p>
            <ol>
                <li>Utilice el buscador para encontrar el codigo CPT deseado (La búsqueda puede hacerse por nombre o código del CPT)</li>
                <li>Escriba al menos 2 caracteres para iniciar la busqueda</li>
                <li>Seleccione el item de la lista desplegable</li>
                <li>El item se agregara automaticamente a su lista de accesos rapidos</li>
            </ol>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_racpt.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Gestionar Items</span></h4>
            <p>Una vez agregados los items:</p>
            <ul>
                <li>Vera la lista de items seleccionados al lado del buscador</li>
                <li>Use el boton <i class="fas fa-trash text-danger"></i> para eliminar items que ya no necesite</li>
                <li>Los cambios se guardan automaticamente</li>
            </ul>
            <div>
                <img src="{{ asset('images/tutorial/settings/setting_raccessfull.png') }}" alt="" style="width: 100%;">
            </div>
        </div>

        <h3>Uso Durante la Consulta</h3>
        <p>Durante una consulta medica, los accesos rapidos aparecen en un panel lateral:</p>
        <ol>
            <li>Abra el panel de <strong>"Lista de Accesos Rápidos"</strong> en la consulta</li>
            <li>Vera sus items configurados organizados por categoria</li>
            <li>Haga click en cualquier item para agregarlo a la orden actual</li>
            <li>Los items ya agregados se marcan visualmente</li>
        </ol>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_raencounter.png') }}" alt="" style="width: 100%;">
        </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-layer-group text-primary"></i>
                Jerarquia de Accesos
            </div>
            <p class="mb-0">Si no ha configurado accesos rapidos personales, el sistema mostrara los accesos predeterminados de la organizacion (CLIENT) o del sistema (MASTER).</p>
        </div>
    </section>


    <!-- Section 6: Working Hours -->
    <section id="horarios-laborales" class="content-section">
        <h2><i class="fas fa-clock me-2"></i>6. Horarios Laborales</h2>

        <p>Configure sus <strong>horarios de trabajo</strong> para cada dia de la semana. Estos horarios se utilizan para el sistema de agendamiento de citas y para mostrar su disponibilidad a los pacientes.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Horario Laboral</strong><br><!--URL: <code>/settings/create_working_hour_user</code>--></p>
        </div>

        <h3>Estructura de Horarios</h3>
        <p>Cada horario de trabajo esta vinculado a:</p>
        <ul>
            <li><strong>Sucursal:</strong> La ubicacion fisica donde atendera</li>
            <li><strong>Consultorio:</strong> El consultorio especifico dentro de esa sucursal</li>
            <li><strong>Dia de la semana:</strong> De lunes a domingo</li>
            <li><strong>Rango de horas:</strong> Hora de entrada y hora de salida</li>
        </ul>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-primary"></i>
                Multiples Turnos
            </div>
            <p class="mb-0">Puede configurar multiples turnos por dia. Por ejemplo, si atiende en la mañana en una sucursal y en la tarde en otra, agregue dos horarios para el mismo dia.</p>
        </div>

        <h3>Configurar Horarios</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a la Configuracion</span></h4>
            <p>Navegue a <strong>Configuraciones → Horario Laboral</strong>. Vera una tabla con los 7 dias de la semana.</p>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_schedule.png') }}" alt="" style="width: 100%;">
        </div>

        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Agregar Horario por Dia</span></h4>
            <p>Para cada dia que desee trabajar:</p>
            <ol>
                <li>Marque la casilla de <strong>"Habilitar"</strong> para activar el dia</li>
                <li>Seleccione la <strong>Sucursal</strong> donde atendera</li>
                <li>Seleccione el <strong>Consultorio</strong> (se filtra segun la sucursal)</li>
                <li>Defina la <strong>Hora de Entrada</strong> (formato HH:MM)</li>
                <li>Defina la <strong>Hora de Salida</strong> (debe ser posterior al inicio)</li>
            </ol>
            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripcion</th>
                            <th>Validacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Habilitar</strong></td>
                            <td data-label="Descripcion">Activa el horario para este dia</td>
                            <td data-label="Validacion">Checkbox</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Sucursal</strong></td>
                            <td data-label="Descripcion">Ubicacion de atencion</td>
                            <td data-label="Validacion">Requerido si habilitado</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Consultorio</strong></td>
                            <td data-label="Descripcion">Consultorio dentro de la sucursal</td>
                            <td data-label="Validacion">Requerido si habilitado</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Hora Inicio</strong></td>
                            <td data-label="Descripcion">Hora de inicio de atencion</td>
                            <td data-label="Validacion">Formato HH:MM, default 08:00</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Hora Fin</strong></td>
                            <td data-label="Descripcion">Hora de fin de atencion</td>
                            <td data-label="Validacion">Debe ser mayor que inicio, default 18:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Agregar Turnos Adicionales</span></h4>
            <p>Si necesita agregar otro turno el mismo dia (ej: manana y tarde en diferentes lugares):</p>
            <ol>
                <li>Haga click en <strong>"Agregar Horario"</strong> en el dia correspondiente</li>
                <li>Se agregara una nueva fila para ese dia</li>
                <li>Configure el nuevo turno con diferente sucursal/consultorio/horario</li>
            </ol>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_schdule.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Guardar Configuracion</span></h4>
            <p>Una vez configurados todos los dias:</p>
            <ol>
                <li>Revise el resumen de horarios configurados</li>
                <li>Los dias con horarios habilitados mostraran una insignia <span class="badge bg-success">Configurado</span></li>
                <li>Haga clic en <strong>"Registrar"</strong> para aplicar los cambios</li>
            </ol>
        </div>

        <h3>Ejemplo de Configuracion</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="day-card active">
                    <div class="day-name"><i class="fas fa-check-circle text-success me-2"></i>Lunes</div>
                    <small class="text-muted">Sucursal Centro - Consultorio 1</small><br>
                    <strong>08:00 - 12:00</strong>
                    <hr class="my-2">
                    <small class="text-muted">Sucursal Norte - Consultorio 3</small><br>
                    <strong>14:00 - 18:00</strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="day-card">
                    <div class="day-name"><i class="fas fa-times-circle text-secondary me-2"></i>Domingo</div>
                    <small class="text-muted">No configurado - Dia de descanso</small>
                </div>
            </div>
        </div>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Importante
            </div>
            <ul class="mb-0">
                <li>Los horarios afectan directamente la disponibilidad en el calendario de citas</li>
                <li>Si cambia los horarios, las citas existentes <strong>no</strong> se modifican automaticamente</li>
                <li>Asegurese de tener sucursales y consultorios creados antes de configurar horarios</li>
            </ul>
        </div>
    </section>

    <!-- Section 7: Invoice Template -->
    <section id="plantilla-factura" class="content-section">
        <h2><i class="fas fa-file-invoice me-2"></i>7. Plantilla de Factura</h2>
        <p>SAMI le permite elegir entre diferentes diseños de facturas para que se adapten a la imagen de su práctica. Puede previsualizar y seleccionar el modelo que prefiera.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Plantilla de Factura</strong></p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Explorar Modelos</span></h4>
            <p>En el listado, verá los diferentes diseños disponibles (Ej: Modelo Moderno, Clásico, Minimalista). Cada uno muestra una vista previa miniatura.</p>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_ra1.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Previsualizar y Seleccionar</span></h4>
            <p>Haga clic en el botón de <strong>"Vista Previa Completa"</strong> para ver el diseño en tamaño completo con datos de ejemplo. Si le convence, haga clic en <strong>"Seleccionar"</strong><!-- o <strong>"Activar"</strong>-->.</p>

        <div>
            <img src="{{ asset('images/tutorial/settings/setting_ra2.png') }}" alt="" style="width: 100%;">
        </div>
        </div>
    </section>

    <!-- Section 8: Medical Disability Template -->
    <section id="plantilla-incapacidad" class="content-section">
        <h2><i class="fas fa-file-prescription me-2"></i>8. Plantilla de Incapacidad Médica</h2>
        <p>Personalice el formato de los documentos de incapacidad médica y justificantes que entrega a sus pacientes.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Plantilla de Incapacidad Médica</strong></p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Selección de Formato</span></h4>
            <p>Al igual que con las facturas, puede elegir entre varios esquemas visuales para las incapacidades. Asegúrese de que el diseño incluya los campos necesarios para su jurisdicción legal.</p>
        <div>
            <img src="{{ asset('images/tutorial/settings/setting_medleave.png') }}" alt="" style="width: 100%;">
        </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Previsualizar y Seleccionar</span></h4>
            <p>Haga clic en el botón de <strong>"Vista Previa Completa"</strong> para ver el diseño en tamaño completo con datos de ejemplo. Si le convence, haga clic en <strong>"Seleccionar"</strong>.</p>
            <div>
                <img src="{{ asset('images/tutorial/settings/setting_ra3.png') }}" alt="" style="width: 100%;">
            </div>
        </div>
    </section>

    <!-- Section 9: Referral Code -->
    <section id="codigo-referidos" class="content-section">
        <h2><i class="fas fa-user-plus me-2"></i>9. Código de Referidos</h2>
        <p>El sistema de referidos le permite invitar a otros colegas a unirse a SAMI y gestionar los beneficios asociados a sus invitaciones.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Código de Referidos</strong></p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Obtener su Código</span></h4>
            <p>En esta sección encontrará su código alfanumérico único. Puede copiarlo y compartirlo directamente por correo electrónico o redes sociales, además, puede descargarlo como PDF con QR incluído.</p>
            <div>
                <img src="{{ asset('images/tutorial/settings/setting_referralcode.png') }}" alt="" style="width: 100%;">
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Seguimiento de Invitaciones</span></h4>
            <p>Podrá visualizar el historial de personas que se han registrado utilizando su código.</p>
            <div>
                <img src="{{ asset('images/tutorial/settings/setting_referralcode2.png') }}" alt="" style="width: 100%;">
            </div>
        </div>
    </section>

    <!-- Section 10: External Storage -->
    <section id="almacenamiento-externo" class="content-section">
        <h2><i class="fas fa-cloud me-2"></i>10. Almacenamiento Externo</h2>

        <p>Configure servicios de <strong>almacenamiento externo en la nube</strong> para guardar automáticamente documentos, imágenes clínicas y archivos de pacientes. SAMI soporta integración con Dropbox para sincronización segura y acceso remoto a sus archivos.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Almacenamiento Externo</strong></p>
        </div>

        <h3>Ventajas del Almacenamiento Externo</h3>
        <div class="row mt-3 mb-4">
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-lock me-2 text-success"></i>Seguridad</h5>
                    <p>Datos encriptados en servidores seguros con cumplimiento de estándares HIPAA</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-globe me-2 text-info"></i>Acceso Remoto</h5>
                    <p>Acceda a sus archivos desde cualquier dispositivo, en cualquier momento</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-sync me-2 text-warning"></i>Sincronización</h5>
                    <p>Sincronización automática de documentos nuevos</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card">
                    <h5><i class="fas fa-history me-2 text-primary"></i>Respaldos</h5>
                    <p>Protección contra pérdida de datos con respaldos automáticos</p>
                </div>
            </div>
        </div>

        <h3>Configurar Dropbox</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a Almacenamiento Externo</span></h4>
            <p>Navegue a <strong>Configuraciones → Almacenamiento Externo</strong>. Verá las opciones disponibles y el estado de conexión actual.</p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Conectar con Dropbox</span></h4>
            <p>Para autorizar la conexión con Dropbox:</p>
            <ol>
                <li>Haga clic en el botón <strong>"Conectar con Dropbox"</strong></li>
                <li>Se abrirá una ventana de autorización de Dropbox</li>
                <li>Ingrese sus credenciales de Dropbox (correo y contraseña)</li>
                <li>Apruebe el acceso para permitir que SAMI acceda a su cuenta</li>
                <li>Será redirigido de vuelta a SAMI con la conexión establecida</li>
            </ol>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Primera Conexión
                </div>
                <p class="mb-0">Si no tiene cuenta de Dropbox, puede crear una gratis en www.dropbox.com. El almacenamiento gratuito incluye 2 GB, perfecto para comenzar.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Habilitar Almacenamiento Externo</span></h4>
            <p>Una vez conectado:</p>
            <ol>
                <li>Verá información de su cuenta Dropbox (email, espacio disponible)</li>
                <li>Marque la casilla <strong>"Habilitar Almacenamiento Externo"</strong> para activar la sincronización</li>
                <li>Aparecerá un estado <span class="badge bg-success">Conectado</span></li>
                <li>Los archivos comenzarán a sincronizarse automáticamente</li>
            </ol>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Importante
                </div>
                <p class="mb-0">Si deshabilita el almacenamiento externo, los archivos ya sincronizados permanecerán en Dropbox, pero los nuevos archivos no se guardarán automáticamente en la nube.</p>
            </div>
        </div>

        <h3>Estado de la Conexión</h3>

        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Significado</th>
                        <th>Acción Recomendada</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Estado"><span class="badge bg-success">Conectado</span></td>
                        <td data-label="Significado">La conexión con Dropbox está activa y funcionando correctamente</td>
                        <td data-label="Accion">Ninguna. Sistema funcionando normalmente.</td>
                    </tr>
                    <tr>
                        <td data-label="Estado"><span class="badge bg-warning">Token Próximo a Expirar</span></td>
                        <td data-label="Significado">El token de autenticación vence pronto (generalmente cada 6 meses)</td>
                        <td data-label="Accion">Reconecte su cuenta de Dropbox antes de que expire</td>
                    </tr>
                    <tr>
                        <td data-label="Estado"><span class="badge bg-danger">Desconectado</span></td>
                        <td data-label="Significado">No hay conexión activa con Dropbox</td>
                        <td data-label="Accion">Haga clic en "Conectar con Dropbox" para restaurar la conexión</td>
                    </tr>
                    <tr>
                        <td data-label="Estado"><span class="badge bg-secondary">Sin Configurar</span></td>
                        <td data-label="Significado">Nunca se ha configurado almacenamiento externo</td>
                        <td data-label="Accion">Haga clic en "Conectar con Dropbox" para empezar</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Gestionar Conexión</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Desconectar Dropbox (Si Fuera Necesario)</span></h4>
            <p>Si desea cambiar de cuenta de Dropbox o dejar de usar almacenamiento externo:</p>
            <ol>
                <li>Haga clic en el botón <strong>"Desconectar de Dropbox"</strong></li>
                <li>Se le pedirá confirmación de la acción</li>
                <li>Después de confirmar, se eliminará el acceso de SAMI a su Dropbox</li>
                <li>Puede reconectarse en cualquier momento</li>
            </ol>
        </div>

        <h3>Archivos que se Sincronizan</h3>
        <p>Los siguientes tipos de documentos se guardarán automáticamente en Dropbox cuando la sincronización esté habilitada:</p>
        <ul>
            <li>Documentos generados (facturas, recetas, órdenes médicas)</li>
            <li>Imágenes clínicas cargadas en consultas</li>
            <li>Archivos adjuntos a historiales de pacientes</li>
            <li>Reportes y documentos descargables</li>
            <li>Certificados médicos generados</li>
        </ul>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-folder me-2 text-primary"></i>
                Estructura de Carpetas
            </div>
            <p class="mb-0">Los archivos se organizarán automáticamente en carpetas dentro de su Dropbox por tipo de documento y fecha, facilitando la búsqueda y organización.</p>
        </div>

        <h3>Solución de Problemas</h3>

        <div class="row">
            <div class="col-md-6">
                <div class="info-box danger">
                    <div class="info-box-title">
                        <i class="fas fa-times-circle text-danger"></i>
                        Error de Conexión
                    </div>
                    <p class="mb-0"><strong>Solución:</strong> Verifique que su cuenta de Dropbox esté activa y que no tenga restricciones. Intente desconectar y conectarse nuevamente.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box danger">
                    <div class="info-box-title">
                        <i class="fas fa-times-circle text-danger"></i>
                        Espacio Insuficiente
                    </div>
                    <p class="mb-0"><strong>Solución:</strong> Compre más almacenamiento en Dropbox o elimine archivos innecesarios para liberar espacio.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 11: Waitlist -->
    <section id="lista-espera" class="content-section">
        <h2><i class="fas fa-hourglass-half me-2"></i>11. Lista de Espera</h2>

        <p>Configure cómo SAMI manejará la <strong>lista de espera de pacientes</strong> que no encontraron disponibilidad inmediata. Puede elegir entre asignación automática o manual de slots cuando haya cancelaciones.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menu → Configuraciones → Lista de Espera</strong></p>
        </div>

        <h3>¿Qué es la Lista de Espera?</h3>
        <p>La lista de espera es un sistema que permite a los pacientes registrarse para ser notificados cuando:</p>
        <ul>
            <li>Se cancela una cita agendada</li>
            <li>Se libera un slot con disponibilidad</li>
            <li>Hay una fecha más cercana disponible</li>
        </ul>

        <h3>Modos de Operación</h3>

        <div class="row mt-4 mb-4">
            <div class="col-md-6">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-robot" style="color: #4caf50;"></i>
                    </div>
                    <h5>Asignación Automática</h5>
                    <p>El sistema asigna automáticamente slots a pacientes en la lista cuando hay disponibilidad</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-card text-center">
                    <div class="icon-circle mx-auto" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-user-tie" style="color: #2196f3;"></i>
                    </div>
                    <h5>Asignación Manual</h5>
                    <p>Usted revisa la lista y decide qué pacientes contactar cuando hay slots disponibles</p>
                </div>
            </div>
        </div>

        <h3>Configurar Modo de Asignación</h3>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a Lista de Espera</span></h4>
            <p>Navegue a <strong>Configuraciones → Lista de Espera</strong>. Verá un toggle para habilitar la asignación automática.</p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Elegir Modo de Operación</span></h4>

            <h5 style="color: #5e35b1; margin-top: 20px; margin-bottom: 10px;"><i class="fas fa-toggle-on me-2"></i>Activar Asignación Automática</h5>
            <p>Para que el sistema asigne automáticamente:</p>
            <ol>
                <li>Marque la casilla <strong>"Habilitar Asignación Automática"</strong></li>
                <li>El sistema alertará si no está completamente configurado</li>
                <li>Guarde los cambios</li>
            </ol>

            <h5 style="color: #5e35b1; margin-top: 20px; margin-bottom: 10px;"><i class="fas fa-toggle-off me-2"></i>Mantener Asignación Manual</h5>
            <p>Para revisar y asignar manualmente:</p>
            <ol>
                <li>Deje la casilla <strong>sin marcar</strong></li>
                <li>Recibirá notificaciones cuando haya cancelaciones</li>
                <li>Acceda a la lista de espera desde el calendario de citas</li>
                <li>Seleccione pacientes y confirme la asignación manualmente</li>
            </ol>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Cambios en Tiempo Real
                </div>
                <p class="mb-0">Puede cambiar entre modos en cualquier momento. El cambio se aplicará inmediatamente para nuevas asignaciones.</p>
            </div>
        </div>

        <h3>Comparativa de Modos</h3>

        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Característica</th>
                        <th>Asignación Automática</th>
                        <th>Asignación Manual</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Caracteristica"><strong>Velocidad</strong></td>
                        <td data-label="Automatica">Inmediata (segundos)</td>
                        <td data-label="Manual">Según su disponibilidad</td>
                    </tr>
                    <tr>
                        <td data-label="Caracteristica"><strong>Criterios de Selección</strong></td>
                        <td data-label="Automatica">Por antigüedad en lista</td>
                        <td data-label="Manual">Usted elige</td>
                    </tr>
                    <tr>
                        <td data-label="Caracteristica"><strong>Control del Médico</strong></td>
                        <td data-label="Automatica">Limitado</td>
                        <td data-label="Manual">Total</td>
                    </tr>
                    <tr>
                        <td data-label="Caracteristica"><strong>Notificación de Paciente</strong></td>
                        <td data-label="Automatica">Automática</td>
                        <td data-label="Manual">Después de confirmar</td>
                    </tr>
                    <tr>
                        <td data-label="Caracteristica"><strong>Casos de Uso Ideal</strong></td>
                        <td data-label="Automatica">Clínicas con alto volumen</td>
                        <td data-label="Manual">Prácticas pequeñas/medianas</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Gestión de la Lista de Espera</h3>

        <p>Una vez configurado el modo, puede acceder a la lista de espera desde varios lugares:</p>

        <ul>
            <li><strong>Calendario de Citas:</strong> Panel lateral con pacientes esperando</li>
            <li><strong>Dashboard:</strong> Notificaciones de nuevas entradas a la lista</li>
            <li><strong>Perfil del Paciente:</strong> Historial de esperas del paciente</li>
            <li><strong>Reportes:</strong> Análisis de tiempo en espera y tasas de conversión</li>
        </ul>

        <h3>Ejemplo de Flujo - Asignación Automática</h3>

        <div class="step-card">
            <ol>
                <li><strong>Paciente A se registra en lista de espera</strong> - para una cita el 15 de septiembre</li>
                <li><strong>Paciente B también se registra</strong> - para la misma fecha</li>
                <li><strong>Se cancela una cita</strong> - se libera un slot el 12 de septiembre</li>
                <li><strong>Sistema asigna automáticamente</strong> - Paciente A (primero en llegar)</li>
                <li><strong>Se envía notificación</strong> - Paciente A recibe su nueva cita</li>
                <li><strong>Paciente A confirma o rechaza</strong> - Si rechaza, se ofrece a Paciente B</li>
            </ol>
        </div>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Importante
            </div>
            <ul class="mb-0">
                <li>Los pacientes deben tener un método de contacto registrado (email o teléfono) para recibir notificaciones</li>
                <li>La asignación respeta los horarios del médico configurados</li>
                <li>Los pacientes pueden rechazar la asignación, manteniendo su posición en la lista</li>
                <li>Es recomendable revisar periódicamente la lista incluso con asignación automática</li>
            </ul>
        </div>
    </section>

    <!-- Section 12: Tips -->
    <section id="tips" class="content-section">
        <h2><i class="fas fa-lightbulb me-2"></i>12. Tips y Mejores Practicas</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-check text-success"></i>
                        Mejores Practicas
                    </div>
                    <ul class="mb-0">
                        <li>Configure sus servicios antes de empezar a facturar</li>
                        <li>Mantenga actualizados sus horarios de trabajo</li>
                        <li>Agregue solo los accesos rapidos que realmente usa</li>
                        <li>Ordene las secciones de consulta segun su flujo de trabajo</li>
                        <li>Revise periodicamente sus configuraciones</li>
                        <li>Use nombres descriptivos en servicios personalizados</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation text-warning"></i>
                        Errores Comunes a Evitar
                    </div>
                    <ul class="mb-0">
                        <li>No configurar horarios antes de agendar citas</li>
                        <li>Agregar demasiados accesos rapidos (pierde efectividad)</li>
                        <!--<li>Dejar servicios sin precio definido</li>
                        <li>No asociar sucursal/consultorio a los horarios</li>
                        <li>Remover secciones obligatorias de consulta</li>-->
                        <li>No guardar los cambios antes de salir</li>
                    </ul>
                </div>
            </div>
        </div>

        <h3>Resumen de Permisos</h3>
        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Funcionalidad</th>
                        <!--<th>Permiso Requerido</th>-->
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Catalogo de Servicios</strong></td>
                        <!--<td data-label="Permiso"><code>settings.create_user_procedures</code></td>-->
                        <td data-label="Descripcion">Crear y gestionar servicios</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Plantilla de Consulta</strong></td>
                        <!--<td data-label="Permiso"><code>settings.create_consultation_template</code></td>-->
                        <td data-label="Descripcion">Personalizar secciones de consulta</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Accesos Rapidos</strong></td>
                        <!--<td data-label="Permiso"><code>settings.create_rapid_access</code></td>-->
                        <td data-label="Descripcion">Configurar accesos rapidos</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Horarios Laborales</strong></td>
                        <!--<td data-label="Permiso"><code>settings.create_working_hour_user</code></td>-->
                        <td data-label="Descripcion">Configurar horarios de trabajo</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Plantilla de Factura</strong></td>
                        <td data-label="Descripcion">Seleccionar diseño de facturas</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Plantilla de Incapacidad Médica</strong></td>
                        <td data-label="Descripcion">Seleccionar diseño de incapacidades</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Código de Referidos</strong></td>
                        <td data-label="Descripcion">Gestionar códigos de invitación</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Firma y Sello</strong></td>
                        <td data-label="Descripcion">Cargar y gestionar firma y sello digital para documentos</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Almacenamiento Externo</strong></td>
                        <td data-label="Descripcion">Configurar sincronización con Dropbox</td>
                    </tr>
                    <tr>
                        <td data-label="Funcionalidad"><strong>Lista de Espera</strong></td>
                        <td data-label="Descripcion">Configurar modo de asignación de slots (automático o manual)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="info-box note mt-4">
            <div class="info-box-title">
                <i class="fas fa-question-circle text-primary"></i>
                Necesita Ayuda?
            </div>
            <p class="mb-0">Si tiene dudas sobre la configuracion de su cuenta o encuentra algun problema, contacte al equipo de soporte tecnico de SAMI. Tambien puede revisar las otras guias del Centro de Ayuda para aprender sobre funcionalidades relacionadas.</p>
        </div>
    </section>

        </div> <!-- End col-lg-8 -->
    </div> <!-- End row -->

    <!-- Navigation -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.doctor-dashboard') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Dashboard Médico
        </a>
        <a href="{{ route('help.profile') }}" class="btn btn-primary btn-lg">
            Mi Perfil <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
@stop
