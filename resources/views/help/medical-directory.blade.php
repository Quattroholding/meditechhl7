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
    background: linear-gradient(180deg, #1a237e 0%, #3949ab 100%);
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
    background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
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
    color: #1a237e;
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e8eaf6;
}

.content-section ul {
    line-height: 1.8;
    color: #555;
}

.content-section li {
    margin-bottom: 10px;
}

/* Doctor Card Demo */
.doctor-card-demo {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    background: #f9f9f9;
    max-width: 400px;
    margin: 20px auto;
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

.info-box.tip {
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
}

.info-box.tip i {
    color: #388e3c;
}

/* Screenshot Placeholder */
.screenshot-placeholder {
    background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
    border: 2px dashed #1a237e;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin: 20px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.screenshot-placeholder i {
    font-size: 3rem;
    color: #1a237e;
    margin-bottom: 15px;
}

.screenshot-placeholder p {
    color: #1a237e;
    font-weight: 500;
    margin-bottom: 5px;
}

.screenshot-placeholder small {
    color: #1a237e;
}

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(26, 35, 126, 0.4);
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
    z-index: 9999;
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

@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'medical-directory'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Directorio Médico</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-address-book me-3"></i>Directorio Médico</h1>
        <p>Encuentre información detallada sobre los profesionales de la salud registrados en el sistema y gestione sus citas.</p>
    </div>
@stop
@section('table-content')
    <div class="content-section">
        <h2><i class="fas fa-info-circle me-2"></i>Información de la Tarjeta Médica</h2>
        <p>Cada médico en el directorio se presenta en una tarjeta que contiene la siguiente información detallada:</p>

        <div>
            <img src="{{ asset('images/tutorial/medical_directory/medd-list.png') }}" alt="" style="width: 100%;">
        </div>

        <ul>
            <li><strong>Nombre del Profesional:</strong> Nombre completo.</li>
            <li><strong>ID / Identificación:</strong> Número de identificación personal.</li>
            <li><strong>Estado:</strong> Indica si el profesional está actualmente <span class="badge bg-success">Activo</span> para recibir citas.</li>
            <li><strong>Avatar / Foto:</strong> Imagen de perfil del profesional para facilitar su identificación.</li>
            <li><strong>Información de Contacto:</strong> Teléfono y correo electrónico <!--(si el profesional ha autorizado su visualización)-->.</li>
            <li><strong>Horario Laboral:</strong> Detalle de los días y horas de atención, organizado por sedes o sucursales.</li>
            <li><strong>Especialidades:</strong> Lista de especialidades médicas avaladas por el profesional.</li>
        </ul>

        <div class="info-box tip">
            <i class="fas fa-magic"></i>
            <div>
                <p class="mb-0"><strong>Dato importante:</strong> Todos los doctores que se registran como clientes activos en la plataforma <strong>SAMI</strong> aparecen automáticamente en este directorio para ser consultados por los pacientes.</p>
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2><i class="fas fa-search me-2"></i>Búsqueda y Filtros</h2>
        <p>Para facilitar la localización de un profesional, el directorio ofrece potentes herramientas de filtrado:</p>
        <ul>
            <li><strong>Búsqueda por Texto:</strong> Filtre por nombre, apellido o número de cédula.</li>
            <li><strong>Filtro por Especialidad:</strong> Seleccione una especialidad específica para ver solo a los expertos en esa área.</li>
            <li><strong>Limpiar Filtros:</strong> Restablezca todos los criterios de búsqueda con un solo clic.</li>
        </ul>
    </div>

    <div class="content-section">
        <h2><i class="fas fa-calendar-alt me-2"></i>Solicitud de Citas</h2>
        <p>Si usted ha ingresado al sistema con el rol de <strong>Paciente</strong>, verá un botón de acción en cada tarjeta para solicitar una cita directamente con el profesional seleccionado, facilitando el acceso a la salud. <br>
        Si usted ha ingresado al sistema con el rol de <strong>Médico</strong> puede hacer una referencia a otro especialista si el paciente lo necesita, dentro de la plantilla de consulta.</p>
    </div>
@stop
