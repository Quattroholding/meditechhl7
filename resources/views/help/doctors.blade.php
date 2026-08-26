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
    background: linear-gradient(135deg, #55acee 0%, #007bb5 100%);
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

/* Content Sections */
.content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.content-section h2 {
    border-bottom: 2px solid #e3f2fd;
    padding-bottom: 10px;
    color: #55acee;
    margin-bottom: 20px;
}

/* Step Cards */
.step-card {
    background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
    border-left: 4px solid #55acee;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
}

.step-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #007bb5;
    margin-bottom: 10px;
}

.step-content {
    color: #555;
    line-height: 1.8;
}

.step-content ul {
    margin-top: 10px;
    margin-bottom: 10px;
}

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #55acee 0%, #007bb5 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(85, 172, 238, 0.4);
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

/* Navigation buttons */
.d-flex.justify-content-between {
    margin-top: 30px;
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
    @include('help.sidebar', ['active' => 'doctors'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Doctores</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-user-md me-3"></i>Módulo de Doctores</h1>
        <p>Gestión y administración del personal médico registrado en el sistema.</p>
    </div>
@stop
@section('table-content')
    <div class="content-section">
        <h2>Lista de Doctores</h2>
        <p>Este módulo permite visualizar y gestionar a todos los profesionales de la salud vinculados a tu organización.</p>

        <div class="step-card">
            <div class="step-title"><strong>Visualización de Personal</strong></div>
            <div class="step-content">
                <p>En la tabla principal podrás ver:</p>
                <ul>
                    <li>Nombre y Especialidad del doctor.</li>
                    <li>Información de contacto (correo y teléfono).</li>
                    <li>Información personal (fecha de nacimineto y Número de documento)</li>
                    {{--}}
                    <li>Sucursales y consultorios asignados.</li>
                    <li>Estado de actividad en el sistema.</li>
                    {{--}}
                </ul>
            </div>
                <div>
                     <img src="{{ asset('images/tutorial/practitioner/practitioner_list.png') }}" alt="Lista de doctores" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                </div>
        </div>
       {{--}}
        <div class="step-card">
            <div class="step-title"><strong>Acciones Administrativas</strong></div>
            <div class="step-content">
                <p>Dependiendo de tus permisos, podrás realizar las siguientes acciones:</p>
                <ul>
                    <li><strong>Ver Perfil:</strong> Accede al detalle completo del doctor, incluyendo su firma y sello.</li>
                    <li><strong>Editar:</strong> Modificar datos básicos, especialidades y asignaciones de sucursales.</li>
                    <li><strong>Firma y Sello:</strong> Gestionar los elementos necesarios para la validez de recetas y órdenes.</li>
                </ul>
            </div>
        </div>
        {{--}}
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.consulting-rooms') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Consultorios
        </a>
        <a href="{{ route('help.patients') }}" class="btn btn-primary btn-lg">
            Pacientes <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
@stop
