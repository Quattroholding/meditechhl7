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

/* Code blocks */
.code-block {
    background: #f5f5f5;
    border-left: 4px solid #5e35b1;
    padding: 15px;
    border-radius: 0 8px 8px 0;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    overflow-x: auto;
}

/* Screenshot placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
    border: 2px dashed #bdbdbd;
    border-radius: 10px;
    padding: 60px 20px;
    text-align: center;
    margin: 20px 0;
    color: #757575;
}

.screenshot-placeholder i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
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

    .field-table td[data-label="Campo"], .field-table td[data-label="Paso"] {
        background: #5e35b1;
        color: white;
        font-size: 15px;
        padding: 12px;
        border-bottom: 2px solid #552fa1;
    }

    .field-table td[data-label="Campo"] strong, .field-table td[data-label="Paso"] strong {
        color: white;
    }

    .field-table td[data-label="Descripcion"] {
        padding: 15px;
        color: #555;
        font-size: 13px;
        line-height: 1.5;
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
}

@stop

@section('sidebar')
    @include('help.sidebar', ['active' => 'external-storage'])
@stop

@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('help.settings') }}">Guías de Configuraciones</a></li>
            <li class="breadcrumb-item active">Almacenamiento Externo</li>
        </ol>
    </nav>
@endsection

@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-cloud-upload-alt me-3"></i>Almacenamiento Externo de Archivos</h1>
        <p>Configure Dropbox para almacenar archivos de consultas médicas en la nube de forma segura y accesible.</p>
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
                    <li><a href="#crear-app-dropbox"><i class="fab fa-dropbox"></i> 2. Crear App en Dropbox</a></li>
                    <li><a href="#configurar-permisos"><i class="fas fa-shield-alt"></i> 3. Configurar Permisos</a></li>
                    <li><a href="#obtener-token"><i class="fas fa-key"></i> 4. Obtener Access Token</a></li>
                    <li><a href="#configurar-sami"><i class="fas fa-cog"></i> 5. Configurar en SAMI</a></li>
                    <li><a href="#usar-sistema"><i class="fas fa-file-upload"></i> 6. Subir Archivos</a></li>
                    <li><a href="#gestionar-archivos"><i class="fas fa-folder-open"></i> 7. Gestionar Archivos</a></li>
                    <li><a href="#tips"><i class="fas fa-lightbulb"></i> 8. Tips y Seguridad</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-8">

    <!-- Section 1: Introduction -->
    <section id="introduccion" class="content-section">
        <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Almacenamiento Externo</h2>

        <p>El módulo de <strong>Almacenamiento Externo</strong> permite integrar SAMI con servicios en la nube como Dropbox para almacenar archivos de consultas médicas (fotografías clínicas, imágenes diagnósticas, resultados de laboratorio, etc.).</p>

        <div class="info-box danger">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                Requisito Obligatorio
            </div>
            <p class="mb-0"><strong>El almacenamiento externo es OBLIGATORIO</strong> para subir archivos en SAMI. No es posible almacenar archivos en el servidor local. Debe configurar Dropbox antes de poder subir cualquier archivo durante las consultas.</p>
        </div>

        <h3>Beneficios del Almacenamiento Externo</h3>
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-shield-alt" style="color: #2196f3;"></i>
                    </div>
                    <h5>Seguridad y Respaldo</h5>
                    <p>Los archivos se almacenan en la infraestructura segura de Dropbox con respaldo automático y cifrado.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-globe" style="color: #4caf50;"></i>
                    </div>
                    <h5>Acceso desde Cualquier Lugar</h5>
                    <p>Acceda a los archivos médicos desde cualquier dispositivo con conexión a internet.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-expand-arrows-alt" style="color: #ff9800;"></i>
                    </div>
                    <h5>Escalabilidad</h5>
                    <p>No hay límites de almacenamiento más allá de los de su cuenta de Dropbox.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-lock" style="color: #9c27b0;"></i>
                    </div>
                    <h5>Privacidad</h5>
                    <p>Cada cliente puede usar su propia cuenta de Dropbox, manteniendo los datos separados.</p>
                </div>
            </div>
        </div>

        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-user-shield text-primary"></i>
                Quién Puede Configurar
            </div>
            <p class="mb-0">Solo los usuarios con <strong>suscripción activa</strong> pueden configurar el almacenamiento externo. Verifique que su cuenta tenga permisos de administrador y suscripción vigente.</p>
        </div>
    </section>

    <!-- Section 2: Create Dropbox App -->
    <section id="crear-app-dropbox" class="content-section">
        <h2><i class="fab fa-dropbox me-2"></i>2. Crear una Aplicación en Dropbox</h2>

        <p>Para conectar SAMI con Dropbox, primero debe crear una aplicación en el <strong>Dropbox App Console</strong>. Este proceso es gratuito y solo toma unos minutos.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder al Dropbox App Console</span></h4>
            <p>Abra su navegador y visite:</p>
            <div class="code-block">
                https://www.dropbox.com/developers/apps
            </div>
            <p class="mt-3">Inicie sesión con su cuenta de Dropbox (o cree una si no tiene).</p>

            <!-- Espacio para captura de pantalla -->


                <img src="{{url('images/tutorial/external_storage/step1.png')}}" width="100%">

                <small>Muestra el botón "Create app" en la interfaz de Dropbox Developers</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Crear Nueva App</span></h4>
            <p>Haga clic en el botón <strong>"Create app"</strong> y complete los siguientes campos:</p>

            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valor Recomendado</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Choose an API</strong></td>
                            <td data-label="Valor"><span class="badge bg-primary">Scoped access</span></td>
                            <td data-label="Descripcion">Seleccione "Scoped access" para usar la API moderna de Dropbox</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Choose the type of access</strong></td>
                            <td data-label="Valor"><span class="badge bg-primary">Full Dropbox</span></td>
                            <td data-label="Descripcion">Puede elegir "Full Dropbox" o "App folder" (carpeta dedicada). Recomendamos "Full Dropbox" para mayor flexibilidad.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Name your app</strong></td>
                            <td data-label="Valor"><code>SAMI-Meditech2</code></td>
                            <td data-label="Descripcion">Nombre único para su aplicación. Use algo descriptivo como "SAMI-[NombreClinica]"</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Importante
                </div>
                <p class="mb-0">El nombre de la app debe ser <strong>único en todo Dropbox</strong>. Si el nombre ya existe, agregue un sufijo único como su nombre o fecha.</p>
            </div>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step2.png')}}" width="100%">
            <small>Muestra las opciones "Scoped access", "Full Dropbox" y el campo de nombre</small>


            <p class="mt-3">Haga clic en <strong>"Create app"</strong> para finalizar.</p>
        </div>
    </section>

    <!-- Section 3: Configure Permissions -->
    <section id="configurar-permisos" class="content-section">
        <h2><i class="fas fa-shield-alt me-2"></i>3. Configurar Permisos de la App</h2>

        <p>Una vez creada la aplicación, debe configurar los <strong>permisos necesarios</strong> para que SAMI pueda leer, escribir y eliminar archivos en Dropbox.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Ir a la Pestaña "Permissions"</span></h4>
            <p>En la página de configuración de su app, haga clic en la pestaña <strong>"Permissions"</strong>.</p>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step3.png')}}" width="100%">
            <small>Muestra las pestañas Settings, Permissions, Branding en la interfaz</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Activar Permisos Requeridos</span></h4>
            <p>Busque y active los siguientes permisos en la sección <strong>"Files and folders"</strong>:</p>

            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Permiso</th>
                            <th>Descripción</th>
                            <th>Requerido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Permiso"><strong>files.content.write</strong></td>
                            <td data-label="Descripcion">Permite crear y subir archivos a Dropbox</td>
                            <td data-label="Requerido"><span class="required-badge">OBLIGATORIO</span></td>
                        </tr>
                        <tr>
                            <td data-label="Permiso"><strong>files.content.read</strong></td>
                            <td data-label="Descripcion">Permite leer y descargar archivos desde Dropbox</td>
                            <td data-label="Requerido"><span class="required-badge">OBLIGATORIO</span></td>
                        </tr>
                        <tr>
                            <td data-label="Permiso"><strong>files.metadata.write</strong></td>
                            <td data-label="Descripcion">Permite eliminar y mover archivos</td>
                            <td data-label="Requerido"><span class="required-badge">OBLIGATORIO</span></td>
                        </tr>
                        <tr>
                            <td data-label="Permiso"><strong>sharing.write</strong></td>
                            <td data-label="Descripcion">Permite crear enlaces compartidos para visualizar archivos</td>
                            <td data-label="Requerido"><span class="required-badge">OBLIGATORIO</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step4.png')}}" width="100%">
            <small>Muestra files.content.write, files.content.read, files.metadata.write y sharing.write marcados</small>



        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Guardar Cambios</span></h4>
            <p>Después de seleccionar todos los permisos, haga clic en el botón <strong>"Submit"</strong> al final de la página para guardar los cambios.</p>

            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-circle text-danger"></i>
                    Muy Importante
                </div>
                <p class="mb-0">Si cambia los permisos de una app existente, <strong>DEBE generar un nuevo Access Token</strong>. Los tokens antiguos no tendrán los nuevos permisos y causarán errores.</p>
            </div>
        </div>
    </section>

    <!-- Section 4: Get Access Token -->
    <section id="obtener-token" class="content-section">
        <h2><i class="fas fa-key me-2"></i>4. Obtener el Access Token</h2>

        <p>El <strong>Access Token</strong> es la credencial que permite a SAMI acceder a su cuenta de Dropbox de forma segura. Es como una contraseña específica para esta aplicación.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Ir a la Pestaña "Settings"</span></h4>
            <p>En la página de su app en Dropbox, haga clic en la pestaña <strong>"Settings"</strong>.</p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Buscar la Sección "OAuth 2"</span></h4>
            <p>Desplácese hacia abajo hasta encontrar la sección <strong>"OAuth 2"</strong>. Allí verá un botón que dice <strong>"Generate access token"</strong> o <strong>"Generated access token"</strong>.</p>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step5.png')}}" width="100%">
            <small>Muestra el botón "Generate" y el campo donde aparecerá el token</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Generar el Token</span></h4>
            <p>Haga clic en <strong>"Generate"</strong>. Dropbox generará un token largo que comienza con <code>sl.</code> seguido de caracteres aleatorios.</p>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Formato del Token
                </div>
                <p class="mb-0">El token tendrá un formato similar a:<br>
                <code>sl.B1aBCDeFGhIjKlMnOpQrStUvWxYz-1234567890AbCdEfGhIjK</code></p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Copiar el Token</span></h4>
            <p>Haga clic en el botón de copiar (icono de portapapeles) o seleccione todo el texto del token y cópielo. Lo necesitará en el siguiente paso.</p>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step6.png')}}" width="100%">
            <small>Muestra el token generado desplazar el taron a la derecha para copiar todo el token haciendo clic derecho en ratón , copiar o Ctrl C una vez ya este sombreado todo el token.</small>


            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-shield-alt text-danger"></i>
                    Seguridad del Token
                </div>
                <ul class="mb-0">
                    <li><strong>NO comparta este token con nadie</strong>. Quien lo tenga puede acceder a su Dropbox.</li>
                    <li>Si sospecha que el token fue comprometido, puede <strong>revocar el acceso</strong> desde esta misma página.</li>
                    <li>El token no tiene fecha de expiración, pero puede regenerar uno nuevo cuando lo necesite.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Section 5: Configure in SAMI -->
    <section id="configurar-sami" class="content-section">
        <h2><i class="fas fa-cog me-2"></i>5. Configurar Almacenamiento en SAMI</h2>

        <p>Ahora que tiene el Access Token de Dropbox, el siguiente paso es configurarlo en SAMI para activar el almacenamiento externo.</p>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-route text-success"></i>
                Ruta de Acceso
            </div>
            <p class="mb-0"><strong>Menú → Configuraciones → Almacenamiento Externo</strong></p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a la Configuración</span></h4>
            <p>En SAMI, navegue al menú lateral y haga clic en <strong>"Configuraciones"</strong>, luego seleccione <strong>"Almacenamiento Externo"</strong>.</p>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step6.5.png')}}" width="100%">
            <small>Muestra el menú "Configuraciones" expandido con la opción "Almacenamiento Externo"</small>


            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-lock text-primary"></i>
                    Permisos Necesarios
                </div>
                <p class="mb-0">Si no ve esta opción en el menú, verifique que:<br>
                1. Su cuenta tenga suscripción activa<br>
                2. Tenga permisos de administrador del cliente</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Completar el Formulario</span></h4>
            <p>Verá un formulario de configuración con los siguientes campos:</p>

            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Habilitar Almacenamiento Externo</strong></td>
                            <td data-label="Descripcion">Active este toggle para habilitar la funcionalidad</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Proveedor</strong></td>
                            <td data-label="Descripcion">Seleccione "Dropbox" (actualmente es el único proveedor disponible)</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Dropbox Access Token</strong></td>
                            <td data-label="Descripcion">Pegue aquí el token que copió de Dropbox (comienza con "sl.")</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step7.png')}}" width="100%">
            <small>Muestra el toggle habilitado, dropdown con "Dropbox" y campo de texto para el token</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Probar la Conexión</span></h4>
            <p>Antes de guardar, haga clic en el botón <strong>"Test Connection"</strong> o <strong>"Probar Conexión"</strong> para verificar que el token es válido.</p>

            <p>Verá uno de estos mensajes:</p>
            <ul>
                <li><strong class="text-success">✓ Conexión exitosa:</strong> El token es válido y SAMI puede acceder a Dropbox.</li>
                <li><strong class="text-danger">✗ Error de conexión:</strong> Revise que:
                    <ul>
                        <li>El token esté completo (sin espacios al inicio o final)</li>
                        <li>Los permisos estén configurados correctamente en Dropbox</li>
                        <li>El token sea reciente (generado después de configurar permisos)</li>
                    </ul>
                </li>
            </ul>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step8.png')}}" width="100%">
            <small>Muestra un alert verde con el mensaje "Conexión exitosa con Dropbox"</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Guardar Configuración</span></h4>
            <p>Una vez que la prueba de conexión sea exitosa, haga clic en <strong>"Guardar"</strong> o <strong>"Save"</strong> para aplicar la configuración.</p>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-lock text-success"></i>
                    Seguridad
                </div>
                <p class="mb-0">El token se almacena <strong>encriptado</strong> en la base de datos de SAMI. Nadie más que el sistema puede leerlo en texto plano.</p>
            </div>
        </div>
    </section>

    <!-- Section 6: Upload Files -->
    <section id="usar-sistema" class="content-section">
        <h2><i class="fas fa-file-upload me-2"></i>6. Subir Archivos Durante Consultas</h2>

        <p>Una vez configurado Dropbox, puede subir archivos durante las consultas médicas. Los archivos se almacenarán automáticamente en su cuenta de Dropbox.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a una Consulta</span></h4>
            <p>Abra o cree una consulta médica para un paciente. En la interfaz de consulta, encontrará la sección <strong>"Archivos de Consulta"</strong>.</p>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step9.png')}}" width="100%">
            <small>Muestra las diferentes secciones de la consulta con "Archivos de Consulta" visible</small>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Indicador de Dropbox
                </div>
                <p class="mb-0">Si Dropbox está configurado correctamente, verá un badge verde <span class="badge bg-success">✓ Dropbox conectado</span> junto al título de la sección.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Seleccionar Categoría</span></h4>
            <p>Antes de subir archivos, seleccione la categoría apropiada del dropdown:</p>

            <div class="field-table-wrapper">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Uso Recomendado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Categoría"><strong>Fotografía Clínica</strong></td>
                            <td data-label="Uso">Fotos de lesiones, heridas, o condiciones visibles del paciente</td>
                        </tr>
                        <tr>
                            <td data-label="Categoría"><strong>Imagen Diagnóstica</strong></td>
                            <td data-label="Uso">Rayos X, ultrasonidos, tomografías, resonancias magnéticas</td>
                        </tr>
                        <tr>
                            <td data-label="Categoría"><strong>Resultado de Laboratorio</strong></td>
                            <td data-label="Uso">Resultados de análisis de sangre, orina, biopsias, etc.</td>
                        </tr>
                        <tr>
                            <td data-label="Categoría"><strong>Prescripción</strong></td>
                            <td data-label="Uso">Recetas médicas, órdenes de medicamentos</td>
                        </tr>
                        <tr>
                            <td data-label="Categoría"><strong>Documento Clínico</strong></td>
                            <td data-label="Uso">Informes médicos, historias clínicas de otros centros</td>
                        </tr>
                        <tr>
                            <td data-label="Categoría"><strong>Documento de Referencia</strong></td>
                            <td data-label="Uso">Cartas de referencia a especialistas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Subir Archivos (Drag & Drop)</span></h4>
            <p>Puede subir archivos de dos formas:</p>

            <h5 class="mt-3"><i class="fas fa-hand-pointer me-2"></i>Método 1: Arrastrar y Soltar</h5>
            <ol>
                <li>Arrastre uno o varios archivos desde su computadora</li>
                <li>Suéltelos sobre el área de subida (cambiará de color al arrastrar)</li>
                <li>Verá una vista previa de las imágenes seleccionadas</li>
            </ol>

            <h5 class="mt-3"><i class="fas fa-mouse-pointer me-2"></i>Método 2: Click para Seleccionar</h5>
            <ol>
                <li>Haga clic en el área de subida</li>
                <li>Seleccione uno o varios archivos desde el explorador</li>
                <li>Verá una vista previa de las imágenes seleccionadas</li>
            </ol>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step10.png')}}" width="100%">
            <small>Muestra el área de subida con vista previa de imágenes y el contador "3 archivo(s) seleccionado(s)"</small>


            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Restricciones de Archivos
                </div>
                <ul class="mb-0">
                    <li><strong>Tipos permitidos:</strong> Solo imágenes (JPG, PNG, GIF)</li>
                    <li><strong>Tamaño máximo:</strong> 10 MB por archivo</li>
                    <li>Puede subir múltiples archivos a la vez</li>
                </ul>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Agregar Nota (Opcional)</span></h4>
            <p>En el campo de texto <strong>"Nota"</strong>, puede agregar un comentario descriptivo sobre los archivos. Por ejemplo:</p>
            <ul>
                <li>"Lesión en brazo derecho - consulta inicial"</li>
                <li>"Resultados de hemograma completo - 19/06/2026"</li>
                <li>"Radiografía de tórax PA - seguimiento neumonía"</li>
            </ul>
        </div>

        <div class="step-card">
            <h4><span class="step-number">5</span><span class="step-title">Subir a Dropbox</span></h4>
            <p>Haga clic en el botón <strong>"Subir Archivos"</strong>. El sistema:</p>
            <ol>
                <li>Validará que los archivos cumplan los requisitos</li>
                <li>Los subirá a Dropbox en la carpeta del paciente</li>
                <li>Mostrará un mensaje de éxito o error</li>
                <li>Agregará los archivos a la lista de "Archivos Subidos"</li>
            </ol>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step11.png')}}" width="100%">
            <small>Muestra el botón "Subir Archivos" con un spinner de carga</small>


            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-folder text-success"></i>
                    Estructura en Dropbox
                </div>
                <p class="mb-0">Los archivos se organizan automáticamente en Dropbox siguiendo esta estructura:<br>
                <code>/consultations/{ID_PACIENTE}/{ID_CONSULTA}/{TIMESTAMP}_{NOMBRE_ARCHIVO}</code></p>
            </div>
        </div>
    </section>

    <!-- Section 7: Manage Files -->
    <section id="gestionar-archivos" class="content-section">
        <h2><i class="fas fa-folder-open me-2"></i>7. Gestionar Archivos Subidos</h2>

        <p>Una vez que los archivos se han subido, puede visualizarlos, descargarlos o eliminarlos desde la misma interfaz de consulta.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Visualizar Archivos</span></h4>
            <p>En la sección <strong>"Archivos Subidos"</strong>, verá una lista en formato de tarjetas con todos los archivos de la consulta. Cada tarjeta muestra:</p>
            <ul>
                <li><strong>Vista previa de la imagen</strong> (si es una imagen)</li>
                <li><strong>Nombre del archivo</strong></li>
                <li><strong>Categoría</strong> (ej: "Fotografía Clínica")</li>
                <li><strong>Nota descriptiva</strong> (si se agregó)</li>
                <li><strong>Tamaño del archivo</strong> (ej: "2.5 MB")</li>
                <li><strong>Fecha y hora de subida</strong> (relativa, ej: "hace 5 minutos")</li>
            </ul>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step12.png')}}" width="100%">
            <small>Muestra 3-4 tarjetas con vista previa de imágenes y sus metadatos</small>

        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Ver Archivo Completo</span></h4>
            <p>Para ver el archivo en tamaño completo:</p>
            <ol>
                <li>Haga clic en el botón <strong>"Ver"</strong> en la tarjeta del archivo</li>
                <li>El archivo se abrirá en una nueva pestaña del navegador</li>
                <li>La URL es temporal y expira después de 60 minutos por seguridad</li>
            </ol>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-clock text-primary"></i>
                    URLs Temporales
                </div>
                <p class="mb-0">Por seguridad, las URLs de visualización son temporales. Si intenta acceder a un enlace después de 60 minutos, deberá hacer clic en "Ver" nuevamente para generar una nueva URL.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Eliminar Archivos</span></h4>
            <p>Para eliminar un archivo:</p>
            <ol>
                <li>Haga clic en el botón rojo <strong>"Eliminar"</strong> en la tarjeta del archivo</li>
                <li>Confirme la acción en el diálogo de confirmación</li>
                <li>El archivo se eliminará tanto de Dropbox como del registro de SAMI</li>
                <li>La acción es <strong>permanente y no se puede deshacer</strong></li>
            </ol>

            <!-- Espacio para captura de pantalla -->
            <img src="{{url('images/tutorial/external_storage/step13.png')}}" width="100%">
            <small>Muestra el mensaje "¿Está seguro de eliminar este archivo?" con botones Cancelar y Eliminar</small>


            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Advertencia de Eliminación
                </div>
                <p class="mb-0">Una vez eliminado, el archivo no se puede recuperar. Asegúrese de no necesitar el archivo antes de eliminarlo. El archivo se elimina tanto de SAMI como de Dropbox.</p>
            </div>
        </div>
    </section>

    <!-- Section 8: Tips and Security -->
    <section id="tips" class="content-section">
        <h2><i class="fas fa-lightbulb me-2"></i>8. Tips y Mejores Prácticas</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="info-box tip">
                    <div class="info-box-title">
                        <i class="fas fa-check text-success"></i>
                        Mejores Prácticas
                    </div>
                    <ul class="mb-0">
                        <li>Configure Dropbox antes de empezar a atender pacientes</li>
                        <li>Use nombres descriptivos para los archivos antes de subirlos</li>
                        <li>Agregue notas para identificar fácilmente el contenido</li>
                        <li>Revise periódicamente su espacio disponible en Dropbox</li>
                        <li>Mantenga su Access Token en un lugar seguro como respaldo</li>
                        <li>Categorice correctamente los archivos para mejor organización</li>
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
                        <li>No generar un nuevo token después de cambiar permisos</li>
                        <li>Compartir el Access Token con terceros</li>
                        <li>Subir archivos demasiado grandes (máx. 10MB)</li>
                        <li>No probar la conexión antes de guardar</li>
                        <li>Eliminar archivos sin confirmar que no se necesitan</li>
                    </ul>
                </div>
            </div>
        </div>

        <h3>Seguridad y Privacidad</h3>
        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-shield-alt text-primary"></i>
                Protección de Datos
            </div>
            <ul class="mb-0">
                <li><strong>Encriptación:</strong> El Access Token se almacena encriptado en la base de datos</li>
                <li><strong>Separación de datos:</strong> Cada cliente puede usar su propia cuenta de Dropbox</li>
                <li><strong>URLs temporales:</strong> Los enlaces de visualización expiran automáticamente</li>
                <li><strong>Auditoría:</strong> Todos los accesos a archivos quedan registrados</li>
                <li><strong>HIPAA/GDPR:</strong> Asegúrese de que su cuenta de Dropbox cumple con las regulaciones de su país</li>
            </ul>
        </div>

        <h3>Solución de Problemas Comunes</h3>
        <div class="field-table-wrapper">
            <table class="field-table">
                <thead>
                    <tr>
                        <th>Problema</th>
                        <th>Solución</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Problema"><strong>Error: "missing_scope"</strong></td>
                        <td data-label="Solución">Faltan permisos en Dropbox. Verifique que todos los permisos estén activados y genere un nuevo token.</td>
                    </tr>
                    <tr>
                        <td data-label="Problema"><strong>Error: "invalid_access_token"</strong></td>
                        <td data-label="Solución">El token es inválido o ha sido revocado. Genere un nuevo token en Dropbox y actualice la configuración.</td>
                    </tr>
                    <tr>
                        <td data-label="Problema"><strong>No se muestra vista previa</strong></td>
                        <td data-label="Solución">Las vistas previas solo funcionan para imágenes. Otros tipos de archivos mostrarán un ícono genérico.</td>
                    </tr>
                    <tr>
                        <td data-label="Problema"><strong>Formulario deshabilitado</strong></td>
                        <td data-label="Solución">No hay almacenamiento externo configurado. Complete la configuración de Dropbox primero.</td>
                    </tr>
                    <tr>
                        <td data-label="Problema"><strong>Error: "path_lookup/not_found"</strong></td>
                        <td data-label="Solución">El archivo no existe en Dropbox. Puede haber sido eliminado manualmente desde Dropbox.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Preguntas Frecuentes</h3>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        ¿Puedo usar mi cuenta personal de Dropbox?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sí, puede usar una cuenta personal. Sin embargo, para prácticas médicas se recomienda usar una cuenta de Dropbox Business para mayor capacidad de almacenamiento y funciones de seguridad adicionales.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ¿Qué pasa con los archivos si cambio de proveedor?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Los archivos permanecerán en su cuenta de Dropbox. Si cambia de proveedor, deberá migrar manualmente los archivos al nuevo servicio. SAMI mantendrá el registro de los archivos pero no podrá acceder a ellos hasta que configure el nuevo proveedor.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        ¿Cuánto espacio necesito en Dropbox?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Depende del volumen de pacientes y del tipo de archivos. Como referencia, una consulta promedio con 5 fotos clínicas ocupa aproximadamente 15-20 MB. Una cuenta gratuita de Dropbox (2 GB) puede almacenar alrededor de 100 consultas con archivos.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        ¿Los pacientes pueden ver los archivos en Dropbox?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No. Los archivos se almacenan en su cuenta privada de Dropbox. Solo usted y los usuarios autorizados de SAMI pueden acceder a ellos. Los pacientes no tienen acceso directo a su Dropbox.
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box note mt-4">
            <div class="info-box-title">
                <i class="fas fa-question-circle text-primary"></i>
                ¿Necesita Ayuda?
            </div>
            <p class="mb-0">Si tiene problemas configurando el almacenamiento externo o preguntas sobre el uso del sistema, contacte al equipo de soporte técnico de SAMI. También puede revisar las otras guías del Centro de Ayuda para aprender sobre funcionalidades relacionadas.</p>
        </div>
    </section>

        </div> <!-- End col-lg-8 -->
    </div> <!-- End row -->

    <!-- Navigation -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.settings') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Configuraciones
        </a>
        <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
            Centro de Ayuda <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
@stop
