<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Configuraciones - Centro de Ayuda SAMI</title>
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
            --settings-color: #5e35b1;
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

        /* Header */
        .help-header {
            background: linear-gradient(135deg, #5e35b1 0%, #7c4dff 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(94, 53, 177, 0.3);
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

        /* Steps */
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

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #ede7f6 0%, #d1c4e9 100%);
            border: 2px dashed #7c4dff;
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
            color: #5e35b1;
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #4527a0;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: #5e35b1;
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
            background: #5e35b1;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(94, 53, 177, 0.4);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background: #4527a0;
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

            .transfer-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
   @include('help.sidebar', ['active' => 'settings'])

    <!-- Main Content -->
    <main class="help-content">

        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('help.settings') }}">Guias de Configuraciones</a></li>
                <li class="breadcrumb-item active">Configuraciones</li>
            </ol>
        </nav>
        <!-- Header -->
        <header class="help-header">
            <h1><i class="fas fa-cogs me-3"></i>Guia de Configuraciones</h1>
            <p>Aprende a configurar tu cuenta: servicios, horarios, accesos rapidos y plantillas de consulta.</p>           
        </header>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                        <li><a href="#catalogo-servicios"><i class="fas fa-clipboard-list"></i> 2. Catálogo de Servicios</a></li>
                        <li><a href="#horarios-laborales"><i class="fas fa-clock"></i> 3. Horarios Laborales</a></li>
                        <li><a href="#accesos-rapidos"><i class="fas fa-bolt"></i> 4. Accesos Rápidos</a></li>
                        <li><a href="#plantilla-consulta"><i class="fas fa-file-medical"></i> 5. Plantilla de Consulta</a></li>
                        <li><a href="#tips"><i class="fas fa-lightbulb"></i> 6. Tips y Mejores Prácticas</a></li>
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
                <div class="col-md-3 mb-3">
                    <div class="feature-card text-center">
                        <div class="icon-circle mx-auto" style="background: rgba(76, 175, 80, 0.1);">
                            <i class="fas fa-clipboard-list" style="color: #4caf50;"></i>
                        </div>
                        <h5>Catalogo de Servicios</h5>
                        <p>Define los procedimientos y servicios que ofreces</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="feature-card text-center">
                        <div class="icon-circle mx-auto" style="background: rgba(33, 150, 243, 0.1);">
                            <i class="fas fa-clock" style="color: #2196f3;"></i>
                        </div>
                        <h5>Horarios Laborales</h5>
                        <p>Configura tus dias y horas de atencion</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="feature-card text-center">
                        <div class="icon-circle mx-auto" style="background: rgba(255, 152, 0, 0.1);">
                            <i class="fas fa-bolt" style="color: #ff9800;"></i>
                        </div>
                        <h5>Accesos Rapidos</h5>
                        <p>Procedimientos frecuentes a un click</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="feature-card text-center">
                        <div class="icon-circle mx-auto" style="background: rgba(156, 39, 176, 0.1);">
                            <i class="fas fa-file-medical" style="color: #9c27b0;"></i>
                        </div>
                        <h5>Plantilla Consulta</h5>
                        <p>Personaliza las secciones de tus consultas</p>
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
                <p class="mb-0">Cada seccion de configuracion requiere permisos especificos. Si no puede ver alguna opcion, contacte al administrador de su organizacion para solicitar los permisos necesarios.</p>
            </div>
        </section>

        <!-- Section 2: Service Catalog -->
        <section id="catalogo-servicios" class="content-section">
            <h2><i class="fas fa-clipboard-list me-2"></i>2. Catalogo de Servicios</h2>

            <p>El <strong>Catalogo de Servicios</strong> le permite definir todos los procedimientos, consultas y servicios que ofrece en su practica. Estos servicios se utilizan para facturacion, ordenes medicas y estadisticas.</p>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-route text-success"></i>
                    Ruta de Acceso
                </div>
                <p class="mb-0"><strong>Menu → Configuraciones → Servicios</strong><br>URL: <code>/settings/create_user_procedures</code></p>
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
                <p>En la seccion "Agregar Servicio CPT", utilice el buscador para encontrar el codigo:</p>
                <ul>
                    <li>Escriba el codigo CPT (ej: 99213)</li>
                    <li>O escriba parte del nombre del procedimiento</li>
                    <li>Seleccione de la lista desplegable</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/settings/setting_cpt.png') }}" alt="" style="width: 100%;">
            </div>

            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Completar Informacion del Servicio</span></h4>
                <p>Una vez seleccionado el codigo CPT, complete los campos adicionales:</p>

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
                            <td><strong>Codigo CPT</strong></td>
                            <td>Codigo seleccionado del buscador</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Precio</strong></td>
                            <td>Precio base del servicio en la moneda configurada</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Complejidad</strong></td>
                            <td>Nivel de complejidad: Baja, Media o Alta</td>
                            <td><span class="required-badge">Requerido</span></td>
                        </tr>
                        <tr>
                            <td><strong>Especialidad</strong></td>
                            <td>Especialidad medica asociada</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>Duracion (min)</strong></td>
                            <td>Tiempo estimado del procedimiento</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>Copago</strong></td>
                            <td>Monto de copago para pacientes con seguro</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>Requiere Autorizacion</strong></td>
                            <td>Indica si requiere autorizacion previa del seguro</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>Cubierto por Seguro</strong></td>
                            <td>Indica si el servicio esta cubierto por seguros</td>
                            <td><span class="optional-badge">Opcional</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Guardar el Servicio</span></h4>
                <p>Haga click en <strong>"Agregar Servicio"</strong> para guardar. El sistema:</p>
                <ul>
                    <li>Genera automaticamente un codigo de servicio (ej: PROC_0001)</li>
                    <li>Vincula el servicio a su cuenta</li>
                    <li>Lo hace disponible para facturacion y ordenes</li>
                </ul>
            </div>

            <h3>Crear un Servicio Personalizado</h3>
            <p>Para servicios que no tienen codigo CPT:</p>

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
                        <td><strong>Nombre</strong></td>
                        <td>Nombre descriptivo del servicio (max 255 caracteres)</td>
                        <td><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td><strong>Descripcion</strong></td>
                        <td>Descripcion detallada del servicio</td>
                        <td><span class="optional-badge">Opcional</span></td>
                    </tr>
                    <tr>
                        <td><strong>Tipo de Servicio</strong></td>
                        <td>Categoria del servicio</td>
                        <td><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td><strong>Precio</strong></td>
                        <td>Precio base del servicio</td>
                        <td><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td><strong>Complejidad</strong></td>
                        <td>Nivel: Baja, Media o Alta</td>
                        <td><span class="required-badge">Requerido</span></td>
                    </tr>
                    <tr>
                        <td><strong>Codigo de Ingreso</strong></td>
                        <td>Codigo contable para facturacion</td>
                        <td><span class="optional-badge">Opcional</span></td>
                    </tr>
                </tbody>
            </table>

            <h4>Tipos de Servicio Disponibles</h4>
            <div class="row">
                <div class="col-md-4">
                    <ul>
                        <li><strong>Consulta</strong> - Consultas medicas</li>
                        <li><strong>Procedimiento</strong> - Procedimientos menores</li>
                        <li><strong>Diagnostico</strong> - Estudios diagnosticos</li>
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
                        <li><strong>Insumo</strong> - Materiales y suministros</li>
                        <li><strong>Otro</strong> - Servicios varios</li>
                    </ul>
                </div>
            </div>

            <h4>Niveles de Complejidad</h4>
            <p>
                <span class="complexity-badge complexity-low">Baja</span>
                <span class="complexity-badge complexity-medium">Media</span>
                <span class="complexity-badge complexity-high">Alta</span>
            </p>

            <div>
                <img src="{{ asset('images/tutorial/settings/setting_catalog.png') }}" alt="" style="width: 100%;">
            </div>


            <h3>Gestionar Servicios Existentes</h3>
            <ul>
                <li><strong>Editar:</strong> Modificar precio, complejidad y otros campos (no el codigo CPT)</li>
                <li><strong>Activar/Desactivar:</strong> Habilitar o deshabilitar servicios sin eliminarlos</li>
                <li><strong>Eliminar:</strong> Remover servicios que ya no utiliza</li>
            </ul>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Importante
                </div>
                <p class="mb-0">Los servicios eliminados no se borran permanentemente (eliminacion suave). El historial de facturacion y ordenes previas se mantiene intacto.</p>
            </div>
        </section>

        <!-- Section 3: Working Hours -->
        <section id="horarios-laborales" class="content-section">
            <h2><i class="fas fa-clock me-2"></i>3. Horarios Laborales</h2>

            <p>Configure sus <strong>horarios de trabajo</strong> para cada dia de la semana. Estos horarios se utilizan para el sistema de agendamiento de citas y para mostrar su disponibilidad a los pacientes.</p>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-route text-success"></i>
                    Ruta de Acceso
                </div>
                <p class="mb-0"><strong>Menu → Configuraciones → Horario Laboral</strong><br>URL: <code>/settings/create_working_hour_user</code></p>
            </div>

            <h3>Estructura de Horarios</h3>
            <p>Cada horario de trabajo esta vinculado a:</p>
            <ul>
                <li><strong>Sucursal:</strong> La ubicacion fisica donde atendera</li>
                <li><strong>Consultorio:</strong> El consultorio especifico dentro de esa sucursal</li>
                <li><strong>Dia de la semana:</strong> De lunes a domingo</li>
                <li><strong>Rango de horas:</strong> Hora de inicio y fin de atencion</li>
            </ul>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Multiples Turnos
                </div>
                <p class="mb-0">Puede configurar multiples turnos por dia. Por ejemplo, si atiende en la manana en una sucursal y en la tarde en otra, agregue dos horarios para el mismo dia.</p>
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
                    <li>Defina la <strong>Hora de Inicio</strong> (formato HH:MM)</li>
                    <li>Defina la <strong>Hora de Fin</strong> (debe ser posterior al inicio)</li>
                </ol>

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
                            <td><strong>Habilitar</strong></td>
                            <td>Activa el horario para este dia</td>
                            <td>Checkbox</td>
                        </tr>
                        <tr>
                            <td><strong>Sucursal</strong></td>
                            <td>Ubicacion de atencion</td>
                            <td>Requerido si habilitado</td>
                        </tr>
                        <tr>
                            <td><strong>Consultorio</strong></td>
                            <td>Consultorio dentro de la sucursal</td>
                            <td>Requerido si habilitado</td>
                        </tr>
                        <tr>
                            <td><strong>Hora Inicio</strong></td>
                            <td>Hora de inicio de atencion</td>
                            <td>Formato HH:MM, default 08:00</td>
                        </tr>
                        <tr>
                            <td><strong>Hora Fin</strong></td>
                            <td>Hora de fin de atencion</td>
                            <td>Debe ser mayor que inicio, default 18:00</td>
                        </tr>
                    </tbody>
                </table>
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
                <img src="{{ asset('images/tutorial/settings/setting_schedule2.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Guardar Configuracion</span></h4>
                <p>Una vez configurados todos los dias:</p>
                <ol>
                    <li>Revise el resumen de horarios configurados</li>
                    <li>Los dias con horarios habilitados mostraran una insignia <span class="badge bg-success">Configurado</span></li>
                    <li>Haga click en <strong>"Guardar"</strong> para aplicar los cambios</li>
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

        <!-- Section 4: Rapid Access -->
        <section id="accesos-rapidos" class="content-section">
            <h2><i class="fas fa-bolt me-2"></i>4. Accesos Rapidos</h2>

            <p>Los <strong>Accesos Rapidos</strong> le permiten tener a la mano los procedimientos, laboratorios e imagenes que solicita con mayor frecuencia. Durante las consultas, puede agregar estos items con un solo click.</p>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-route text-success"></i>
                    Ruta de Acceso
                </div>
                <p class="mb-0"><strong>Menu → Configuraciones → Accesos Rapidos</strong><br>URL: <code>/settings/create_rapid_access</code></p>
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
                    <li>Utilice el buscador para encontrar el codigo CPT deseado</li>
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
                    <li>Vera la lista de items seleccionados debajo del buscador</li>
                    <li>Use el boton <i class="fas fa-trash text-danger"></i> para eliminar items que ya no necesite</li>
                    <li>Los cambios se guardan automaticamente</li>
                </ul>
            </div>

            <h3>Uso Durante la Consulta</h3>
            <p>Durante una consulta medica, los accesos rapidos aparecen en un panel lateral:</p>
            <ol>
                <li>Abra el panel de <strong>"Accesos Rapidos"</strong> en la consulta</li>
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

        <!-- Section 5: Consultation Template -->
        <section id="plantilla-consulta" class="content-section">
            <h2><i class="fas fa-file-medical me-2"></i>5. Plantilla de Consulta</h2>

            <p>La <strong>Plantilla de Consulta</strong> le permite personalizar que secciones aparecen durante sus consultas medicas y en que orden. Esto agiliza su flujo de trabajo mostrando solo las secciones relevantes para su especialidad.</p>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-route text-success"></i>
                    Ruta de Acceso
                </div>
                <p class="mb-0"><strong>Menu → Configuraciones → Plantilla Consulta</strong><br>URL: <code>/settings/create_consultation_template</code></p>
            </div>

            <h3>Secciones Disponibles</h3>
            <p>SAMI ofrece multiples secciones que puede incluir en sus consultas:</p>

            <div class="row">
                <div class="col-md-6">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Seccion</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Signos Vitales</strong></td>
                                <td>Presion arterial, frecuencia cardiaca, temperatura, etc.</td>
                            </tr>
                            <tr>
                                <td><strong>Examen Fisico</strong></td>
                                <td>Hallazgos del examen fisico por sistemas</td>
                            </tr>
                            <tr>
                                <td><strong>Condiciones</strong></td>
                                <td>Diagnosticos y condiciones del paciente</td>
                            </tr>
                            <tr>
                                <td><strong>Enfermedad Actual</strong></td>
                                <td>Descripcion de la enfermedad presente</td>
                            </tr>
                            <tr>
                                <td><strong>Prescripciones</strong></td>
                                <td>Medicamentos recetados</td>
                            </tr>
                            <tr>
                                <td><strong>Ordenes Medicas</strong></td>
                                <td>Laboratorios, imagenes y procedimientos</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Seccion</th>
                                <th>Descripcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Antecedentes</strong></td>
                                <td>Historial medico del paciente</td>
                            </tr>
                            <tr>
                                <td><strong>Notas Medicas</strong></td>
                                <td>Notas clinicas generales</td>
                            </tr>
                            <tr>
                                <td><strong>Notas Personales</strong></td>
                                <td>Notas privadas del medico</td>
                            </tr>
                            <tr>
                                <td><strong>Licencias Medicas</strong></td>
                                <td>Incapacidades y justificantes</td>
                            </tr>
                            <tr>
                                <td><strong>Plan</strong></td>
                                <td>Plan de tratamiento y seguimiento</td>
                            </tr>
                            <tr>
                                <td><strong>Especialidad</strong></td>
                                <td>Secciones especificas de su especialidad</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Agregar Secciones</span></h4>
                <p>Para agregar una seccion a su plantilla:</p>
                <ol>
                    <li>Busque la seccion deseada en la lista <strong>"Secciones Disponibles"</strong></li>
                    <li>Haga click en el boton para moverla a la lista de seleccionadas</li>
                    <li>La seccion aparecera inmediatamente en <strong>"Secciones Seleccionadas"</strong></li>
                </ol>

            <div>
                <img src="{{ asset('images/tutorial/settings/setting_template.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Ordenar Secciones</span></h4>
                <p>Puede cambiar el orden de las secciones arrastrando:</p>
                <ol>
                    <li>En la lista de <strong>"Secciones Seleccionadas"</strong></li>
                    <li>Haga click sostenido en una seccion</li>
                    <li>Arrastre hacia arriba o abajo para cambiar su posicion</li>
                    <li>Suelte para confirmar el nuevo orden</li>
                </ol>
                <p>El orden define como aparecen las secciones durante la consulta.</p>
            </div>

            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Quitar Secciones</span></h4>
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
                    <p class="mb-0">Algunas secciones son <strong>obligatorias</strong> y no pueden ser removidas. Estas secciones son necesarias para el funcionamiento correcto del sistema y apareceran siempre en sus consultas.</p>
                </div>
            </div>

            <h3>Secciones por Especialidad</h3>
            <p>Dependiendo de su especialidad medica, vera secciones adicionales especificas:</p>
            <ul>
                <li><strong>Dermatologia:</strong> Secciones para lesiones de piel, fotografia clinica</li>
                <li><strong>Ginecologia:</strong> Historia ginecologica, examenes especializados</li>
                <li><strong>Pediatria:</strong> Curvas de crecimiento, desarrollo</li>
                <li><strong>Cardiologia:</strong> Electrocardiograma, evaluacion cardiovascular</li>
                <li>Y mas segun su especialidad...</li>
            </ul>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-user-md text-primary"></i>
                    Primera Configuracion
                </div>
                <p class="mb-0">Si es la primera vez que accede, el sistema cargara automaticamente todas las secciones generales como base. Puede personalizar desde ahi.</p>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/settings/setting_templderm.png') }}" alt="" style="width: 100%;">
            </div>
            <div>
                <img src="{{ asset('images/tutorial/settings/setting_enderm.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 6: Tips -->
        <section id="tips" class="content-section">
            <h2><i class="fas fa-lightbulb me-2"></i>6. Tips y Mejores Practicas</h2>

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
                            <li>Dejar servicios sin precio definido</li>
                            <li>No asociar sucursal/consultorio a los horarios</li>
                            <li>Remover secciones obligatorias de consulta</li>
                            <li>No guardar los cambios antes de salir</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h3>Resumen de Permisos</h3>
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Funcionalidad</th>
                        <th>Permiso Requerido</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Catalogo de Servicios</strong></td>
                        <td><code>settings.create_user_procedures</code></td>
                        <td>Crear y gestionar servicios</td>
                    </tr>
                    <tr>
                        <td><strong>Horarios Laborales</strong></td>
                        <td><code>settings.create_working_hour_user</code></td>
                        <td>Configurar horarios de trabajo</td>
                    </tr>
                    <tr>
                        <td><strong>Accesos Rapidos</strong></td>
                        <td><code>settings.create_rapid_access</code></td>
                        <td>Configurar accesos rapidos</td>
                    </tr>
                    <tr>
                        <td><strong>Plantilla de Consulta</strong></td>
                        <td><code>settings.create_consultation_template</code></td>
                        <td>Personalizar secciones de consulta</td>
                    </tr>
                </tbody>
            </table>

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
            <a href="{{ route('help.appointments') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Agendamiento de Citas
            </a>
            <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
        </div>
    </main>

    <!-- Back to Top -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Back to top visibility
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });
    </script>
</body>
</html>
