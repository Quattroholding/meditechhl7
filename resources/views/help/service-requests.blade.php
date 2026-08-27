@extends('help.layout')
@section('style')

:root {
    --study-color: #00897b;
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

.help-content .row {
    margin-left: 0;
    margin-right: 0;
    max-width: 100%;
}

.help-content .row > [class*="col-"] {
    padding-left: 15px;
    padding-right: 15px;
}

.help-content img {
    max-width: 100%;
    height: auto;
}

.help-content table,
.help-content .field-table {
    max-width: 100%;
    overflow-x: auto;
    display: block;
}

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

.help-breadcrumb {
    background: #fff;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.module-header {
    background: linear-gradient(135deg, var(--study-color) 0%, #4db6ac 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 137, 123, 0.3);
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

.content-section {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.content-section h2 {
    color: var(--study-color);
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0f2f1;
}

.field-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.field-table th {
    background: var(--study-color);
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
    background: var(--study-color);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 137, 123, 0.4);
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
    background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
    border: 2px dashed var(--study-color);
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
    color: var(--study-color);
    margin-bottom: 15px;
}

.screenshot-placeholder p {
    color: #004d40;
    font-weight: 500;
    margin-bottom: 5px;
}

.screenshot-placeholder small {
    color: var(--study-color);
}

.step-card {
    background: linear-gradient(135deg, #fff 0%, #f1f8e9 100%);
    border-left: 4px solid var(--study-color);
    padding: 20px;
    border-radius: 0 8px 8px 0;
    height: 100%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.step-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.step-content {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
}

.required-badge {
    background: #dc3545;
    color: white;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
}

.optional-badge {
    background: #6c757d;
    color: white;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
}

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

@media (max-width: 768px) {
    .field-table thead {
        display: none;
    }

    .field-table tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .field-table td {
        display: block;
        text-align: right;
        padding: 10px 15px;
        position: relative;
        border-bottom: 1px solid #f8f9fa;
    }

    .field-table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        font-weight: 700;
        color: var(--study-color);
    }

    .field-table td:last-child {
        border-bottom: none;
    }
}

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
    @include('help.sidebar', ['active' => 'service-requests'])
@stop
@section('breadcrumb')
    <nav class="help-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Repositorio de Estudios</li>
        </ol>
    </nav>
@endsection
@section('module-header')
    <div class="module-header">
        <h1><i class="fas fa-microscope me-3"></i>Repositorio de Estudios</h1>
        <p>Módulo centralizado para la gestión y seguimiento de todas las solicitudes de estudios (Laboratorios, Imágenes y Procedimientos) realizadas en el sistema.</p>
    </div>
@stop
@section('table-content')

    <div class="content-section">
            <h2><i class="fas fa-list me-2"></i>Lista de Estudios</h2>
            <p>En esta sección podrá visualizar todas las solicitudes generadas. A continuación se explican los campos disponibles en la tabla de resultados:</p>

            <div>
                <img src="{{ asset('images/tutorial/service_requests/services-st.png') }}" alt="" style="width: 100%;">
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
                        <!--<tr>
                            <td data-label="Campo"><strong>ID</strong></td>
                            <td data-label="Descripción">Identificador único interno de la solicitud en el sistema.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Código</strong></td>
                            <td data-label="Descripción">Código de referencia asociado al estudio (ej. CUPS o código interno de la entidad).</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Tipo</strong></td>
                            <td data-label="Descripción">Categoría a la que pertenece el estudio (Laboratorio, Imagen o Procedimiento).</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Descripción</strong></td>
                            <td data-label="Descripción">Nombre detallado o descripción del estudio solicitado.</td>
                        </tr>-->
                        <tr>
                            <td data-label="Campo"><strong>Paciente</strong></td>
                            <td data-label="Descripción">Nombre completo del paciente a quien se le solicitó el estudio.</td>
                        </tr>
                        <!--<tr>
                            <td data-label="Campo"><strong>Profesional</strong></td>
                            <td data-label="Descripción">Médico o profesional de la salud que realizó la solicitud.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Estado</strong></td>
                            <td data-label="Descripción">Situación actual de la solicitud (Borrador, Activo, Completado, etc.).</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Prioridad</strong></td>
                            <td data-label="Descripción">Nivel de urgencia de la solicitud (Rutina, Urgente, Inmediato).</td>
                        </tr>-->
                        <tr>
                            <td data-label="Campo"><strong>Fecha de Consulta</strong></td>
                            <td data-label="Descripción">Fecha y hora en que se asignó una orden médica a la consulta de paciente.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Tipo de Cita</strong></td>
                            <td data-label="Descripción">Tipo de Servicio que fue colocado al crear la cita.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Servicios</strong></td>
                            <td data-label="Descripción">Servicios que fueron agregados en la consulta.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Resultados</strong></td>
                            <td data-label="Descripción">Resultados cargados a las ordenes médicas que le fueran agregadas al paciente en la consulta.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-tasks me-2"></i>Acciones y Gestión</h2>
            <p>Dentro de la lista de estudios, cada registro cuenta con acciones específicas para su gestión:</p>
            <div class="mb-4">
                <img src="{{ asset('images/tutorial/service_requests/sr-detail.png') }}" alt="Subir Resultado" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="step-card">
                        <div class="step-title"><i class="fas fa-upload text-primary me-2"></i>Subir Resultados</div>
                        <div class="step-content">
                            <p>Para cargar los resultados de un estudio:</p>
                            <ol>
                                <li>Ubique el estudio en la lista.</li>
                                <li>Haga clic en el icono de <strong>Subir Resultados (botón azul)</strong> ubicado en la columna de acciones.</li>
                                <li>Se abrirá el modal <strong>"Subir Resultado"</strong> donde deberá completar la información técnica.</li>
                                <li>Luego de haber completado toda la información, haga clic en <strong>"Subir Resultado"</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="step-card">
                        <div class="step-title"><i class="fas fa-exchange-alt text-warning me-2"></i>Cambiar Estado</div>
                        <div class="step-content">
                            <p>Puede gestionar el ciclo de vida de la solicitud manualmente:</p>
                            <ol>
                                <li>Haga clic en el icono de <strong>Cambiar Estado (botón amarillo)</strong>.</li>
                                <li>Se abrirá el modal <strong>"Gestión de Estado"</strong>.</li>
                                <li>Seleccione el nuevo estado y el la razón del cambio(este campo es opcional).</li>
                                <li>Luego de haber completado toda la información, haga clic en <strong>"Cambiar Estado"</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-window-maximize me-2"></i>Detalle: Modal Subir Resultado</h3>
            <p>Este formulario permite vincular el documento físico o digital del resultado con el registro del paciente:</p>

            <div class="mb-4">
                <img src="{{ asset('images/tutorial/service_requests/services-mod1.png') }}" alt="Subir Resultado" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>

            <div class="table-responsive">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Obligatorio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Tipo de Resultado</strong></td>
                            <td data-label="Descripción">Clasificación del tipo de informe que se está cargando.</td>
                            <td data-label="Obligatorio"><span class="required-badge">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Estado</strong></td>
                            <td data-label="Descripción">Estado clínico del resultado (ej. Preliminar, Final, Corregido o Modificado).</td>
                            <td data-label="Obligatorio"><span class="required-badge">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Fecha del Resultado</strong></td>
                            <td data-label="Descripción">Fecha y hora exacta en la que se generó el resultado.</td>
                            <td data-label="Obligatorio"><span class="required-badge">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Archivo</strong></td>
                            <td data-label="Descripción">Botón para seleccionar el archivo desde su equipo. Formatos soportados: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX. (Máx. 10MB).</td>
                            <td data-label="Obligatorio"><span class="required-badge">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Interpretación</strong></td>
                            <td data-label="Descripción">Evaluación cualitativa del resultado (ej. Normal, Anormal, Crítico).</td>
                            <td data-label="Obligatorio"><span class="optional-badge">No</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Rango de Referencia</strong></td>
                            <td data-label="Descripción">Texto descriptivo de los valores normales para comparar con el resultado.</td>
                            <td data-label="Obligatorio"><span class="optional-badge">No</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Observaciones</strong></td>
                            <td data-label="Descripción">Comentarios clínicos relevantes sobre el estudio.</td>
                            <td data-label="Obligatorio"><span class="optional-badge">No</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Notas</strong></td>
                            <td data-label="Descripción">Cualquier anotación adicional o administrativa.</td>
                            <td data-label="Obligatorio"><span class="optional-badge">No</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="mt-4"><i class="fas fa-history me-2"></i>Detalle: Modal Gestión de Estado</h3>
            <p>Utilice este modal para actualizar en qué etapa se encuentra la solicitud de estudio:</p>

            <div class="mb-4">
                <img src="{{ asset('images/tutorial/service_requests/sr-mod2.png') }}" alt="Gestión de Estado" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>

            <div class="table-responsive">
                <table class="field-table">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Obligatorio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Campo"><strong>Nuevo Estado</strong></td>
                            <td data-label="Descripción">Seleccione el estado al que desea transicionar la solicitud (ej. Activo, En Espera, Completado, Revocado o Ingresado por error).</td>
                            <td data-label="Obligatorio"><span class="required-badge">Sí</span></td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Razón del cambio</strong></td>
                            <td data-label="Descripción">Espacio para justificar el motivo por el cual se está modificando el estado, útil para auditoría médica.</td>
                            <td data-label="Obligatorio"><span class="optional-badge">No</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>Estados de la Solicitud</h2>
            <p>Las solicitudes pueden pasar por diferentes estados según el estándar FHIR:</p>
            <ul>
                <li><span class="status-badge bg-secondary text-white">Borrador</span>: La solicitud ha sido creada pero aún no se ha enviado o confirmado.</li>
                <li><span class="status-badge bg-primary text-white">Activo</span>: La solicitud está confirmada y pendiente de ejecución o resultados.</li>
                <li><span class="status-badge bg-warning text-dark">En Espera</span>: La solicitud ha sido pausada temporalmente.</li>
                <li><span class="status-badge bg-success text-white">Completado</span>: El estudio se ha realizado y los resultados están disponibles.</li>
                <li><span class="status-badge bg-danger text-white">Revocado</span>: La solicitud ha sido cancelada.</li>
                <li><span class="status-badge bg-black text-white">Ingresado por error</span>: El estudio ha sido ingresado por error.</li>
            </ul>
        </div>

        <!-- SECTION: PRESCRIPTIONS / MEDICATION REQUESTS -->
        <div class="content-section">
            <h2><i class="fas fa-prescription-bottle me-2"></i>Prescripciones - Lista de Recetas</h2>
            <p>Desde esta sección puede acceder a todas las recetas médicas prescritas por los doctores durante las consultas. Aquí podrá visualizar, descargar y gestionar las prescripciones de medicamentos.</p>

            <div class="mb-4">
                <img src="{{ asset('images/tutorial/service_requests/prescription_list.png') }}" alt="Gestión de Estado" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>

            <h3 class="mt-4"><i class="fas fa-list me-2"></i>Campos de la Lista de Prescripciones</h3>
            <p>La tabla de prescripciones contiene los siguientes campos:</p>

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
                            <td data-label="Campo"><strong>Paciente</strong></td>
                            <td data-label="Descripción">Nombre completo del paciente a quien se prescribieron los medicamentos.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Doctor</strong></td>
                            <td data-label="Descripción">Nombre del profesional de salud que emitió la prescripción.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Fecha</strong></td>
                            <td data-label="Descripción">Fecha en la que se generó la prescripción médica.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Total de Medicamentos</strong></td>
                            <td data-label="Descripción">Cantidad de medicamentos incluidos en la receta.</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Estado</strong></td>
                            <td data-label="Descripción">Estatus actual de la prescripción (Activa, Completada, Cancelada, etc.).</td>
                        </tr>
                        <tr>
                            <td data-label="Campo"><strong>Descargar</strong></td>
                            <td data-label="Descripción">Botón para descargar la receta en formato PDF con la firma digital del médico.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="mt-4"><i class="fas fa-file-pdf me-2"></i>Descargar Receta en PDF</h3>
            <p>Para obtener una copia de la receta médica:</p>
            <ol>
                <li>Localice la prescripción que desea descargar en la lista</li>
                <li>Haga clic en el botón <strong>"Descargar PDF"</strong> o el ícono de descarga</li>
                <li>El sistema generará automáticamente un documento PDF con:
                    <ul>
                        <li>Todos los medicamentos prescritos con dosis e indicaciones</li>
                        <li>Firma digital del doctor (si está configurada)</li>
                        <li>Sello y validez legal del documento</li>
                        <li>Datos del paciente y de la consulta</li>
                    </ul>
                </li>
                <li>El PDF se descargará a su dispositivo y podrá:
                    <ul>
                        <li>Guardarlo en sus archivos</li>
                        <li>Imprimirlo directamente</li>
                        <li>Enviarlo al paciente por correo electrónico</li>
                        <li>Compartirlo con farmacias o especialistas</li>
                    </ul>
                </li>
            </ol>

            <div class="screenshot-placeholder">
                <i class="fas fa-image"></i>
                <p>Opción de Descarga de PDF</p>
                <small>Captura de pantalla: Botones de descarga y acciones de recetas</small>
            </div>

            <div class="info-box info-note">
                <i class="fas fa-info-circle text-primary"></i>
                <div>
                    <strong>Documentos con Validez Legal</strong>
                    <p class="mb-0">Los archivos PDF descargados incluyen automáticamente la firma y sello digital del médico prescriptor, lo que les confiere validez legal y médica según la normativa de documentos electrónicos.</p>
                </div>
            </div>
        </div>
@stop
