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
    background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
    color: #fff;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(2, 136, 209, 0.3);
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
    border-bottom: 2px solid #e1f5fe;
    padding-bottom: 10px;
    color: #0288d1;
}

/* Step Cards */
.step-card {
    background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
    border-left: 4px solid #0288d1;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
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
    background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.3rem;
    box-shadow: 0 4px 10px rgba(2, 136, 209, 0.4);
}

.step-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #0288d1;
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
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
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

.info-box-title {
    font-weight: 600;
    margin-bottom: 10px;
}

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(2, 136, 209, 0.4);
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

    .step-card, .sub-step {
        border-left: none;
        padding: 25px;
        border-bottom: 5px solid #0288d1;
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
    @include('help.sidebar', ['active' => 'profile'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Perfil de Usuario</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <header class="module-header">
        <h1><i class="fas fa-user-circle me-3"></i>Perfil de Usuario</h1>
        <p>Gestiona tu información personal, seguridad y preferencias de SAMI.</p>
    </header>
@stop
@section('table-content')
    <!-- 1. Encabezado de Perfil -->
    <div class="content-section">
        <h2>Encabezado de Perfil</h2>
        <p>El cintillo azul en la parte superior muestra un resumen rápido de tu identidad y actividad en la plataforma.</p>
        <div class="step-card">
            <div class="step-content">
                <ul>
                    <li><strong>Información Personal:</strong> Muestra tu nombre completo, tipo y número de documento, correo electrónico y teléfono.</li>
                    <li><strong>Estadísticas:</strong> Indica el total de citas agendadas y consultas finalizadas a la fecha.</li>
                    <li><strong>Agregar Especialidad:</strong> Botón directo para gestionar tus cualificaciones médicas.</li>
                </ul>
            </div>
                <div>
                     <img src="{{ asset('images/tutorial/profile/profile_header.png') }}" alt="" style="width: 100%;">
                </div>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Acerca de mí y Especialidades -->
        <div class="col-lg-4">
            <!-- 2. Acerca de mí -->
            <div class="content-section">
                <h2>Acerca de mí</h2>
                <p>Información demográfica y de registro profesional.</p>
                <div class="step-card">
                    <div class="step-content">
                        <ul class="mb-0">
                            <li><strong>Género y Nacimiento:</strong> Datos básicos de identificación.</li>
                            <li><strong>Registro y Licencia:</strong> Tus códigos de identificación profesional.</li>
                            <li><strong>Último ingreso:</strong> Registro de tu última sesión.</li>
                        </ul>
                    </div>
                <div>
                     <img src="{{ asset('images/tutorial/profile/profile_aboutme.png') }}" alt="" style="width: 100%;">
                </div>
                </div>
            </div>

            <!-- 3. Especialidades -->
            <div class="content-section">
                <h2>Especialidades</h2>
                <p>Gestión de tus áreas de especialización médica.</p>
                <div class="step-card">
                    <div class="step-content">
                        <ul class="mb-0">
                            <li><strong>Listado:</strong> Visualiza tus especialidades y su periodo de vigencia.</li>
                            <li><strong>Especialidad Principal:</strong> Elige cuál aparecerá destacada en las búsquedas de pacientes.</li>
                        </ul>
                    </div>
                <div>
                     <img src="{{ asset('images/tutorial/profile/profile_specialities.png') }}" alt="" style="width: 100%;">
                </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Configuración de Perfil y Acceso -->
        <div class="col-lg-8">
            <!-- 4. Configuración de Perfil -->
            <div class="content-section">
                <h2>Configuración de Perfil</h2>
                <p>Actualiza tus datos básicos que aparecen en los documentos generados.</p>
                <div class="step-card">
                    <div class="step-content">
                        <p>Modifica tu nombre, teléfono y dirección. Asegúrate de mantener estos datos actualizados.</p>
                        <p class="text-primary"><strong><i class="fas fa-save"></i> Importante:</strong> Debes hacer clic en <strong>"ACTUALIZAR"</strong> para guardar los cambios.</p>
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/profile/profile_data.png') }}" alt="Datos de perfil" class="img-fluid rounded border shadow-sm">
                    </div>
                </div>
            </div>

            <!-- 5. Configuración de acceso -->
            <div class="content-section">
                <h2>Configuración de acceso</h2>
                <p>Cambia tu contraseña periódicamente para mantener tu cuenta segura.</p>
                <div class="step-card">
                    <div class="step-content">
                        <p>Introduce tu clave actual y la nueva contraseña (dos veces).</p>
                        <div class="info-box py-2 my-2">
                            <p class="mb-0 small"><i class="fas fa-shield-alt me-2"></i><strong>Requisito:</strong> Mínimo <strong>8 caracteres</strong>.</p>
                        </div>
                        <p class="text-primary"><strong><i class="fas fa-save"></i> Importante:</strong> Haz clic en <strong>"ACTUALIZAR"</strong> al finalizar.</p>
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/profile/profile_pw.png') }}" alt="Seguridad" class="img-fluid rounded border shadow-sm">
                    </div>
                </div>

                <!-- Nueva sección de 2FA -->
                <div class="step-card" style="border-left-color: #1a237e; background: linear-gradient(135deg, #e8eaf6 0%, #fff 100%);">
                    <div class="step-content">
                        <h5><i class="fas fa-shield-alt me-2 text-primary"></i>Autenticación de Dos Factores (2FA) - <strong>Obligatorio</strong></h5>
                        <p>SAMI ahora requiere autenticación de dos factores para todos los usuarios. El sistema te guiará automáticamente para vincular tu celular en tu próximo inicio de sesión.</p>
                        <a href="{{ route('help.2fa') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-book-open me-1"></i> Ver Guía de Configuración Obligatoria
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 6. Firma y Sello Digital -->
        <div class="col-lg-7">
            <div class="content-section h-100">
                <h2>Firma y Sello Digital</h2>
                <p>Esencial para la validez legal de recetas, incapacidades e <strong>HISTORIA CLÍNICA</strong>.</p>
                <div class="step-card">
                    <div class="step-content">
                        <p>Sube imágenes (JPG, PNG, GIF, SVG) de hasta 2MB.</p>
                        <ul>
                            <li><strong>Carga:</strong> Haz clic en <strong>"Subir Firma"</strong> o <strong>"Subir Sello"</strong> tras elegir el archivo.</li>
                            <li><strong>Gestión:</strong> Usa <strong>"Eliminar"</strong> para quitar o selecciona un nuevo archivo para <strong>"Reemplazar"</strong>.</li>
                        </ul>
                    </div>
                    <div>
                        <img src="{{ asset('images/tutorial/profile/profile_sign.png') }}" alt="Firma y sello" class="img-fluid rounded border shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Código de Referidos -->
        <div class="col-lg-5">
            <div class="content-section h-100">
                <h2>Tu Código de Referido</h2>
                <p>Gana beneficios invitando a otros colegas a unirse a SAMI.</p>
                <div class="step-card">
                    <div class="step-content">
                        <p>Gana crédito en tu próxima factura por cada nuevo cliente registrado con tu código.</p>
                        <ul>
                            <li><strong>Código y Enlace:</strong> Copia y comparte tu identificador único.</li>
                            <li><strong>QR Imprimible:</strong> Descarga un PDF con código QR para tu consultorio.</li>
                            <li><strong>Seguimiento:</strong> Revisa tu total de referidos exitosos.</li>
                        </ul>
                    </div>
                <div>
                     <img src="{{ asset('images/tutorial/profile/profile_referralcode.png') }}" alt="" style="width: 100%;">
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-title"><strong><i class="fas fa-lightbulb"></i> Tip de Seguridad</strong></div>
        <p class="mb-0">Nunca compartas tus credenciales de acceso. SAMI registra todas las acciones por usuario para garantizar la trazabilidad clínica y administrativa.</p>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.settings') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Configuraciones
        </a>
        <a href="{{ route('help.subscriptions') }}" class="btn btn-primary btn-lg">
            Suscripciones <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
@stop
