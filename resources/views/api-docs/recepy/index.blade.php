<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Sistema Recepy</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5em;
            font-weight: 300;
        }

        .header p {
            margin: 0;
            font-size: 1.2em;
            opacity: 0.9;
        }

        .overview {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .overview h2 {
            color: #667eea;
            margin-top: 0;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .feature {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .feature h3 {
            margin-top: 0;
            color: #333;
        }

        .feature ul {
            padding-left: 20px;
            margin: 0;
        }

        .feature li {
            margin-bottom: 5px;
        }

        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .doc-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .doc-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .doc-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }

        .doc-card-header h3 {
            margin: 0 0 10px 0;
            font-size: 1.3em;
        }

        .doc-card-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9em;
        }

        .doc-card-body {
            padding: 20px;
        }

        .doc-card-body ul {
            margin: 0;
            padding-left: 20px;
        }

        .doc-card-body li {
            margin-bottom: 8px;
        }

        .doc-card-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            text-align: center;
        }

        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background: #5a67d8;
            text-decoration: none;
            color: white;
        }

        .base-url {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            margin: 20px 0;
            text-align: center;
            color: #334155;
        }

        .tech-stack {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tech-stack h2 {
            color: #667eea;
            margin-top: 0;
        }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .tech-item {
            background: #e2e8f0;
            color: #334155;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            color: #64748b;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header h1 {
                font-size: 2em;
            }

            .features,
            .docs-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Sistema Recepy</h1>
        <p>API para Generación de Recetas Médicas</p>
    </div>

    <div class="overview">
        <h2>🏥 Descripción General</h2>
        <p>
            El Sistema Recepy es una API RESTful completa para la generación y gestión de recetas médicas digitales.
            Diseñado específicamente para ser consumido por aplicaciones móviles Flutter, proporciona todas las
            funcionalidades necesarias para que los médicos puedan crear, gestionar y personalizar sus recetas
            de manera digital y profesional.
        </p>

        <div class="base-url">
            Base URL: https://meditecpty.com/api
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <h3>🔐 Sistema de Autenticación</h3>
            <ul>
                <li>Login y registro de usuarios</li>
                <li>Autenticación con tokens Bearer</li>
                <li>Recuperación de contraseñas</li>
                <li>Gestión de sesiones segura</li>
            </ul>
        </div>

        <div class="feature">
            <h3>👨‍⚕️ Perfiles de Doctores</h3>
            <ul>
                <li>Información profesional completa</li>
                <li>Upload de logo, firma y sello</li>
                <li>Datos de contacto del consultorio</li>
                <li>Número de registro médico</li>
            </ul>
        </div>

        <div class="feature">
            <h3>📝 Gestión de Recetas</h3>
            <ul>
                <li>Creación de recetas completas</li>
                <li>Información detallada del paciente</li>
                <li>Diagnósticos y notas adicionales</li>
                <li>Control de estados de recetas</li>
            </ul>
        </div>

        <div class="feature">
            <h3>💊 Medicamentos Detallados</h3>
            <ul>
                <li>Medicamentos línea por línea</li>
                <li>Dosis, frecuencia y duración</li>
                <li>Instrucciones específicas</li>
                <li>Reordenamiento dinámico</li>
            </ul>
        </div>
    </div>

    <div class="tech-stack">
        <h2>🛠️ Stack Tecnológico</h2>
        <p>El sistema está construido con tecnologías modernas y robustas para garantizar rendimiento y escalabilidad:</p>
        <div class="tech-list">
            <span class="tech-item">Laravel 12</span>
            <span class="tech-item">PHP 8.3</span>
            <span class="tech-item">MySQL/PostgreSQL</span>
            <span class="tech-item">Laravel Sanctum</span>
            <span class="tech-item">RESTful API</span>
            <span class="tech-item">JSON Responses</span>
            <span class="tech-item">File Storage</span>
            <span class="tech-item">Validation</span>
        </div>
    </div>

    <h2 style="text-align: center; color: #667eea; margin: 40px 0 30px 0;">📚 Documentación de API</h2>

    <div class="docs-grid">
        <div class="doc-card">
            <div class="doc-card-header">
                <h3>🔐 Autenticación</h3>
                <p>Sistema de login y gestión de usuarios</p>
            </div>
            <div class="doc-card-body">
                <ul>
                    <li>POST /auth/login - Iniciar sesión</li>
                    <li>POST /auth/register - Registrar usuario</li>
                    <li>GET /auth/user - Obtener usuario</li>
                    <li>POST /auth/logout - Cerrar sesión</li>
                    <li>POST /auth/forgot-password - Recuperar contraseña</li>
                </ul>
            </div>
            <div class="doc-card-footer">
                <a href="authentication.md" class="btn" target="_blank">Ver Documentación</a>
            </div>
        </div>

        <div class="doc-card">
            <div class="doc-card-header">
                <h3>👨‍⚕️ Perfiles de Doctores</h3>
                <p>Gestión de información profesional médica</p>
            </div>
            <div class="doc-card-body">
                <ul>
                    <li>GET /recepy/doctor-profiles - Listar perfiles</li>
                    <li>POST /recepy/doctor-profiles - Crear perfil</li>
                    <li>PUT /recepy/doctor-profiles/{id} - Actualizar</li>
                    <li>DELETE /recepy/doctor-profiles/{id} - Eliminar</li>
                    <li>GET /recepy/users/{userId}/doctor-profile - Por usuario</li>
                </ul>
            </div>
            <div class="doc-card-footer">
                <a href="doctor-profiles.md" class="btn" target="_blank">Ver Documentación</a>
            </div>
        </div>

        <div class="doc-card">
            <div class="doc-card-header">
                <h3>📋 Recetas Médicas</h3>
                <p>Creación y gestión de recetas completas</p>
            </div>
            <div class="doc-card-body">
                <ul>
                    <li>GET /recepy/prescriptions - Listar con filtros</li>
                    <li>POST /recepy/prescriptions - Crear receta</li>
                    <li>PUT /recepy/prescriptions/{id} - Actualizar</li>
                    <li>PATCH /prescriptions/{id}/status - Cambiar estado</li>
                    <li>DELETE /recepy/prescriptions/{id} - Eliminar</li>
                </ul>
            </div>
            <div class="doc-card-footer">
                <a href="prescriptions.md" class="btn" target="_blank">Ver Documentación</a>
            </div>
        </div>

        <div class="doc-card">
            <div class="doc-card-header">
                <h3>💊 Medicamentos</h3>
                <p>Gestión detallada de medicamentos por receta</p>
            </div>
            <div class="doc-card-body">
                <ul>
                    <li>GET /prescriptions/{id}/medications - Listar</li>
                    <li>POST /prescriptions/{id}/medications - Agregar</li>
                    <li>PUT /prescriptions/{id}/medications/{medicationId} - Actualizar</li>
                    <li>PUT /prescriptions/{id}/medications/order - Reordenar</li>
                    <li>PUT /prescriptions/{id}/medications/bulk-update - Actualización masiva</li>
                </ul>
            </div>
            <div class="doc-card-footer">
                <a href="medications.md" class="btn" target="_blank">Ver Documentación</a>
            </div>
        </div>
    </div>

    <div class="overview">
        <h2>🚀 Características Principales</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
            <div>
                <h3>✅ Autenticación Segura</h3>
                <p>Sistema basado en Laravel Sanctum con tokens Bearer para máxima seguridad.</p>
            </div>
            <div>
                <h3>📱 Optimizado para Flutter</h3>
                <p>Respuestas JSON estructuradas y consistentes, perfectas para aplicaciones móviles.</p>
            </div>
            <div>
                <h3>🔄 API RESTful Completa</h3>
                <p>Operaciones CRUD completas con validación robusta y manejo de errores.</p>
            </div>
            <div>
                <h3>📁 Gestión de Archivos</h3>
                <p>Upload y gestión de imágenes para logos, firmas y sellos médicos.</p>
            </div>
            <div>
                <h3>🔍 Filtros Avanzados</h3>
                <p>Búsqueda y filtrado por múltiples criterios con paginación eficiente.</p>
            </div>
            <div>
                <h3>📊 Estados de Workflow</h3>
                <p>Control completo del estado de recetas: activas, completadas, canceladas.</p>
            </div>
        </div>
    </div>

    <div class="overview">
        <h2>🎯 Casos de Uso</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
            <div>
                <h3>👩‍⚕️ Para Médicos</h3>
                <ul>
                    <li>Configurar perfil profesional con datos de contacto</li>
                    <li>Subir logo del consultorio, firma digital y sello</li>
                    <li>Crear recetas digitales con información completa del paciente</li>
                    <li>Gestionar medicamentos línea por línea con instrucciones detalladas</li>
                    <li>Controlar el estado de las recetas emitidas</li>
                </ul>
            </div>
            <div>
                <h3>📱 Para Desarrolladores</h3>
                <ul>
                    <li>Integración sencilla con aplicaciones Flutter</li>
                    <li>Documentación completa con ejemplos de código</li>
                    <li>Respuestas consistentes y predecibles</li>
                    <li>Manejo robusto de errores con códigos HTTP estándar</li>
                    <li>Validación automática de datos de entrada</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        <h3>📞 Soporte y Contacto</h3>
        <p>
            Para soporte técnico, preguntas sobre la implementación o reportar problemas,
            contacta al equipo de desarrollo.
        </p>
        <p>
            <strong>Versión:</strong> 1.0.0 •
            <strong>Última actualización:</strong> Enero 2025
        </p>
    </div>
</body>
</html>
