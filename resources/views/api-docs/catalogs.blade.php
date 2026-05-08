<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos - Meditec PTY API</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: #f8fafc;
            color: #2d3748;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(135deg, #38b2ac 0%, #319795 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        .nav {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #38b2ac;
        }

        .main-content {
            padding: 2rem 0;
        }

        .endpoint {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .method {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.9rem;
            color: white;
        }

        .method.post { background: #48bb78; }
        .method.get { background: #4299e1; }
        .method.put { background: #ed8936; }
        .method.delete { background: #f56565; }

        .endpoint-url {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #2d3748;
        }

        .section {
            margin: 1.5rem 0;
        }

        .section h3 {
            color: #2d3748;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .code-block {
            background: #1a202c;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .response-codes {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: center;
            margin: 1rem 0;
        }

        .status-code {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
        }

        .status-200 { background: #48bb78; }
        .status-201 { background: #38b2ac; }
        .status-400 { background: #ed8936; }
        .status-401 { background: #f56565; }
        .status-403 { background: #e53e3e; }
        .status-404 { background: #a0aec0; }
        .status-422 { background: #9f7aea; }
        .status-500 { background: #718096; }

        .params-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .params-table th,
        .params-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .params-table th {
            background: #f7fafc;
            font-weight: 600;
            color: #2d3748;
        }

        .param-required {
            color: #f56565;
            font-weight: 600;
        }

        .param-optional {
            color: #718096;
        }

        .toc {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .toc ul {
            list-style: none;
            padding-left: 0;
        }

        .toc ul li {
            margin: 0.5rem 0;
        }

        .toc ul li a {
            text-decoration: none;
            color: #4a5568;
            transition: color 0.3s;
        }

        .toc ul li a:hover {
            color: #38b2ac;
        }

        .note {
            background: #bee3f8;
            border-left: 4px solid #4299e1;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }

        .info-box {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }

        .warning {
            background: #fed7d7;
            border-left: 4px solid #f56565;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-pills"></i> Catálogos Médicos</h1>
            <p>Endpoints para acceso a catálogos y referencias médicas</p>
        </div>
    </div>

    <nav class="nav">
        <div class="container nav-container">
            <div class="logo">
                <a href="index" style="text-decoration: none; color: #2d3748;"><strong>← Meditec PTY API Docs</strong></a>
            </div>
            <ul class="nav-links">
                <li><a href="{{route('api.docs.show','index')}}"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="{{route('api.docs.show','auth')}}"><i class="fas fa-key"></i> Autenticación</a></li>
                <li><a href="{{route('api.docs.show','appointments')}}"><i class="fas fa-calendar-alt"></i> Citas</a></li>
                <li><a href="{{route('api.docs.show','patients')}}"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="{{route('api.docs.show','practitioners')}}"><i class="fas fa-user-md"></i> Médicos</a></li>
                <li><a href="{{route('api.docs.show','catalogs')}}"><i class="fas fa-pills"></i> Catálogos</a></li>
                <li><a href="{{route('api.docs.show','notifications')}}"><i class="fas fa-bell"></i> Notificaciones</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="toc">
                <h3>Índice de Contenidos</h3>
                <ul>
                    <li><a href="#medicines">GET /medicines - Catálogo de Medicamentos</a></li>
                    <li><a href="#cpts">GET /cpts/{type} - Códigos CPT</a></li>
                    <li><a href="#diagnostics">GET /diagnostics - Códigos de Diagnóstico</a></li>
                    <li><a href="#services-catalog">GET /services_catalog - Catálogo de Servicios</a></li>
                    <li><a href="#medical-speciality">GET /medical_speciality - Especialidades Médicas</a></li>
                </ul>
            </div>

            <div class="note">
                <strong>Información importante:</strong> Los catálogos médicos contienen referencias estándar de la industria de salud, incluyendo medicamentos, códigos CPT, diagnósticos ICD-10 y servicios médicos. Estos endpoints están diseñados para soportar aplicaciones clínicas y administrativas.
            </div>

            <div class="endpoint" id="medicines">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/medicines</span>
                </div>

                <p>Obtiene el catálogo completo de medicamentos disponibles en el sistema, incluyendo información farmacológica detallada.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Buscar por nombre comercial o genérico del medicamento</td>
                            </tr>
                            <tr>
                                <td><code>category</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por categoría farmacológica</td>
                            </tr>
                            <tr>
                                <td><code>active_ingredient</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por principio activo</td>
                            </tr>
                            <tr>
                                <td><code>prescription_required</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar si requiere receta médica (true/false)</td>
                            </tr>
                            <tr>
                                <td><code>limit</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Número máximo de resultados (por defecto: 50)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/medicines?search=ibuprofeno&category=analgesico&limit=10
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Catálogo de medicamentos obtenido exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "commercial_name": "Ibuprofeno 400mg",
            "generic_name": "Ibuprofeno",
            "active_ingredient": "Ibuprofeno",
            "dosage": "400mg",
            "pharmaceutical_form": "Tableta",
            "category": "Analgésico/Antiinflamatorio",
            "manufacturer": "Laboratorios ABC",
            "prescription_required": false,
            "contraindications": [
                "Hipersensibilidad al ibuprofeno",
                "Úlcera péptica activa",
                "Insuficiencia renal severa"
            ],
            "side_effects": [
                "Náuseas",
                "Dolor de cabeza",
                "Mareos"
            ],
            "dosage_instructions": "Adultos: 1-2 tabletas cada 6-8 horas. Máximo 6 tabletas al día.",
            "storage_conditions": "Conservar en lugar fresco y seco, protegido de la luz",
            "expiration_months": 36,
            "active": true
        }
    ],
    "total": 1,
    "showing": "1 de 1 resultados"
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="cpts">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/cpts/{type}</span>
                </div>

                <p>Obtiene códigos CPT (Current Procedural Terminology) utilizados para codificar procedimientos médicos y servicios.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Ruta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>type</code></td>
                                <td>string</td>
                                <td>Tipo de CPT: evaluation, surgery, radiology, pathology, medicine</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Buscar por código o descripción</td>
                            </tr>
                            <tr>
                                <td><code>specialty</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por especialidad médica</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="info-box">
                    <strong>Tipos de CPT Disponibles:</strong><br>
                    <ul style="margin: 0.5rem 0; padding-left: 2rem;">
                        <li><strong>evaluation:</strong> Códigos de evaluación y manejo (99201-99499)</li>
                        <li><strong>surgery:</strong> Códigos de cirugía (10021-69990)</li>
                        <li><strong>radiology:</strong> Códigos de radiología (70010-79999)</li>
                        <li><strong>pathology:</strong> Códigos de patología y laboratorio (80047-89398)</li>
                        <li><strong>medicine:</strong> Códigos de medicina (90281-99607)</li>
                    </ul>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/cpts/evaluation?search=consulta&specialty=cardiologia
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Códigos CPT obtenidos exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "code": "99213",
            "description": "Visita de oficina para evaluación y manejo de paciente establecido, que requiere al menos 2 de estos 3 componentes clave",
            "category": "Evaluación y Manejo",
            "type": "evaluation",
            "specialty": "Medicina General",
            "relative_value_units": 1.3,
            "medicare_fee": 92.47,
            "work_rvu": 0.97,
            "practice_expense_rvu": 0.45,
            "malpractice_rvu": 0.02,
            "active": true
        }
    ],
    "type": "evaluation",
    "total": 1
}
                    </div>

                    <div class="response-codes">
                        <span class="status-code status-404">404</span>
                        <span>Tipo de CPT no válido</span>
                    </div>
                    <div class="code-block">
{
    "message": "Tipo de CPT no válido. Tipos disponibles: evaluation, surgery, radiology, pathology, medicine"
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="diagnostics">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/diagnostics</span>
                </div>

                <p>Obtiene códigos de diagnóstico ICD-10 utilizados para codificar enfermedades, síntomas y causas externas.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Buscar por código ICD-10 o descripción</td>
                            </tr>
                            <tr>
                                <td><code>category</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por categoría de diagnóstico</td>
                            </tr>
                            <tr>
                                <td><code>chapter</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por capítulo ICD-10 (A00-B99, C00-D49, etc.)</td>
                            </tr>
                            <tr>
                                <td><code>limit</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Número máximo de resultados (por defecto: 50)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/diagnostics?search=hipertension&category=cardiovascular&limit=10
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Códigos de diagnóstico obtenidos exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "code": "I10",
            "description": "Hipertensión esencial (primaria)",
            "full_description": "Hipertensión esencial (primaria) - Presión arterial alta de causa desconocida",
            "category": "Enfermedades del sistema circulatorio",
            "chapter": "I00-I99",
            "chapter_name": "Enfermedades del sistema circulatorio",
            "severity": "mild",
            "chronic": true,
            "reportable": false,
            "synonyms": [
                "Hipertensión arterial",
                "Presión alta",
                "HTA esencial"
            ],
            "includes": [
                "Hipertensión (arterial) (benigna) (esencial) (maligna) (primaria) (sistémica)"
            ],
            "excludes": [
                "Hipertensión complicando el embarazo, parto y puerperio"
            ],
            "active": true
        }
    ],
    "total": 1,
    "filters_applied": {
        "search": "hipertension",
        "category": "cardiovascular"
    }
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="services-catalog">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/services_catalog</span>
                </div>

                <p>Obtiene el catálogo de servicios médicos disponibles en la institución, incluyendo precios y descripciones.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>category</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por categoría de servicio</td>
                            </tr>
                            <tr>
                                <td><code>department</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por departamento médico</td>
                            </tr>
                            <tr>
                                <td><code>active_only</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Solo servicios activos (por defecto: true)</td>
                            </tr>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Buscar por nombre del servicio</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/services_catalog?category=consulta&department=cardiologia
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Catálogo de servicios obtenido exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "code": "CONS-CARD-001",
            "name": "Consulta Cardiología General",
            "description": "Consulta médica especializada en cardiología para evaluación y seguimiento de pacientes",
            "category": "Consulta Médica",
            "department": "Cardiología",
            "duration_minutes": 30,
            "price": {
                "base_price": 75.00,
                "insurance_price": 60.00,
                "currency": "USD"
            },
            "requirements": [
                "Documento de identidad",
                "Tarjeta de seguro (si aplica)",
                "Referencia médica (opcional)"
            ],
            "preparation_instructions": "No se requiere preparación especial",
            "active": true,
            "available_locations": [
                {
                    "branch_id": 1,
                    "branch_name": "Sucursal Principal",
                    "available": true
                }
            ]
        }
    ],
    "categories": [
        "Consulta Médica",
        "Exámenes de Laboratorio",
        "Estudios Radiológicos",
        "Procedimientos",
        "Cirugía"
    ],
    "departments": [
        "Cardiología",
        "Medicina General",
        "Pediatría",
        "Ginecología"
    ]
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="medical-speciality">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/medical_speciality</span>
                </div>

                <p>Obtiene información detallada sobre las especialidades médicas disponibles en el sistema.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>active_only</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Solo especialidades activas (por defecto: true)</td>
                            </tr>
                            <tr>
                                <td><code>with_practitioners</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Solo especialidades con médicos disponibles (por defecto: false)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/medical_speciality?active_only=true&with_practitioners=true
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Especialidades médicas obtenidas exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "name": "Cardiología",
            "description": "Especialidad médica que se ocupa de las afecciones del corazón y del aparato circulatorio",
            "full_description": "La cardiología es la rama de la medicina interna que se ocupa de las afecciones del corazón y del aparato circulatorio. Se incluye dentro de las especialidades médicas, es decir, no quirúrgicas.",
            "code": "CARD",
            "category": "Especialidad Médica",
            "typical_conditions": [
                "Hipertensión arterial",
                "Infarto del miocardio",
                "Arritmias cardíacas",
                "Insuficiencia cardíaca"
            ],
            "common_procedures": [
                "Electrocardiograma (ECG)",
                "Ecocardiografía",
                "Prueba de esfuerzo",
                "Cateterismo cardíaco"
            ],
            "practitioners_count": 5,
            "available_practitioners": [
                {
                    "id": 1,
                    "name": "Dr. Juan Carlos Pérez",
                    "registry": "RM-12345"
                }
            ],
            "active": true,
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "total": 1,
    "summary": {
        "total_specialties": 15,
        "active_specialties": 12,
        "with_practitioners": 10
    }
}
                    </div>
                </div>
            </div>

            <div class="warning">
                <strong>Importante:</strong> Los catálogos médicos contienen información estándar de la industria y pueden requerir actualizaciones periódicas para mantener la precisión y relevancia clínica.
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
</body>
</html>
