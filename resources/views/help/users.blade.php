@extends('help.layout')
@section('style')
    .help-content {
    margin-left: 280px;
    padding: 30px;
    min-height: 100vh;
    }

    .help-breadcrumb {
    background: #fff;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .help-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .content-section h2 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
    color: var(--user-color);
    }

    .step-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border-left: 4px solid var(--user-color);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
    }

    .screenshot-placeholder {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border: 2px dashed var(--user-color);
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
    color: var(--user-color);
    margin-bottom: 15px;
    }

    .back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: var(--user-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
    }

    .back-to-top.visible {
    opacity: 1;
    visibility: visible;
    }

    @media (max-width: 992px) {
    .help-content { margin-left: 0; }
    }
@stop
@section('sidebar')
    @include('help.sidebar', ['active' => 'users'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <header class="help-header">
        <h1><i class="fas fa-users me-3"></i>Gestión de Usuarios</h1>
        <p>Administración de cuentas de acceso, roles y seguridad para todo el personal.</p>
    </header>
@stop
@section('table-content')
    <div class="content-section">
        <h2>Lista de Usuarios</h2>
        <p>Desde este módulo se gestionan todos los usuarios que tienen acceso a la plataforma, independientemente de su rol.</p>

        <div class="step-card">
            <div class="step-title"><strong>Gestión de Cuentas</strong></div>
            <div class="step-content">
                <p>En el listado de usuarios puedes:</p>
                <ul>
                    <li>Ver quiénes tienen acceso activo al sistema.</li>
                    <li>Identificar el rol asignado a cada usuario.</li>
                    <li>Ver la fecha de creación.</li>
                    <li>Realizar búsquedas rápidas por nombre o correo.</li>
                </ul>
            </div>
            <div>
                <img src="{{ asset('images/tutorial/user/user_list.png') }}" alt="" style="width: 100%;">
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2>Crear Nuevo Usuario</h2>
        <p>Proceso para dar de alta a nuevo personal administrativo o médico.</p>

        <div class="step-card">
            <div class="step-title"><strong>Formulario de Registro</strong></div>
            <div class="step-content">
                <p>Al crear un usuario, debes completar los datos básicos y asignar un rol inicial. Recuerda que el sistema enviará un correo de bienvenida al nuevo usuario con sus credenciales temporales.</p>
            </div>
            <div>
                <img src="{{ asset('images/tutorial/user/user_create.png') }}" alt="" style="width: 100%;">
            </div>
        </div>
    </div>

    <!--<div class="content-section">
        <h2>Validar Documentación</h2>
        <p>Módulo para la verificación de documentos cargados por los usuarios.</p>

        <div class="step-card">
            <div class="step-title"><strong>Proceso de Validación</strong></div>
            <div class="step-content">
                <p>En esta sección podrás revisar los documentos adjuntos (como identificaciones o licencias) y aprobarlos o rechazarlos para garantizar la seguridad de la plataforma.</p>
            </div>
            <div class="screenshot-placeholder">
                <i class="fas fa-file-signature"></i>
                <p>Imagen de Validación de Documentación</p>
            </div>
        </div>
    </div>-->

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('help.profile') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Mi Perfil
        </a>
        <a href="{{ route('help.roles') }}" class="btn btn-primary btn-lg">
            Roles y Permisos <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
@stop
