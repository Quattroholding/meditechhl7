<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos - Meditec PTY API</title>
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
            background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
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
            color: #9f7aea;
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
            color: #9f7aea;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-user-md"></i> Médicos</h1>
            <p>Endpoints para gestión de médicos, especialidades y disponibilidad</p>
        </div>
    </div>

    <nav class="nav">
        <div class="container nav-container">
            <div class="logo">
                <a href="index" style="text-decoration: none; color: #2d3748;"><strong>← Meditec PTY API Docs</strong></a>
            </div>
            <ul class="nav-links">
                <li><a href="index"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="auth"><i class="fas fa-key"></i> Autenticación</a></li>
                <li><a href="appointments"><i class="fas fa-calendar-alt"></i> Citas</a></li>
                <li><a href="patients"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="practitioners"><i class="fas fa-user-md"></i> Médicos</a></li>
                <li><a href="catalogs"><i class="fas fa-pills"></i> Catálogos</a></li>
                <li><a href="notifications"><i class="fas fa-bell"></i> Notificaciones</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="toc">
                <h3>Índice de Contenidos</h3>
                <ul>
                    <li><strong>API Sanctum (auth:sanctum)</strong></li>
                    <li><a href="#list-practitioners">GET /practitioners - Listar Médicos</a></li>
                    <li><a href="#practitioner-availability">GET /practitioners/{id}/availability - Disponibilidad</a></li>
                    <li><a href="#practitioner-consulting-rooms">GET /practitioners/{id}/consulting-rooms - Consultorios</a></li>
                    <li><a href="#practitioner-service-catalog">GET /practitioners/{id}/service-catalog - Catálogo de Servicios</a></li>
                    <li><a href="#medical-specialities">GET /medical-specialities - Especialidades Médicas</a></li>
                    <li><a href="#consulting-rooms">GET /consulting-rooms - Lista de Consultorios</a></li>
                    <li><strong>API v1 (api.token)</strong></li>
                    <li><a href="#v1-list-practitioners">GET /v1/practitioners - Listar Médicos</a></li>
                    <li><a href="#v1-practitioner-availability">GET /v1/practitioners/{id}/availability - Disponibilidad</a></li>
                    <li><a href="#v1-practitioner-consulting-rooms">GET /v1/practitioners/{id}/consulting-rooms - Consultorios</a></li>
                    <li><a href="#v1-practitioner-service-catalog">GET /v1/practitioners/{id}/service-catalog - Catálogo de Servicios</a></li>
                    <li><a href="#v1-medical-specialities">GET /v1/medical-specialities - Especialidades Médicas</a></li>
                </ul>
            </div>

            <div class="note">
                <strong>Nota:</strong> Los endpoints de médicos están disponibles en dos versiones:
                <ul style="margin-top: 0.5rem;">
                    <li><strong>API Sanctum (/api/*):</strong> Para usuarios autenticados con tokens Sanctum</li>
                    <li><strong>API v1 (/api/v1/*):</strong> Para integraciones externas con tokens API que incluyen restricciones por IP y scopes</li>
                </ul>
            </div>

            <div class="endpoint" id="list-practitioners">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/practitioners</span>
                </div>

                <p>Obtiene la lista de médicos registrados en el sistema con paginación y filtros de búsqueda. <strong>Incluye los horarios ocupados de la próxima semana</strong> para facilitar la planificación de citas.</p>

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
                                <td><code>page</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Número de página (por defecto: 1)</td>
                            </tr>
                            <tr>
                                <td><code>per_page</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Elementos por página (1-50, por defecto: 10)</td>
                            </tr>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Buscar por nombre, email, teléfono, identificación o especialidad</td>
                            </tr>
                            <tr>
                                <td><code>speciality_id</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por especialidad médica específica</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/practitioners?page=1&per_page=5&search=cardiología&speciality_id=3
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Lista de médicos obtenida exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "fhir_id": "practitioner-abc123",
            "identifier": "8-765-432",
            "registry": "RM-12345",
            "name": "Dr. Juan Carlos Pérez",
            "given_name": "Juan Carlos",
            "family_name": "Pérez",
            "gender": "male",
            "birth_date": "1975-08-20",
            "phone": "+507 6000-9876",
            "email": "dr.perez@ejemplo.com",
            "active": true,
            "specialties": [
                {
                    "id": 3,
                    "name": "Cardiología",
                    "description": "Especialidad en enfermedades del corazón"
                }
            ],
            "clients": [
                {
                    "id": 1,
                    "name": "Clínica San Fernando"
                }
            ],
            "profile_picture_url": "https://ejemplo.com/storage/avatars/practitioner-1.jpg",
            "next_week_schedule": {
                "week_start": "2025-09-08",
                "week_end": "2025-09-14",
                "booked_appointments": [
                    {
                        "start": "2025-09-08 09:00",
                        "end": "2025-09-08 10:00",
                        "date": "2025-09-08",
                        "start_time": "09:00",
                        "end_time": "10:00",
                        "status": "booked",
                        "day_of_week": "Monday",
                        "day_of_week_es": "Lunes"
                    },
                    {
                        "start": "2025-09-10 14:30",
                        "end": "2025-09-10 15:30",
                        "date": "2025-09-10",
                        "start_time": "14:30",
                        "end_time": "15:30",
                        "status": "arrived",
                        "day_of_week": "Wednesday",
                        "day_of_week_es": "Miércoles"
                    }
                ],
                "total_appointments": 2,
                "busy_days": ["2025-09-08", "2025-09-10"]
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 5,
        "total": 25,
        "last_page": 5,
        "from": 1,
        "to": 5,
        "has_more_pages": true
    },
    "next_week_info": {
        "start_date": "2025-09-08",
        "end_date": "2025-09-14",
        "week_dates": [
            {
                "date": "2025-09-08",
                "day_name": "Monday",
                "day_name_es": "Lunes",
                "day_short": "Mon",
                "day_number": "8"
            },
            {
                "date": "2025-09-09",
                "day_name": "Tuesday",
                "day_name_es": "Martes",
                "day_short": "Tue",
                "day_number": "9"
            },
            {
                "date": "2025-09-10",
                "day_name": "Wednesday",
                "day_name_es": "Miércoles",
                "day_short": "Wed",
                "day_number": "10"
            },
            {
                "date": "2025-09-11",
                "day_name": "Thursday",
                "day_name_es": "Jueves",
                "day_short": "Thu",
                "day_number": "11"
            },
            {
                "date": "2025-09-12",
                "day_name": "Friday",
                "day_name_es": "Viernes",
                "day_short": "Fri",
                "day_number": "12"
            },
            {
                "date": "2025-09-13",
                "day_name": "Saturday",
                "day_name_es": "Sábado",
                "day_short": "Sat",
                "day_number": "13"
            },
            {
                "date": "2025-09-14",
                "day_name": "Sunday",
                "day_name_es": "Domingo",
                "day_short": "Sun",
                "day_number": "14"
            }
        ]
    }
}
                    </div>
                </div>

                <div class="section">
                    <h3>Campos de Horarios de la Próxima Semana</h3>
                    <p>La respuesta incluye información detallada sobre los horarios ocupados de la próxima semana para cada practitioner:</p>

                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>next_week_schedule</code></td>
                                <td>object</td>
                                <td>Objeto contenedor con la información de horarios de la próxima semana</td>
                            </tr>
                            <tr>
                                <td><code>week_start</code></td>
                                <td>date</td>
                                <td>Fecha de inicio de la semana (Lunes)</td>
                            </tr>
                            <tr>
                                <td><code>week_end</code></td>
                                <td>date</td>
                                <td>Fecha de fin de la semana (Domingo)</td>
                            </tr>
                            <tr>
                                <td><code>booked_appointments</code></td>
                                <td>array</td>
                                <td>Lista de citas con estado 'booked', 'arrived' o 'fulfilled'</td>
                            </tr>
                            <tr>
                                <td><code>total_appointments</code></td>
                                <td>integer</td>
                                <td>Cantidad total de citas ocupadas en la semana</td>
                            </tr>
                            <tr>
                                <td><code>busy_days</code></td>
                                <td>array</td>
                                <td>Lista de fechas únicas con citas agendadas</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 style="margin-top: 1.5rem; margin-bottom: 0.5rem;">Campos de cada cita en <code>booked_appointments</code>:</h4>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>start</code></td>
                                <td>datetime</td>
                                <td>Fecha y hora de inicio completa (YYYY-MM-DD HH:mm)</td>
                            </tr>
                            <tr>
                                <td><code>end</code></td>
                                <td>datetime</td>
                                <td>Fecha y hora de fin completa (YYYY-MM-DD HH:mm)</td>
                            </tr>
                            <tr>
                                <td><code>date</code></td>
                                <td>date</td>
                                <td>Solo la fecha de la cita (YYYY-MM-DD)</td>
                            </tr>
                            <tr>
                                <td><code>start_time</code></td>
                                <td>time</td>
                                <td>Solo la hora de inicio (HH:mm)</td>
                            </tr>
                            <tr>
                                <td><code>end_time</code></td>
                                <td>time</td>
                                <td>Solo la hora de fin (HH:mm)</td>
                            </tr>
                            <tr>
                                <td><code>status</code></td>
                                <td>string</td>
                                <td>Estado de la cita: 'booked', 'arrived', 'fulfilled'</td>
                            </tr>
                            <tr>
                                <td><code>day_of_week</code></td>
                                <td>string</td>
                                <td>Día de la semana en inglés (Monday, Tuesday, etc.)</td>
                            </tr>
                            <tr>
                                <td><code>day_of_week_es</code></td>
                                <td>string</td>
                                <td>Día de la semana en español (Lunes, Martes, etc.)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="endpoint" id="practitioner-availability">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/practitioners/{id}/availability</span>
                </div>

                <p>Obtiene la disponibilidad de horarios de un médico específico para una fecha determinada.</p>

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
                                <td><code>id</code></td>
                                <td>integer</td>
                                <td>ID del médico</td>
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
                                <td><code>date</code></td>
                                <td>date</td>
                                <td><span class="param-required">Sí</span></td>
                                <td>Fecha para consultar disponibilidad (YYYY-MM-DD, hoy o futura)</td>
                            </tr>
                            <tr>
                                <td><code>duration</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Duración de la cita en minutos (15-180, por defecto: 30)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/practitioners/1/availability?date=2024-02-15&duration=30
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Disponibilidad obtenida exitosamente</span>
                    </div>
                    <div class="code-block">
{
    "date": "2024-02-15",
    "practitioner": {
        "id": 1,
        "name": "Dr. Juan Carlos Pérez",
        "specialties": ["Cardiología"]
    },
    "working_hours": {
        "start": "08:00",
        "end": "17:00",
        "lunch_break": {
            "start": "12:00",
            "end": "13:00"
        }
    },
    "available_slots": [
        {
            "time": "08:00",
            "available": true,
            "end_time": "08:30"
        },
        {
            "time": "08:30",
            "available": true,
            "end_time": "09:00"
        },
        {
            "time": "09:00",
            "available": false,
            "reason": "Cita existente"
        },
        {
            "time": "09:30",
            "available": true,
            "end_time": "10:00"
        }
    ],
    "total_available": 12,
    "next_available": "08:00"
}
                    </div>

                    <div class="response-codes">
                        <span class="status-code status-404">404</span>
                        <span>Médico no encontrado</span>
                    </div>
                    <div class="code-block">
{
    "message": "Médico no encontrado"
}
                    </div>

                    <div class="response-codes">
                        <span class="status-code status-422">422</span>
                        <span>Parámetros de validación incorrectos</span>
                    </div>
                    <div class="code-block">
{
    "message": "Los datos proporcionados no son válidos.",
    "errors": {
        "date": ["La fecha debe ser igual o posterior a hoy"],
        "duration": ["La duración debe estar entre 15 y 180 minutos"]
    }
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="practitioner-consulting-rooms">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/practitioners/{id}/consulting-rooms</span>
                </div>

                <p>Obtiene los consultorios disponibles para un médico específico.</p>

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
                                <td><code>id</code></td>
                                <td>integer</td>
                                <td>ID del médico</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Consultorios del médico</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "name": "Consultorio 101",
            "location": "Primer piso",
            "description": "Consultorio equipado para cardiología",
            "capacity": 1,
            "branch": {
                "id": 1,
                "name": "Sucursal Principal",
                "address": "Calle Principal #123"
            },
            "equipment": [
                "Electrocardiograma",
                "Esfigmomanómetro",
                "Estetoscopio digital"
            ]
        }
    ]
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="practitioner-service-catalog">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/practitioners/{id}/service-catalog</span>
                </div>

                <p>Obtiene el catálogo de servicios disponibles para un médico específico.</p>

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
                                <td><code>id</code></td>
                                <td>integer</td>
                                <td>ID del médico</td>
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
                                <td><code>service_type</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por tipo de servicio</td>
                            </tr>
                            <tr>
                                <td><code>specialty</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por especialidad médica</td>
                            </tr>
                            <tr>
                                <td><code>cpt_code</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por código CPT</td>
                            </tr>
                            <tr>
                                <td><code>complexity</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por nivel de complejidad</td>
                            </tr>
                            <tr>
                                <td><code>covered_by_insurance</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar servicios cubiertos por seguro</td>
                            </tr>
                            <tr>
                                <td><code>requires_authorization</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar servicios que requieren autorización</td>
                            </tr>
                            <tr>
                                <td><code>min_price</code></td>
                                <td>decimal</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Precio mínimo (usar con max_price)</td>
                            </tr>
                            <tr>
                                <td><code>max_price</code></td>
                                <td>decimal</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Precio máximo (usar con min_price)</td>
                            </tr>
                            <tr>
                                <td><code>search</code></td>
                                <td>string</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Búsqueda en nombre, descripción y códigos</td>
                            </tr>
                            <tr>
                                <td><code>per_page</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Elementos por página (1-100, default: 20)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/practitioners/1/service-catalog?specialty=cardiologia&covered_by_insurance=true&per_page=10
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuesta Exitosa</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Catálogo de servicios obtenido correctamente</span>
                    </div>
                    <div class="code-block">
{
    "practitioner": {
        "id": 1,
        "name": "Dr. Juan Pérez"
    },
    "data": [
        {
            "id": 1,
            "fhir_id": "service-12345",
            "code": "CAR_0001",
            "cpt_code": "93000",
            "name": "Electrocardiograma",
            "description": "Registro de la actividad eléctrica del corazón",
            "service_type": "diagnostic",
            "base_price": 50.00,
            "effective_price": 57.50,
            "currency": "USD",
            "duration_minutes": 30,
            "estimated_duration_hours": 0.5,
            "specialty": "cardiologia",
            "complexity": "low",
            "requires_authorization": false,
            "covered_by_insurance": true,
            "patient_copay": 10.00,
            "insurance_allowable": 45.00,
            "is_active": true,
            "is_currently_available": true,
            "effective_date": "2024-01-01",
            "expiration_date": null,
            "profit_margin": 20.00,
            "profit_margin_percentage": 53.33,
            "client": {
                "id": 1,
                "name": "Hospital Central",
                "code": "HC001"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 25,
        "last_page": 3,
        "from": 1,
        "to": 10,
        "has_more_pages": true
    },
    "filters_applied": {
        "specialty": "cardiologia",
        "covered_by_insurance": true,
        "per_page": 10
    }
}
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas de Error</h3>
                    <div class="response-codes">
                        <span class="status-code status-404">404</span>
                        <span>Médico no encontrado</span>
                    </div>
                    <div class="code-block">
{
    "message": "Médico no encontrado"
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="medical-specialities">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/medical-specialities</span>
                </div>

                <p>Obtiene la lista de especialidades médicas disponibles en el sistema.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {token}
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Lista de especialidades médicas</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "name": "Medicina General",
            "description": "Atención médica general y preventiva",
            "practitioners_count": 15
        },
        {
            "id": 2,
            "name": "Pediatría",
            "description": "Especialidad médica dedicada al cuidado de bebés, niños y adolescentes",
            "practitioners_count": 8
        },
        {
            "id": 3,
            "name": "Cardiología",
            "description": "Especialidad médica que se ocupa de las enfermedades del corazón",
            "practitioners_count": 5
        },
        {
            "id": 4,
            "name": "Ginecología y Obstetricia",
            "description": "Especialidad médica y quirúrgica que trata las enfermedades del sistema reproductor femenino",
            "practitioners_count": 6
        }
    ]
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="consulting-rooms">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/consulting-rooms</span>
                </div>

                <p>Obtiene la lista de consultorios disponibles en todas las sucursales.</p>

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
                                <td><code>branch_id</code></td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Filtrar por sucursal específica</td>
                            </tr>
                            <tr>
                                <td><code>available</code></td>
                                <td>boolean</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Mostrar solo consultorios disponibles (true/false)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/consulting-rooms?branch_id=1&available=true
Authorization: Bearer 1|abcd1234...
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Lista de consultorios</span>
                    </div>
                    <div class="code-block">
{
    "data": [
        {
            "id": 1,
            "name": "Consultorio 101",
            "location": "Primer piso",
            "description": "Consultorio general equipado",
            "capacity": 1,
            "active": true,
            "branch": {
                "id": 1,
                "name": "Sucursal Principal",
                "address": "Calle Principal #123",
                "city": "Ciudad de Panamá"
            },
            "current_availability": true,
            "next_appointment": "2024-02-15 10:00:00"
        }
    ]
}
                    </div>
                </div>
            </div>

            <!-- API v1 Endpoints -->
            <h2 style="color: #2d3748; margin: 3rem 0 2rem 0; padding: 1rem; background: #f7fafc; border-left: 4px solid #4299e1; border-radius: 4px;">
                <i class="fas fa-code"></i> API v1 - Integración Externa
            </h2>

            <div class="note">
                <strong>Autenticación API v1:</strong> Estos endpoints requieren un token API válido con los scopes apropiados.
                <div class="code-block" style="margin-top: 0.5rem;">Authorization: Bearer {api-token}</div>
            </div>

            <div class="endpoint" id="v1-list-practitioners">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/v1/practitioners</span>
                </div>

                <p>Endpoint idéntico al de Sanctum pero con autenticación por token API. Incluye los horarios ocupados de la próxima semana.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {api-token}
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <p>Los mismos parámetros que <a href="#list-practitioners">GET /practitioners</a></p>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/v1/practitioners?page=1&per_page=5
Authorization: Bearer mdt_abc123...
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Respuesta</h3>
                    <p>Formato de respuesta idéntico al endpoint de Sanctum.</p>
                </div>
            </div>

            <div class="endpoint" id="v1-practitioner-availability">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/v1/practitioners/{id}/availability</span>
                </div>

                <p>Disponibilidad de horarios para integración externa.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {api-token}
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros</h3>
                    <p>Los mismos parámetros que <a href="#practitioner-availability">GET /practitioners/{id}/availability</a></p>
                </div>

                <div class="section">
                    <h3>Parámetros Específicos</h3>
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
                                <td>date</td>
                                <td>string</td>
                                <td><span class="param-required">Sí</span></td>
                                <td>Fecha de inicio en formato YYYY-MM-DD (debe ser hoy o futura)</td>
                            </tr>
                            <tr>
                                <td>duration</td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Duración de cada slot en minutos (15-480, default: 30)</td>
                            </tr>
                            <tr>
                                <td>days</td>
                                <td>integer</td>
                                <td><span class="param-optional">No</span></td>
                                <td>Número de días a consultar (1-14, default: 1)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/v1/practitioners/1/availability?date=2024-02-15&duration=30&days=3
Authorization: Bearer mdt_abc123...
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Respuesta Exitosa</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Disponibilidad obtenida correctamente</span>
                    </div>
                    <div class="code-block">
{
    "practitioner": {
        "id": 1,
        "name": "Dr. Juan Pérez"
    },
    "duration_minutes": 30,
    "availability": [
        {
            "date": "2024-02-15",
            "day_name": "Jueves",
            "day_of_week": 4,
            "slots": [
                {
                    "time": "09:00",
                    "end_time": "09:30",
                    "available": true,
                    "reason": null
                },
                {
                    "time": "09:30",
                    "end_time": "10:00",
                    "available": true,
                    "reason": null
                },
                {
                    "time": "10:00",
                    "end_time": "10:30",
                    "available": false,
                    "reason": "Cita existente"
                },
                {
                    "time": "10:30",
                    "end_time": "11:00",
                    "available": true,
                    "reason": null
                },
                {
                    "time": "12:00",
                    "end_time": "12:30",
                    "available": false,
                    "reason": "Hora de almuerzo"
                },
                {
                    "time": "14:00",
                    "end_time": "14:30",
                    "available": true,
                    "reason": null
                }
            ]
        },
        {
            "date": "2024-02-16",
            "day_name": "Viernes",
            "day_of_week": 5,
            "slots": [
                {
                    "time": "09:00",
                    "end_time": "09:30",
                    "available": true,
                    "reason": null
                },
                {
                    "time": "09:30",
                    "end_time": "10:00",
                    "available": false,
                    "reason": "Cita existente"
                }
            ]
        },
        {
            "date": "2024-02-17",
            "day_name": "Sábado",
            "day_of_week": 6,
            "slots": [],
            "reason": "Día no laborable"
        }
    ],
    "summary": {
        "total_slots": 8,
        "available_slots": 5,
        "occupancy_rate": 37.5
    }
}
                    </div>
                </div>

                <div class="section">
                    <h3>Respuestas de Error</h3>
                    <div class="response-codes">
                        <span class="status-code status-404">404</span>
                        <span>Médico no encontrado</span>
                    </div>
                    <div class="code-block">
{
    "message": "Médico no encontrado"
}
                    </div>

                    <div class="response-codes">
                        <span class="status-code status-422">422</span>
                        <span>Parámetros de validación incorrectos</span>
                    </div>
                    <div class="code-block">
{
    "message": "Parámetros de validación incorrectos",
    "errors": {
        "date": ["La fecha debe ser hoy o futura"],
        "duration": ["La duración debe estar entre 15 y 480 minutos"]
    }
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="v1-practitioner-consulting-rooms">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/v1/practitioners/{id}/consulting-rooms</span>
                </div>

                <p>Consultorios disponibles para un médico específico (integración externa).</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {api-token}
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/v1/practitioners/1/consulting-rooms
Authorization: Bearer mdt_abc123...
Accept: application/json
                    </div>
                </div>
            </div>

            <div class="endpoint" id="v1-practitioner-service-catalog">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/v1/practitioners/{id}/service-catalog</span>
                </div>

                <p>Catálogo de servicios disponibles para un médico específico (integración externa).</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {api-token}
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Parámetros de Consulta</h3>
                    <p>Los mismos parámetros que <a href="#practitioner-service-catalog">GET /practitioners/{id}/service-catalog</a></p>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/v1/practitioners/1/service-catalog?specialty=cardiologia&per_page=5
Authorization: Bearer mdt_abc123...
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Respuesta Exitosa</h3>
                    <div class="response-codes">
                        <span class="status-code status-200">200</span>
                        <span>Catálogo de servicios obtenido correctamente</span>
                    </div>
                    <div class="code-block">
{
    "practitioner": {
        "id": 1,
        "name": "Dr. Juan Pérez"
    },
    "data": [
        {
            "id": 1,
            "code": "CAR_0001",
            "cpt_code": "93000",
            "name": "Electrocardiograma",
            "description": "Registro de la actividad eléctrica del corazón",
            "service_type": "diagnostic",
            "base_price": 50.00,
            "effective_price": 57.50,
            "duration_minutes": 30,
            "specialty": "cardiologia",
            "complexity": "low",
            "requires_authorization": false,
            "covered_by_insurance": true,
            "is_currently_available": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 5,
        "total": 12,
        "last_page": 3,
        "has_more_pages": true
    }
}
                    </div>
                </div>
            </div>

            <div class="endpoint" id="v1-medical-specialities">
                <div class="endpoint-header">
                    <span class="method get">GET</span>
                    <span class="endpoint-url">/api/v1/medical-specialities</span>
                </div>

                <p>Lista de especialidades médicas para integración externa.</p>

                <div class="section">
                    <h3>Headers Requeridos</h3>
                    <div class="code-block">
Authorization: Bearer {api-token}
Accept: application/json
                    </div>
                </div>

                <div class="section">
                    <h3>Ejemplo de Petición</h3>
                    <div class="code-block">
GET /api/v1/medical-specialities
Authorization: Bearer mdt_abc123...
Accept: application/json
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h4>Información Adicional sobre Médicos</h4>
                <ul style="margin: 0.5rem 0; padding-left: 2rem;">
                    <li><strong>Horarios de Trabajo:</strong> Los médicos tienen horarios configurables por día de la semana mediante la tabla <code>user_working_hours</code>. Si no hay configuración personalizada, se usan horarios por defecto (08:00-17:00, lunes a viernes)</li>
                    <li><strong>Múltiples Especialidades:</strong> Un médico puede tener múltiples especialidades</li>
                    <li><strong>Multi-Sucursal:</strong> Los médicos pueden trabajar en múltiples sucursales</li>
                    <li><strong>Disponibilidad en Tiempo Real:</strong> La disponibilidad se calcula considerando citas existentes, horarios de trabajo personalizados y horarios de almuerzo (12:00-13:00 por defecto)</li>
                    <li><strong>Horarios de la Próxima Semana:</strong> El endpoint de listado incluye automáticamente los horarios ocupados de la próxima semana (lunes a domingo) con citas de tipo 'booked', 'arrived' y 'fulfilled'</li>
                    <li><strong>Planificación Avanzada:</strong> La información de horarios permite a los usuarios identificar rápidamente días con mayor/menor disponibilidad para agendar nuevas citas</li>
                    <li><strong>Información Multilingüe:</strong> Los días de la semana se proporcionan tanto en inglés como en español para mayor usabilidad</li>
                </ul>
            </div>

            <div class="note">
                <strong>Nueva Funcionalidad:</strong> A partir de esta versión, la lista de médicos incluye automáticamente información sobre los horarios ocupados de la próxima semana. Esta funcionalidad mejora significativamente la experiencia de planificación y agendamiento de citas sin requerir peticiones adicionales a la API.
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
</body>
</html>
