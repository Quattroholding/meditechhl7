<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles y Permisos - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #673ab7;
            --roles-color: #673ab7;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-text);
        }

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
            background: linear-gradient(135deg, #673ab7 0%, #9575cd 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(103, 58, 183, 0.3);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            border-bottom: 2px solid #ede7f6;
            padding-bottom: 10px;
            color: var(--roles-color);
        }

        .step-card {
            background: linear-gradient(135deg, #f3e5f5 0%, #fff 100%);
            border-left: 4px solid var(--roles-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            background: #f3e5f5;
            border-left: 4px solid #673ab7;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .badge-admin { background: #fee2e2; color: #991b1b; }
        .badge-doctor { background: #dcfce7; color: #166534; }
        .badge-assistant { background: #dbeafe; color: #1e40af; }
        .badge-receptionist { background: #fef9c3; color: #854d0e; }
        .badge-accounting { background: #f3e8ff; color: #6b21a8; }
        .badge-patient { background: #e0f7fa; color: #006064; }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--roles-color);
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

        /* Matrix Table Styles */
        .matrix-container {
            margin-top: 30px;
        }

        .search-container {
            margin-bottom: 20px;
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .search-input {
            padding: 12px 20px 12px 45px;
            border-radius: 30px;
            border: 2px solid #ede7f6;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--roles-color);
            outline: none;
            box-shadow: 0 4px 12px rgba(103, 58, 183, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #9575cd;
        }

        .matrix-table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            background: white;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .matrix-table th {
            background: #673ab7;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
        }

        .matrix-table th:first-child {
            text-align: left;
            position: sticky;
            left: 0;
            z-index: 2;
            background: #512da8;
        }

        .matrix-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
        }

        .matrix-table td:first-child {
            text-align: left;
            position: sticky;
            left: 0;
            background: white;
            font-weight: 500;
            z-index: 1;
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
            white-space: nowrap;
        }

        .module-row {
            background: #f8f9fa;
            font-weight: 700 !important;
            color: #4527a0;
            cursor: pointer;
        }

        .module-row i {
            transition: transform 0.3s;
        }

        .module-row.collapsed i {
            transform: rotate(-90deg);
        }

        .module-group-header {
            background: #ede7f6;
            color: #5e35b1;
            padding: 10px 15px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .permission-allowed {
            color: #2e7d32;
            font-size: 1.2rem;
        }

        .permission-denied {
            background-color: #ffebee !important;
            color: #c62828;
            font-size: 1.2rem;
        }

        .matrix-table tr:hover td {
            background-color: #f3e5f5;
        }

        @media (max-width: 992px) {
            .help-content { margin-left: 0; }
            .matrix-table td:first-child, .matrix-table th:first-child {
                position: static;
            }
        }
    </style>
</head>
<body>
    @include('help.sidebar', ['active' => 'roles'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Roles y Permisos</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-user-shield me-3"></i>Roles y Permisos</h1>
            <p>Gestiona los niveles de acceso y responsabilidades de cada usuario en tu sistema.</p>
        </header>

        <div class="content-section">
            <h2>Conceptos Básicos</h2>
            <p>El sistema SAMI utiliza un modelo de control de acceso basado en roles (RBAC). Esto significa que los permisos no se asignan directamente a los usuarios, sino a roles, y luego los usuarios se vinculan a uno o más roles.</p>
            
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5><i class="fas fa-id-badge text-primary me-2"></i>¿Qué es un Rol?</h5>
                            <p class="small mb-0">Un rol es una etiqueta que define un conjunto de responsabilidades. Ejemplo: "Doctor", "Asistente", "Contador".</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5><i class="fas fa-key text-success me-2"></i>¿Qué es un Permiso?</h5>
                            <p class="small mb-0">Un permiso es la capacidad técnica de realizar una acción específica. Ejemplo: "Crear Cita", "Ver Facturas".</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Roles Predeterminados</h2>
            <p>SAMI incluye varios roles preconfigurados para facilitar la puesta en marcha:</p>
            
            <div class="list-group mb-4">
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-admin">Admin Client</span>
                    <p class="small mb-0 text-muted">Acceso total a la configuración de la cuenta, sucursales y gestión de usuarios.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-doctor">Doctor</span>
                    <p class="small mb-0 text-muted">Gestión de su agenda, atención de consultas y acceso a historias clínicas de sus pacientes.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-receptionist">Recepcionista</span>
                    <p class="small mb-0 text-muted">Agendamiento de citas, registro de nuevos pacientes y cobros básicos.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-assistant">Asistente Médico</span>
                    <p class="small mb-0 text-muted">Preparación de pacientes (signos vitales) y soporte en la agenda del doctor.</p>
                </div>
                <div class="list-group-item border-0 border-bottom">
                    <span class="role-badge badge-patient">Paciente</span>
                    <p class="small mb-0 text-muted">Acceso a su portal personal, visualización de sus citas, recetas médicas y resultados de exámenes.</p>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title"><strong><i class="fas fa-info-circle me-2"></i>Nota sobre Planes y Administración</strong></div>
            <p class="mb-2">La disponibilidad de roles y sus permisos específicos dependen directamente del plan seleccionado (Básico, Estándar o Premium).</p>
            <ul class="mb-0 small">
                <li><strong>Planes Básico y Estándar:</strong> No existe un rol de "Admin Client" independiente. El Doctor que adquiere la suscripción actúa como administrador del sistema con acceso total.</li>
                <li><strong>Plan Premium:</strong> El rol de "Admin Client" es independiente. Puede ser el mismo médico (creado como un usuario adicional con ese rol) o una persona distinta encargada exclusivamente de la administración.</li>
            </ul>
        </div>

        <div class="content-section matrix-container">
            <h2>Matriz de Permisos Detallada</h2>
            <p>Consulta la comparativa completa de funciones permitidas según el plan y el rol asignado.</p>

            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="matrixSearch" class="search-input" placeholder="Buscar permiso o función (ej: 'citas', 'facturas')...">
            </div>

            <div class="matrix-table-wrapper">
                <table class="matrix-table" id="permissionsMatrix">
                    <thead>
                        <tr>
                            <th>Módulo / Función</th>
                            <th>Doctor (Estándar)</th>
                            <th>Admin Client (Premium)</th>
                            <th>Doctor (Premium)</th>
                            <th>Asistente (Premium)</th>
                            <th>Recep. (Premium)</th>
                            <th>Paciente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dashboard -->
                        <tr class="module-group-header">
                            <td colspan="7">Dashboard</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Dashboard Principal</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>

                        <!-- Sedes -->
                        <tr class="module-group-header">
                            <td colspan="7">Sedes</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Sucursales / Registrar</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Consultorios / Registrar</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Doctores -->
                        <tr class="module-group-header">
                            <td colspan="7">Doctores</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Gestión de Doctores Asociados</td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Pacientes -->
                        <tr class="module-group-header">
                            <td colspan="7">Pacientes</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Pacientes</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Crear Pacientes</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Citas -->
                        <tr class="module-group-header">
                            <td colspan="7">Citas</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Citas</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Calendario / Crear Cita</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>

                        <!-- Consultas -->
                        <tr class="module-group-header">
                            <td colspan="7">Consultas</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Ver Detalles (Consulta, Órdenes, Incapacidades)</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Editar Consultas</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Descargar PDF Resumen</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Descargar PDF Factura</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>

                        <!-- Repositorio -->
                        <tr class="module-group-header">
                            <td colspan="7">Repositorio de Estudios</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Acceso a Repositorio</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                         <!-- Medicamentos -->
                        <tr class="module-group-header">
                            <td colspan="7">Medicamentos</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Medicamentos</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Crear Medicamentos</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Editar Medicamentos</td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Eliminar Medicamentos 'Custom'</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Encuestas -->
                        <tr class="module-group-header">
                            <td colspan="7">Encuestas</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Gestión de Encuestas</td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Cuentas -->
                        <tr class="module-group-header">
                            <td colspan="7">Cuentas</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Facturas (Lista + Acciones)</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Pagos</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Configuraciones -->
                        <tr class="module-group-header">
                            <td colspan="7">Configuraciones</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Servicios</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Plantilla de Consulta</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Accesos Rápidos</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Horario Laboral</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Plantilla de Factura</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Plantilla de Incapacidad Médica</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Código de Referidos</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Directorio -->
                        <tr class="module-group-header">
                            <td colspan="7">Directorio Médico</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Acceso a Directorio</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>

                        <!-- Usuarios -->
                        <tr class="module-group-header">
                            <td colspan="7">Usuarios</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Lista de Usuarios</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Crear Usuarios</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Suscripción -->
                        <tr class="module-group-header">
                            <td colspan="7">Suscripción</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Ver Plan</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Facturas Suscripción</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Pagos Suscripción</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Métodos de Pago</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Soporte -->
                        <tr class="module-group-header">
                            <td colspan="7">Soporte</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Contacto Soporte Técnico</td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Historial Médico -->
                        <tr class="module-group-header">
                            <td colspan="7">Historial Médico</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Ver Historial Personal</td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>

                        <!-- Perfil -->
                        <tr class="module-group-header">
                            <td colspan="7">Perfil</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Datos Personales</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Datos Profesionales</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Agregar Especialidad</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Configuraciones Básicas de cuenta</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Firma y Sello Digital</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>
                        <tr class="permission-row">
                            <td>Código de Referido</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                            <td class="permission-denied"><i class="fas fa-times-circle"></i></td>
                        </tr>

                        <!-- Cerrar Sesión -->
                        <tr class="module-group-header">
                            <td colspan="7">Sistema</td>
                        </tr>
                        <tr class="permission-row">
                            <td>Cerrar Sesión</td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                            <td class="permission-allowed"><i class="fas fa-check-circle"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box mt-4">
                <div class="info-box-title"><strong><i class="fas fa-info-circle me-2"></i>Sobre esta matriz</strong></div>
                <p class="small mb-0">Esta tabla representa la configuración estándar del sistema. Si necesitas un plan personalizado, contacte al departamento de ventas para evaluar tu caso.</p>
            </div>
        </div>

        <!--<div class="content-section">
            <h2>Gestión de Roles Personalizados</h2>
            <p>Puedes crear roles nuevos para adaptar el sistema a las necesidades específicas de tu centro médico.</p>
            
            <div class="step-card">
                <div class="step-title">1. Crear un Nuevo Rol</div>
                <div class="step-content">
                    <p>Dirígete a <strong>Configuraciones > Roles y Permisos</strong>. Haz clic en "Nuevo Rol", asígnale un nombre descriptivo y selecciona los permisos deseados agrupados por módulos.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">2. Asignar Permisos por Módulo</div>
                <div class="step-content">
                    <p>Los permisos están organizados por áreas de trabajo (Citas, Pacientes, Facturas, etc.). Puedes usar el botón "Seleccionar todos" de un módulo si el rol debe tener control total sobre esa sección.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">3. Vincular Usuario a Rol</div>
                <div class="step-content">
                    <p>Una vez creado el rol, ve a la sección de <strong>Usuarios</strong>, selecciona un usuario y en su perfil asigna el rol que acabas de crear. Los cambios se aplicarán en su próximo inicio de sesión.</p>
                </div>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title"><strong><i class="fas fa-shield-alt"></i> Recomendación de Seguridad</strong></div>
            <p class="mb-0">Aplica el "Principio de Menor Privilegio": otorga a los usuarios solo los permisos estrictamente necesarios para realizar su trabajo. Esto protege la privacidad de los datos de tus pacientes.</p>
        </div>-->

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('help.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
            <a href="{{ route('help.support') }}" class="btn btn-primary btn-lg">
                Soporte y Contacto <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </main>

    <button class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btn = document.getElementById('backToTop');
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        };
        btn.onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        };

        // Live Search for Permissions Matrix
        const searchInput = document.getElementById('matrixSearch');
        const table = document.getElementById('permissionsMatrix');
        const rows = table.getElementsByClassName('permission-row');
        const headers = table.getElementsByClassName('module-group-header');

        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();
            const allElements = table.querySelectorAll('tr.permission-row, tr.module-group-header');
            
            let currentHeader = null;
            let currentGroupHasMatch = false;

            allElements.forEach(row => {
                if (row.classList.contains('module-group-header')) {
                    // Update previous header if it had no matches
                    if (currentHeader && !currentGroupHasMatch && filter !== '') {
                        currentHeader.style.display = 'none';
                    }
                    
                    currentHeader = row;
                    currentGroupHasMatch = false;
                    row.style.display = ''; // Reset display
                } else {
                    const text = row.cells[0].textContent.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = '';
                        currentGroupHasMatch = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Final check for the last header
            if (currentHeader && !currentGroupHasMatch && filter !== '') {
                currentHeader.style.display = 'none';
            }
        });
    </script>
</body>
</html>
