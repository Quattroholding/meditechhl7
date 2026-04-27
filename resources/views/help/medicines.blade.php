<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos - Centro de Ayuda SAMI</title>
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
            --medicine-color: #673ab7;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
        }

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

        .help-content {
            margin-left: 280px;
            padding: 30px;
            max-width: calc(100vw - 280px);
            overflow-x: hidden;
            box-sizing: border-box;
        }

        @media (max-width: 992px) {
            .help-content {
                margin-left: 0;
                max-width: 100%;
            }
        }

        .module-header {
            background: linear-gradient(135deg, var(--medicine-color) 0%, #9575cd 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(103, 58, 183, 0.3);
            position: relative;
            overflow: hidden;
        }

        .module-header h1 {
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .module-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .help-breadcrumb {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            color: var(--medicine-color);
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ede7f6;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .field-table th {
            background: var(--medicine-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        .field-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--medicine-color);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(103, 58, 183, 0.4);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 9999;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            border: 2px dashed var(--medicine-color);
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
            color: var(--medicine-color);
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #4a148c;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: var(--medicine-color);
        }
    </style>
</head>
<body>

    @include('help.sidebar', ['active' => 'medicines'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Medicamentos</li>
            </ol>
        </nav>

        <div class="module-header">
            <h1><i class="fas fa-pills me-3"></i>Catálogo de Medicamentos</h1>
            <p>Gestión del vademécum o catálogo de medicamentos disponibles para la formulación en las consultas médicas.</p>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-list me-2"></i>Lista de Medicamentos</h2>
            <p>A continuación se describen los campos principales que encontrará en la lista de medicamentos:</p>
            
            <div>
                <img src="{{ asset('images/tutorial/medicines/medicines-index.png') }}" alt="" style="width: 100%;">
            </div>

            <div class="table-responsive">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>ID</strong></td>
                            <td>Identificador único del medicamento en la base de datos.</td>
                        </tr>
                        <tr>
                            <td><strong>Código</strong></td>
                            <td>Código de referencia estándar o interno del medicamento.</td>
                        </tr>
                        <tr>
                            <td><strong>Fuente</strong></td>
                            <td>Indica si el medicamento proviene de un catálogo estándar o si fue creado manualmente (CUSTOM).</td>
                        </tr>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td>Nombre comercial o genérico del medicamento, incluyendo ingredientes activos y concentraciones.</td>
                        </tr>
                        <tr>
                            <td><strong>Forma</strong></td>
                            <td>Presentación farmacéutica del medicamento (ej. Tabletas, Cápsulas, Solución Inyectable).</td>
                        </tr>
                        <tr>
                            <td><strong>Fabricante</strong></td>
                            <td>Laboratorio o entidad responsable de la fabricación del medicamento.</td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td>Indica si el medicamento está habilitado para su uso en el sistema.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-plus-circle me-2"></i>Gestión de Medicamentos</h2>
            <div class="info-box border-start border-4 border-primary p-3 bg-light mb-3">
                <p class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i><strong>Nota:</strong> Solo los medicamentos marcados como "CUSTOM" pueden ser editados o eliminados por el usuario administrador del cliente, siempre que cuente con los permisos necesarios.</p>
            </div>
            <p>Desde este módulo puede:</p>
            <ul>
                <li>Buscar medicamentos por nombre, código o fabricante.</li>
                <li>Filtrar por estado (Activo/Inactivo).</li>
                <li>Agregar nuevos medicamentos personalizados al catálogo de su institución.</li>
            </ul>
        </div>
    </main>

    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
