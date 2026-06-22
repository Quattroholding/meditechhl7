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
    text-decoration: none;
}

.help-sidebar .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
    transform: translateX(5px);
}

.help-sidebar .nav-link.active {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

.help-sidebar .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Module header */
.module-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.module-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.module-header p {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
}

/* Table of Contents Card */
.toc-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8ecf1;
}

.toc-card h5 {
    color: #1a1f36;
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-list li {
    margin-bottom: 8px;
}

.toc-list a {
    color: #4a5568;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.toc-list a:hover {
    background: #f7fafc;
    color: #667eea;
    transform: translateX(4px);
}

.toc-list a i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    opacity: 0.7;
}

/* Content sections */
.content-section {
    background: white;
    border-radius: 12px;
    padding: 35px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e8ecf1;
}

.content-section h2 {
    color: #1a1f36;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
    font-size: 1.75rem;
}

.content-section h3 {
    color: #2d3748;
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.content-section p {
    color: #4a5568;
    line-height: 1.8;
    margin-bottom: 15px;
}

/* Step Cards */
.step-card {
    background: #f7fafc;
    border-left: 4px solid #667eea;
    padding: 25px;
    margin: 25px 0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.step-card:hover {
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
    transform: translateX(5px);
}

.step-card h4 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-weight: 700;
    font-size: 1.1rem;
}

.step-title {
    flex: 1;
}

/* Feature Cards */
.feature-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    border: 1px solid #e8ecf1;
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.feature-card .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.feature-card .icon-circle i {
    font-size: 1.8rem;
}

.feature-card h5 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.feature-card p {
    color: #718096;
    margin: 0;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* Info Boxes */
.info-box {
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    border-left: 4px solid;
}

.info-box.note {
    background: #ebf8ff;
    border-color: #3182ce;
}

.info-box.tip {
    background: #f0fff4;
    border-color: #38a169;
}

.info-box.warning {
    background: #fffaf0;
    border-color: #dd6b20;
}

.info-box.danger {
    background: #fff5f5;
    border-color: #e53e3e;
}

.info-box-title {
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    font-size: 1rem;
}

.info-box-title i {
    margin-right: 10px;
    font-size: 1.2rem;
}

.info-box ul {
    margin: 10px 0;
    padding-left: 20px;
}

.info-box li {
    margin-bottom: 8px;
}

/* Code blocks */
.code-block {
    background: #2d3748;
    color: #63b3ed;
    padding: 15px 20px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    overflow-x: auto;
    margin: 15px 0;
    border: 1px solid #4a5568;
}

code {
    background: #edf2f7;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    color: #e53e3e;
}

/* Tables */
.field-table-wrapper {
    overflow-x: auto;
    margin: 20px 0;
}

.field-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.field-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.field-table th {
    padding: 15px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
}

.field-table td {
    padding: 15px 20px;
    border-bottom: 1px solid #e8ecf1;
    color: #4a5568;
}

.field-table tbody tr:last-child td {
    border-bottom: none;
}

.field-table tbody tr:hover {
    background: #f7fafc;
}

/* Badges */
.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge.bg-primary {
    background: #667eea !important;
    color: white;
}

.required-badge {
    background: #e53e3e;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Images */
.content-section img {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin: 20px 0;
    border: 1px solid #e8ecf1;
}

.content-section small {
    color: #718096;
    font-style: italic;
    display: block;
    margin-top: -15px;
    margin-bottom: 20px;
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 992px) {
    .help-content {
        margin-left: 0;
        max-width: 100%;
        padding: 20px;
    }

    .help-sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .module-header h1 {
        font-size: 1.8rem;
    }

    .toc-card {
        position: relative !important;
        margin-bottom: 20px;
    }

    .field-table {
        font-size: 0.85rem;
    }

    .field-table thead {
        display: none;
    }

    .field-table td {
        display: block;
        text-align: right;
        padding: 10px;
        position: relative;
        padding-left: 50%;
    }

    .field-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: 600;
        text-align: left;
    }
}

@endsection

@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-cloud-upload-alt me-3"></i>Almacenamiento Externo de Archivos</h1>
        <p>Configure Dropbox para almacenar archivos de consultas médicas en la nube de forma segura con renovación automática de acceso.</p>
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
                    <li><a href="#preparacion"><i class="fab fa-dropbox"></i> 2. Preparación</a></li>
                    <li><a href="#conectar-dropbox"><i class="fas fa-link"></i> 3. Conectar Dropbox</a></li>
                    <li><a href="#subir-archivos"><i class="fas fa-file-upload"></i> 4. Subir Archivos</a></li>
                    <li><a href="#gestionar-archivos"><i class="fas fa-folder-open"></i> 5. Gestionar Archivos</a></li>
                    <li><a href="#tips"><i class="fas fa-lightbulb"></i> 6. Tips y Problemas</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-8">

    <!-- Section 1: Introduction -->
    <section id="introduccion" class="content-section">
        <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Almacenamiento Externo</h2>

        <p>El módulo de <strong>Almacenamiento Externo</strong> permite integrar SAMI con Dropbox para almacenar archivos de consultas médicas (fotografías clínicas, imágenes diagnósticas, resultados de laboratorio, etc.) de forma segura en la nube.</p>

        <div class="info-box danger">
            <div class="info-box-title">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                Requisito Obligatorio
            </div>
            <p class="mb-0"><strong>El almacenamiento externo es OBLIGATORIO</strong> para subir archivos en SAMI. No es posible almacenar archivos en el servidor local. Debe configurar Dropbox antes de poder subir cualquier archivo durante las consultas.</p>
        </div>

        <h3>Arquitectura Multi-Tenant: Cada Cliente Su Propio Dropbox</h3>
        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-building text-success"></i>
                ¿Cómo funciona con múltiples clientes?
            </div>
            <div class="mb-3">
                <strong>Una sola app de Dropbox en el servidor</strong> (configurada por el administrador de SAMI), pero <strong>cada cliente conecta su propia cuenta</strong> de Dropbox:
            </div>
            <ul class="mb-3">
                <li><strong>Clínica A</strong> → Conecta su Dropbox personal → Archivos en el Dropbox de Clínica A</li>
                <li><strong>Clínica B</strong> → Conecta su Dropbox personal → Archivos en el Dropbox de Clínica B</li>
                <li><strong>Clínica C</strong> → Conecta su Dropbox personal → Archivos en el Dropbox de Clínica C</li>
            </ul>
            <div class="alert alert-success mb-0" style="background: rgba(76, 175, 80, 0.1); border-left: 4px solid #4caf50; padding: 12px;">
                <strong>✓ Aislamiento total:</strong> Los archivos de cada clínica están completamente separados en sus propias cuentas de Dropbox. Una clínica no puede ver los archivos de otra.
            </div>
        </div>

        <h3>Beneficios del Almacenamiento Externo con OAuth 2.0</h3>
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-shield-alt" style="color: #2196f3;"></i>
                    </div>
                    <h5>Seguridad Mejorada</h5>
                    <p>Conexión OAuth 2.0 con tokens que se renuevan automáticamente sin intervención manual.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-sync-alt" style="color: #4caf50;"></i>
                    </div>
                    <h5>Renovación Automática</h5>
                    <p>Los tokens se actualizan automáticamente antes de expirar. Sin interrupciones en el servicio.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-globe" style="color: #ff9800;"></i>
                    </div>
                    <h5>Acceso Universal</h5>
                    <p>Acceda a los archivos médicos desde cualquier dispositivo con conexión a internet.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(156, 39, 176, 0.1);">
                        <i class="fas fa-lock" style="color: #9c27b0;"></i>
                    </div>
                    <h5>Privacidad Total</h5>
                    <p>Cada cliente usa su propia cuenta de Dropbox con datos completamente separados.</p>
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

    <!-- Section 2: Preparation -->
    <section id="preparacion" class="content-section">
        <h2><i class="fab fa-dropbox me-2"></i>2. Preparación: Lo que Necesitas</h2>

        <p>Antes de conectar Dropbox con SAMI, asegúrate de tener lo siguiente:</p>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fab fa-dropbox" style="color: #0061FF;"></i>
                    </div>
                    <h5>Cuenta de Dropbox</h5>
                    <p>Necesitas tener una cuenta de Dropbox (gratuita o de pago). Si no tienes una, créala en <a href="https://www.dropbox.com" target="_blank">dropbox.com</a></p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="feature-card">
                    <div class="icon-circle" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-user-shield" style="color: #4caf50;"></i>
                    </div>
                    <h5>Permisos de Administrador</h5>
                    <p>Debes ser <strong>administrador</strong> de tu clínica en SAMI para poder configurar Dropbox.</p>
                </div>
            </div>
        </div>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-info-circle text-success"></i>
                No Necesitas Crear una App
            </div>
            <p class="mb-0">La aplicación de Dropbox ya está configurada por el administrador de SAMI. Tú solo necesitas <strong>conectar tu cuenta de Dropbox</strong> con un clic. Es muy simple.</p>
        </div>

        <div class="info-box warning mt-3">
            <div class="info-box-title">
                <i class="fas fa-cloud text-warning"></i>
                Espacio en Dropbox
            </div>
            <p class="mb-0">Asegúrate de tener suficiente espacio disponible en tu cuenta de Dropbox para almacenar los archivos médicos. Las cuentas gratuitas tienen 2GB, las de pago tienen más espacio.</p>
        </div>
    </section>

    <!-- Section 3: Connect Dropbox -->
    <section id="conectar-dropbox" class="content-section">
        <h2><i class="fas fa-link me-2"></i>3. Conectar Tu Cuenta de Dropbox</h2>

        <p>Sigue estos pasos para conectar tu cuenta de Dropbox con SAMI. El proceso toma menos de 2 minutos.</p>

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

            <img src="{{url('images/tutorial/external_storage/step7.png')}}" width="100%">
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
            <h4><span class="step-number">2</span><span class="step-title">Hacer Clic en "Conectar con Dropbox"</span></h4>
            <p>En la página de configuración, verá un botón azul grande que dice <strong>"Conectar con Dropbox"</strong>. Haga clic en él.</p>

            <img src="{{url('images/tutorial/external_storage/step8.png')}}" width="100%">
            <small>Muestra el botón "Conectar con Dropbox" con el icono de Dropbox</small>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-magic text-success"></i>
                    Sin Tokens Manuales
                </div>
                <p class="mb-0">Ya no necesita copiar y pegar tokens manualmente. OAuth 2.0 maneja todo automáticamente de forma segura.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Autorizar en Dropbox</span></h4>
            <p>Será redirigido a la página de Dropbox donde se le pedirá autorizar a SAMI para acceder a su cuenta.</p>

            <ol>
                <li>Revise los permisos solicitados (deben coincidir con los configurados)</li>
                <li>Haga clic en <strong>"Allow"</strong> o <strong>"Permitir"</strong></li>
                <li>Será redirigido automáticamente de vuelta a SAMI</li>
            </ol>

            <img src="{{url('images/tutorial/external_storage/step9.png')}}" width="100%">
            <small>Muestra la pantalla de autorización de Dropbox con el botón "Allow"</small>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Verificar Conexión Exitosa</span></h4>
            <p>Después de autorizar, verá un mensaje de éxito y la configuración mostrará:</p>

            <ul>
                <li><strong>Estado</strong>: "Dropbox Conectado" con ícono verde ✓</li>
                <li><strong>Cuenta</strong>: ID de su cuenta de Dropbox</li>
                <li><strong>Token válido hasta</strong>: Fecha de expiración del token actual</li>
                <li><strong>Renovación automática</strong>: Confirmación de que se auto-renovará</li>
            </ul>

            <img src="{{url('images/tutorial/external_storage/step10.png')}}" width="100%">
            <small>Muestra el estado "Conectado" con información del token y renovación automática</small>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-sync-alt text-success"></i>
                    Renovación Automática
                </div>
                <p class="mb-0">El sistema renovará automáticamente los tokens antes de que expiren. <strong>No necesitará reconectarse manualmente</strong> nunca más.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">5</span><span class="step-title">Habilitar Almacenamiento</span></h4>
            <p>Con Dropbox ya conectado, active el toggle <strong>"Almacenamiento Externo Activo"</strong> para comenzar a usar el almacenamiento en la nube.</p>

            <p>Cuando esté activo:</p>
            <ul>
                <li>✓ Todos los archivos nuevos se guardarán en Dropbox</li>
                <li>✓ Los archivos existentes permanecen donde están</li>
                <li>✓ Puede desactivarlo temporalmente si lo necesita</li>
            </ul>

            <img src="{{url('images/tutorial/external_storage/step11.png')}}" width="100%">
            <small>Muestra el toggle "Almacenamiento Externo Activo" habilitado</small>
        </div>
    </section>

    <!-- Section 4: Upload Files -->
    <section id="subir-archivos" class="content-section">
        <h2><i class="fas fa-file-upload me-2"></i>4. Subir Archivos de Consulta</h2>

        <p>Una vez configurado el almacenamiento externo, puede subir archivos durante las consultas médicas.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Acceder a una Consulta</span></h4>
            <p>Navegue a <strong>Consultas → Ver/Editar Consulta</strong> o cree una nueva consulta.</p>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Ir a Sección de Archivos</span></h4>
            <p>En la consulta, busque la sección <strong>"Archivos de Consulta"</strong> o <strong>"Subir Archivos"</strong>.</p>

            <img src="{{url('images/tutorial/external_storage/step12.png')}}" width="100%">
            <small>Muestra la sección de archivos con el indicador "Dropbox conectado" en verde</small>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Seleccionar Categoría y Archivos</span></h4>
            <p>Complete el formulario de subida:</p>

            <ol>
                <li><strong>Categoría</strong>: Seleccione el tipo (Fotografía Clínica, Imagen Diagnóstica, Resultado de Laboratorio, etc.)</li>
                <li><strong>Archivos</strong>: Arrastre archivos o haga clic para seleccionar desde su dispositivo</li>
                <li><strong>Nota</strong> (opcional): Agregue una descripción del archivo</li>
            </ol>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Archivos Permitidos
                </div>
                <p class="mb-0">
                    <strong>Formatos</strong>: Solo imágenes (JPG, PNG, GIF)<br>
                    <strong>Tamaño máximo</strong>: 10MB por archivo<br>
                    <strong>Cantidad</strong>: Puede subir múltiples archivos a la vez
                </p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">4</span><span class="step-title">Subir a Dropbox</span></h4>
            <p>Haga clic en <strong>"Subir Archivos"</strong>. Los archivos se cargarán directamente a Dropbox y verá:</p>

            <ul>
                <li>Barra de progreso durante la subida</li>
                <li>Mensaje de éxito al completar</li>
                <li>Preview de los archivos subidos</li>
            </ul>

            <img src="{{url('images/tutorial/external_storage/step13.png')}}" width="100%">
            <small>Muestra archivos subiendo con barra de progreso</small>
        </div>

        <div class="step-card">
            <h4><span class="step-number">5</span><span class="step-title">Verificar en Dropbox</span></h4>
            <p>Los archivos se organizan automáticamente en Dropbox con la siguiente estructura:</p>

            <div class="code-block">
/consultations/
  /{patient_id}/
    /{encounter_id}-{nombre_paciente}/
      /{timestamp}_{nombre_archivo}.jpg
            </div>

            <p class="mt-3">Ejemplo real:</p>
            <div class="code-block">
/consultations/42/789-Maria_Rodriguez/20260619153045_rayos_x_torax.jpg
            </div>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-folder-tree text-success"></i>
                    Organización Inteligente
                </div>
                <p class="mb-0">Cada paciente tiene su carpeta, y dentro de ella, cada consulta tiene su subcarpeta con el nombre del paciente para fácil identificación.</p>
            </div>
        </div>
    </section>

    <!-- Section 5: Manage Files -->
    <section id="gestionar-archivos" class="content-section">
        <h2><i class="fas fa-folder-open me-2"></i>5. Gestionar Archivos Subidos</h2>

        <p>Puede visualizar, descargar y eliminar archivos directamente desde SAMI.</p>

        <div class="step-card">
            <h4><span class="step-number">1</span><span class="step-title">Ver Lista de Archivos</span></h4>
            <p>En la consulta, verá todos los archivos subidos con:</p>

            <ul>
                <li>Preview de imagen (si es imagen)</li>
                <li>Nombre del archivo</li>
                <li>Categoría (badge de color)</li>
                <li>Nota descriptiva (si se agregó)</li>
                <li>Tamaño del archivo</li>
                <li>Fecha de subida</li>
            </ul>

            <img src="{{url('images/tutorial/external_storage/step14.png')}}" width="100%">
            <small>Muestra la lista de archivos en formato tarjetas con previews</small>
        </div>

        <div class="step-card">
            <h4><span class="step-number">2</span><span class="step-title">Visualizar Archivos</span></h4>
            <p>Haga clic en el botón <strong>"Ver"</strong> para abrir el archivo en una nueva pestaña. El sistema generará automáticamente una URL temporal de Dropbox que expira en 60 minutos por seguridad.</p>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-clock text-primary"></i>
                    URLs Temporales
                </div>
                <p class="mb-0">Las URLs de visualización son temporales por seguridad. Si vuelve a ver el archivo más tarde, se generará una nueva URL automáticamente.</p>
            </div>
        </div>

        <div class="step-card">
            <h4><span class="step-number">3</span><span class="step-title">Eliminar Archivos</span></h4>
            <p>Para eliminar un archivo:</p>

            <ol>
                <li>Haga clic en el botón rojo <strong>"Eliminar"</strong></li>
                <li>Confirme la acción en el diálogo de confirmación</li>
                <li>El archivo se eliminará de Dropbox y de SAMI</li>
            </ol>

            <div class="info-box danger">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Eliminación Permanente
                </div>
                <p class="mb-0">Los archivos eliminados <strong>NO se pueden recuperar</strong> desde SAMI. Asegúrese de que realmente desea eliminar el archivo antes de confirmar.</p>
            </div>
        </div>
    </section>

    <!-- Section 6: Tips and Security -->
    <section id="tips" class="content-section">
        <h2><i class="fas fa-lightbulb me-2"></i>6. Tips y Resolución de Problemas</h2>

        <h3>✅ Mejores Prácticas</h3>
        <div class="info-box tip">
            <ul class="mb-0">
                <li>Use una cuenta de Dropbox exclusiva para su clínica (no personal)</li>
                <li>Habilite autenticación de dos factores en su cuenta Dropbox</li>
                <li>Categorice correctamente los archivos para fácil búsqueda</li>
                <li>Agregue notas descriptivas a archivos importantes</li>
                <li>Revise periódicamente el espacio disponible en Dropbox</li>
                <li>Mantenga las credenciales OAuth seguras en el servidor</li>
                <li>No comparta el App Secret con terceros</li>
            </ul>
        </div>

        <h3 class="mt-4">🔒 Seguridad y Privacidad</h3>
        <div class="info-box note">
            <div class="info-box-title">
                <i class="fas fa-shield-alt text-primary"></i>
                Medidas de Seguridad Implementadas
            </div>
            <ul class="mb-0">
                <li><strong>Tokens encriptados</strong>: Los access tokens y refresh tokens se guardan encriptados en la base de datos</li>
                <li><strong>Renovación automática</strong>: Los tokens se renuevan antes de expirar sin exponer credenciales</li>
                <li><strong>URLs temporales</strong>: Los enlaces de visualización expiran en 60 minutos</li>
                <li><strong>Aislamiento multi-tenant</strong>: Cada cliente tiene sus propias credenciales y archivos separados</li>
                <li><strong>Audit logs</strong>: Todas las operaciones quedan registradas para auditoría</li>
                <li><strong>HTTPS obligatorio</strong>: Todas las comunicaciones están cifradas en producción</li>
            </ul>
        </div>

        <h3 class="mt-4">🔧 Resolución de Problemas</h3>

        <div class="step-card">
            <h4>Error: "Token expirado" o "expired_access_token"</h4>
            <p><strong>Causa</strong>: El refresh token también expiró (muy poco común).</p>
            <p><strong>Solución</strong>:</p>
            <ol>
                <li>Vaya a Configuración → Almacenamiento Externo</li>
                <li>Click en "Desconectar Dropbox"</li>
                <li>Click en "Conectar con Dropbox" nuevamente</li>
                <li>Autorice de nuevo en Dropbox</li>
            </ol>
        </div>

        <div class="step-card">
            <h4>Error: "redirect_uri_mismatch"</h4>
            <p><strong>Causa</strong>: La URL de callback configurada en Dropbox no coincide con la de su servidor.</p>
            <p><strong>Solución</strong>:</p>
            <ol>
                <li>Verifique la Redirect URI en Dropbox App Console → Settings → OAuth 2</li>
                <li>Debe ser exactamente: <code>https://su-dominio.com/settings/dropbox/callback</code></li>
                <li>Incluya https:// si usa SSL (recomendado)</li>
                <li>Verifique que no haya espacios extra al inicio o final</li>
            </ol>
        </div>

        <div class="step-card">
            <h4>Error: "No se puede subir archivos"</h4>
            <p><strong>Causa</strong>: Almacenamiento externo deshabilitado o no configurado.</p>
            <p><strong>Solución</strong>:</p>
            <ol>
                <li>Verifique que Dropbox esté conectado (estado verde)</li>
                <li>Verifique que el toggle "Almacenamiento Externo Activo" esté habilitado</li>
                <li>Si ve mensaje de error sobre credenciales, reconéctese con Dropbox</li>
            </ol>
        </div>

        <div class="step-card">
            <h4>Las imágenes no se visualizan (src="#")</h4>
            <p><strong>Causa</strong>: Error al generar URL temporal desde Dropbox.</p>
            <p><strong>Solución</strong>:</p>
            <ol>
                <li>Refresque la página (el sistema generará nueva URL)</li>
                <li>Verifique que los permisos de Dropbox incluyan <code>sharing.write</code> y <code>sharing.read</code></li>
                <li>Si persiste, desconecte y vuelva a conectar Dropbox</li>
            </ol>
        </div>

        <div class="info-box warning">
            <div class="info-box-title">
                <i class="fas fa-tools text-warning"></i>
                Soporte Técnico
            </div>
            <p class="mb-0">Si los problemas persisten después de seguir estos pasos, contacte a soporte técnico con:<br>
            1. Descripción del error exacto<br>
            2. Captura de pantalla del mensaje de error<br>
            3. Pasos que realizó antes del error</p>
        </div>

        <h3 class="mt-4">📊 Monitoreo</h3>
        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-chart-line text-success"></i>
                Verificación de Salud del Sistema
            </div>
            <p class="mb-0">El sistema registra automáticamente en los logs cuando:<br>
            • Se renuevan tokens automáticamente<br>
            • Se suben archivos exitosamente<br>
            • Ocurren errores de conexión<br>
            <br>
            Su administrador de sistemas puede revisar <code>storage/logs/laravel.log</code> para monitoreo detallado.</p>
        </div>

        <div class="info-box note mt-4">
            <div class="info-box-title">
                <i class="fas fa-graduation-cap text-primary"></i>
                ¿Necesita Más Ayuda?
            </div>
            <p class="mb-0">Para asistencia adicional:<br>
            • Consulte la <a href="{{route('help.index')}}" target="_blank">documentación completa</a><br>
            • Contacte al equipo de soporte<br>
            • Revise la <a href="https://www.dropbox.com/developers/documentation" target="_blank">documentación de Dropbox</a> (en inglés)</p>
        </div>
    </section>

        </div>
    </div>
@stop

@section('sidebar')
    @include('help.sidebar', ['active' => 'external-storage'])
@endsection
