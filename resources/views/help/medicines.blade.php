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

:root {
    --medicine-color: #673ab7;
}

@stop

@section('sidebar')
    @include('help.sidebar', ['active' => 'medicines'])
@stop

@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Medicamentos</li>
        </ol>
    </nav>
@endsection

@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-pills me-3"></i>Catálogo de Medicamentos</h1>
        <p>Gestión del vademécum o catálogo de medicamentos disponibles para la formulación en las consultas médicas.</p>
    </div>
@stop

@section('table-content')
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
                        <td>Código registro para medicamentos provenientes del MINSA.</td>
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
        <h2><i class="fas fa-plus me-2"></i>Crear Nuevo Medicamento</h2>
        <p>Para registrar un nuevo medicamento que no se encuentra en el catálogo estándar, siga estos pasos:</p>
        <ol>
            <li>Acceda al formulario de creación desde el botón correspondiente en la lista de medicamentos.</li>
            <li>Complete la información solicitada en el formulario (los campos marcados con <span class="text-danger">*</span> son obligatorios).</li>
            <li>Si el medicamento tiene múltiples componentes, puede agregarlos en la sección de <strong>Ingredientes Activos</strong> haciendo clic en el botón <strong>+ Agregar Ingrediente</strong>.</li>
            <li>Hacer clic en el botón <strong>Registrar</strong>.</li>
            <li>El medicamento se creará con éxito y aparecerá identificado con la fuente <span class="badge bg-info text-dark">CUSTOM</span>.</li>
        </ol>

        <div class="mt-4 mb-4">
            <img src="{{ asset('images/tutorial/medicines/create-medicine.png') }}" alt="Formulario de creación de medicamento" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        </div>

        <h3 class="h5 mt-4 mb-3">Campos del Formulario</h3>
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
                        <td><strong>Nombre Genérico *</strong></td>
                        <td>Nombre principal o sustancia activa del medicamento.</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre Comercial</strong></td>
                        <td>Nombre de marca bajo el cual se vende el producto.</td>
                    </tr>
                    <tr>
                        <td><strong>Código</strong></td>
                        <td>Código interno o identificador del medicamento.</td>
                    </tr>
                    <tr>
                        <td><strong>Forma Farmacéutica *</strong></td>
                        <td>Presentación del medicamento (ej: Tabletas, Suspensión, etc.).</td>
                    </tr>
                    <tr>
                        <td><strong>Fabricante</strong></td>
                        <td>Laboratorio o empresa que produce el medicamento.</td>
                    </tr>
                    <tr>
                        <td><strong>Estado *</strong></td>
                        <td>Define si el medicamento estará disponible para su uso inmediato (Activo).</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="bg-light text-center"><strong>Sección: Ingredientes Activos</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Nombre del Ingrediente *</strong></td>
                        <td>Nombre de cada componente activo.</td>
                    </tr>
                    <tr>
                        <td><strong>Dosis *</strong></td>
                        <td>Cantidad del ingrediente por unidad de medida.</td>
                    </tr>
                    <tr>
                        <td><strong>Unidad *</strong></td>
                        <td>Unidad de medida de la dosis (ej: mg, ml, etc.).</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="content-section">
        <h2><i class="fas fa-mouse-pointer me-2"></i>Botones de Acciones</h2>
        <p>En la lista de medicamentos, encontrará los siguientes botones para interactuar con los registros:</p>

        <div class="info-box border-start border-4 border-warning p-3 bg-light mb-4 shadow-sm">
            <p class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i><strong>Condiciones para Editar o Eliminar:</strong></p>
            <p class="mb-0 mt-2 small text-muted">Estas acciones solo son posibles para medicamentos de fuente <strong>"CUSTOM"</strong> bajo las siguientes condiciones:</p>
            <ul class="mb-0 mt-2 small">
                <li>Contar con los permisos necesarios asignados a su cuenta.</li>
                <li>El medicamento <strong>no debe haber sido utilizado</strong> en ninguna receta médica previa.</li>
            </ul>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="p-4 border rounded h-100 bg-light shadow-sm text-center">
                    <h5 class="text-primary"><i class="fas fa-edit me-2"></i>Editar</h5>
                    <p class="mb-0">Permite modificar la información de un medicamento personalizado.</p>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="p-4 border rounded h-100 bg-light shadow-sm text-center">
                    <h5 class="text-danger"><i class="fas fa-trash me-2"></i>Eliminar</h5>
                    <p class="mb-0">Permite retirar un medicamento personalizado del catálogo.</p>
                </div>
            </div>
        </div>
    </div>
@stop
