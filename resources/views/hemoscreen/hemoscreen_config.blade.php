<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Configuración - HemoScreen Gateway</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title .icon {
            font-size: 2rem;
        }

        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .step-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .step h3 {
            display: inline-block;
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .step-content {
            margin-left: 50px;
            margin-top: 15px;
        }

        .step-content p {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .config-item {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 15px;
            margin: 8px 0;
            font-family: 'Courier New', monospace;
        }

        .config-item strong {
            color: #667eea;
            font-weight: bold;
            display: inline-block;
            min-width: 150px;
        }

        .config-item .value {
            color: #2d3748;
            background: #f7fafc;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .warning strong {
            color: #856404;
        }

        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .success strong {
            color: #155724;
        }

        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .info strong {
            color: #0c5460;
        }

        ul {
            margin: 15px 0 15px 25px;
        }

        ul li {
            margin: 8px 0;
            line-height: 1.8;
        }

        .highlight {
            background: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }

        .screenshot-placeholder {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #999;
            margin: 15px 0;
            background: #fafafa;
        }

        .footer {
            background: #2d3748;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }

        @media print {
            body {
                background: white;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🩺 Manual de Configuración</h1>
            <p>HemoScreen Gateway - Guía de Instalación y Configuración Completa</p>
        </div>

        <div class="content">
            <!-- PARTE 1: INSTALACIÓN DEL SOFTWARE -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">💿</span>
                    Parte 1: Instalación del Software Gateway
                </h2>

                <div class="step">
                    <span class="step-number">1</span>
                    <h3>Ejecutar el Instalador</h3>
                    <div class="step-content">
                        <p>Ejecuta el archivo <strong>HemoScreen Gateway Setup 1.0.0.exe</strong></p>
                        <div class="info">
                            <strong>📌 Nota:</strong> Puede aparecer una advertencia de Windows SmartScreen. Haz clic en "Más información" y luego en "Ejecutar de todas formas".
                        </div>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <h3>Elegir Ubicación de Instalación</h3>
                    <div class="step-content">
                        <p>El instalador te permitirá elegir dónde instalar la aplicación. La ubicación predeterminada es:</p>
                        <div class="code-block">C:\Users\[TuUsuario]\AppData\Local\Programs\HemoScreen Gateway\</div>
                        <p>Puedes cambiarla si lo prefieres, pero se recomienda dejar la predeterminada.</p>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <h3>Completar la Instalación</h3>
                    <div class="step-content">
                        <p>Sigue los pasos del instalador y haz clic en <strong>"Instalar"</strong>.</p>
                        <p>Una vez finalizada la instalación, se creará un acceso directo en el escritorio y en el menú de inicio.</p>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">4</span>
                    <h3>Iniciar la Aplicación</h3>
                    <div class="step-content">
                        <p>Ejecuta <strong>HemoScreen Gateway</strong> desde el acceso directo del escritorio.</p>
                        <div class="success">
                            <strong>✅ ¡Listo!</strong> La aplicación se abrirá y verás el panel de control principal.
                        </div>
                    </div>
                </div>
            </div>

            <!-- PARTE 2: CONFIGURACIÓN DE LA APLICACIÓN -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">⚙️</span>
                    Parte 2: Configuración de la Aplicación
                </h2>

                <div class="step">
                    <span class="step-number">1</span>
                    <h3>Detectar la IP de la PC</h3>
                    <div class="step-content">
                        <p>En la sección <strong>"Guía de Instalación Rápida"</strong> de la aplicación:</p>
                        <ul>
                            <li>Haz clic en el botón <strong>"Mostrar Guía"</strong></li>
                            <li>Presiona el botón <strong>"Detectar IP de esta PC"</strong></li>
                            <li>Anota la dirección IP que se muestra (por ejemplo: <span class="highlight">192.168.1.100</span>)</li>
                        </ul>
                        <div class="warning">
                            <strong>⚠️ Importante:</strong> Esta IP será necesaria para configurar el dispositivo HemoScreen. Guárdala para el siguiente paso.
                        </div>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <h3>Abrir Panel de Configuración</h3>
                    <div class="step-content">
                        <p>En la sección <strong>"⚙️ Configuración"</strong> de la aplicación:</p>
                        <ul>
                            <li>Haz clic en el botón <strong>"Mostrar"</strong></li>
                            <li>Se desplegará el formulario de configuración</li>
                        </ul>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <h3>Completar los Datos de Configuración</h3>
                    <div class="step-content">
                        <p>Ingresa los siguientes datos en el formulario:</p>

                        <div class="config-item">
                            <strong>🌐 URL de la API:</strong> <span class="value">https://tu-servidor.com</span>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Dirección del servidor SaaS de Meditech donde se enviarán los resultados.
                        </p>

                        <div class="config-item">
                            <strong>🔑 Token de API:</strong> <span class="value">mdt_xxxxxxxxxxxxx</span>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Token de autenticación proporcionado por Meditech SaaS.
                        </p>

                        <div class="config-item">
                            <strong>🔀 Tipo de Endpoint:</strong>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Selecciona según tu tipo de instalación:
                        </p>
                        <ul style="margin-left: 40px;">
                            <li><strong>HemoScreen Normal:</strong> Para integraciones estándar con el sistema completo</li>
                            <li><strong>HemoScreen Standalone:</strong> Para instalaciones independientes sin sistema médico completo</li>
                        </ul>

                        <div class="config-item">
                            <strong>🔢 Serial del Dispositivo:</strong> <span class="value">HS-LOCAL-01</span>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Identificador único de este equipo HemoScreen (puedes usar cualquier nombre descriptivo).
                        </p>

                        <div class="config-item">
                            <strong>🔌 Puerto TCP:</strong> <span class="value">5000</span>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Puerto donde el gateway escuchará las conexiones del HemoScreen. <strong>Dejar en 5000</strong> (valor recomendado).
                        </p>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">4</span>
                    <h3>Guardar Configuración</h3>
                    <div class="step-content">
                        <p>Haz clic en el botón <strong>"💾 Guardar Configuración"</strong></p>
                        <div class="success">
                            <strong>✅ Guardado exitoso:</strong> Verás un mensaje de confirmación y la aplicación aplicará los cambios automáticamente.
                        </div>
                        <div class="info">
                            <strong>📌 Nota:</strong> No es necesario reiniciar la aplicación. Los cambios se aplican de inmediato.
                        </div>
                    </div>
                </div>
            </div>

            <!-- PARTE 3: CONFIGURACIÓN DEL DISPOSITIVO HEMOSCREEN -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">🔬</span>
                    Parte 3: Configuración del Dispositivo HemoScreen
                </h2>

                <div class="warning">
                    <strong>⚠️ Requisito previo:</strong> Asegúrate de que el HemoScreen esté conectado a la <strong>misma red local</strong> que la PC donde está instalado el Gateway.
                </div>

                <div class="step">
                    <span class="step-number">1</span>
                    <h3>Acceder al Menú de Configuración</h3>
                    <div class="step-content">
                        <p>En el dispositivo HemoScreen:</p>
                        <ul>
                            <li>Toca el ícono de <strong>Configuración</strong> (engranaje) en la pantalla principal</li>
                            <li>Navega a <strong>Settings → Connectivity → LIS</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <h3>Configurar Conexión LIS</h3>
                    <div class="step-content">
                        <p>Ingresa los siguientes parámetros en el menú LIS:</p>

                        <div class="config-item">
                            <strong>Host IP:</strong> <span class="value">[La IP detectada en Parte 2, Paso 1]</span>
                        </div>
                        <p style="margin-left: 20px; margin-top: -5px; font-size: 0.9rem; color: #666;">
                            Ejemplo: 192.168.1.100
                        </p>

                        <div class="config-item">
                            <strong>Port:</strong> <span class="value">5000</span>
                        </div>

                        <div class="config-item">
                            <strong>Standard:</strong> <span class="value">POCT1-A</span>
                        </div>

                        <div class="config-item">
                            <strong>Protocol:</strong> <span class="value">TCP/IP (XML)</span>
                        </div>

                        <div class="info">
                            <strong>📌 Importante:</strong> Verifica que estos valores estén exactamente como se indica, especialmente el estándar POCT1-A y el protocolo TCP/IP (XML).
                        </div>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <h3>Configurar Ajustes Avanzados</h3>
                    <div class="step-content">
                        <p>En el menú <strong>Connectivity</strong> del HemoScreen:</p>
                        <ul>
                            <li>Activa la opción <strong>"Reenvío"</strong> (o "Resend" en inglés)</li>
                        </ul>

                        <p>En el submenú <strong>Connectivity → Ajustes</strong>:</p>
                        <ul>
                            <li>Activa <strong>"Modo Continuo"</strong> (Continuous Mode)</li>
                            <li>Activa <strong>"Consulta de Paciente"</strong> (Patient Query)</li>
                        </ul>

                        <div class="warning">
                            <strong>⚠️ ¿Por qué estos ajustes?</strong>
                            <ul style="margin-top: 10px;">
                                <li><strong>Reenvío:</strong> Permite que el equipo reenvíe resultados antiguos si es necesario</li>
                                <li><strong>Modo Continuo:</strong> Mantiene la conexión activa con el Gateway</li>
                                <li><strong>Consulta de Paciente:</strong> Permite consultar y reenviar resultados históricos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">4</span>
                    <h3>Probar la Conexión</h3>
                    <div class="step-content">
                        <p>En el menú LIS del HemoScreen:</p>
                        <ul>
                            <li>Busca y presiona el botón <strong>"Test LIS Connection"</strong></li>
                            <li>El equipo intentará conectarse al Gateway</li>
                        </ul>

                        <div class="success">
                            <strong>✅ Conexión exitosa:</strong> En la aplicación Gateway verás el estado cambiar a "Conectado" con un indicador verde.
                        </div>

                        <div class="warning">
                            <strong>⚠️ Si no conecta:</strong> Verifica que:
                            <ul style="margin-top: 10px;">
                                <li>Ambos dispositivos estén en la misma red</li>
                                <li>La IP ingresada sea correcta</li>
                                <li>El puerto 5000 no esté bloqueado por el firewall de Windows</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PARTE 4: VERIFICACIÓN FINAL -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">✅</span>
                    Parte 4: Verificación y Prueba Final
                </h2>

                <div class="step">
                    <span class="step-number">1</span>
                    <h3>Verificar Estados en el Gateway</h3>
                    <div class="step-content">
                        <p>En la aplicación Gateway, verifica que las tres tarjetas de estado muestren:</p>
                        <ul>
                            <li><strong>Estado del Dispositivo:</strong> 🟢 Conectado (verde)</li>
                            <li><strong>Cola de Mensajes:</strong> 0 mensajes pendientes</li>
                            <li><strong>Estado SaaS:</strong> 🟢 Online (verde)</li>
                        </ul>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <h3>Realizar una Prueba Real</h3>
                    <div class="step-content">
                        <p>Para confirmar que todo funciona correctamente:</p>
                        <ul>
                            <li>Realiza una prueba con una muestra de control en el HemoScreen</li>
                            <li>Observa el <strong>"Registro de Actividad"</strong> en el Gateway</li>
                            <li>Deberías ver mensajes indicando la recepción y envío del resultado</li>
                        </ul>

                        <div class="success">
                            <strong>✅ Prueba exitosa:</strong> Si ves el mensaje "Resultado enviado exitosamente al SaaS", ¡la integración está funcionando correctamente!
                        </div>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <h3>¿Qué hacer si hay Mensajes Pendientes?</h3>
                    <div class="step-content">
                        <p>Si por alguna razón el SaaS no estaba disponible o hubo un error:</p>
                        <ul>
                            <li>Los resultados se guardarán en la <strong>"Cola de Mensajes"</strong></li>
                            <li>El Gateway intentará reenviarlos automáticamente cada 15 segundos</li>
                            <li>También puedes hacer clic en <strong>"🔄 Reintentar Ahora"</strong> para procesarlos manualmente</li>
                        </ul>

                        <div class="info">
                            <strong>📌 Funcionamiento Offline:</strong> El Gateway puede trabajar sin conexión a internet. Los resultados se almacenan localmente y se enviarán automáticamente cuando la conexión se restablezca.
                        </div>
                    </div>
                </div>
            </div>

            <!-- SOLUCIÓN DE PROBLEMAS -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">🔧</span>
                    Solución de Problemas Comunes
                </h2>

                <div class="step">
                    <span class="step-number">1</span>
                    <h3>El HemoScreen no se conecta al Gateway</h3>
                    <div class="step-content">
                        <p><strong>Posibles causas y soluciones:</strong></p>
                        <ul>
                            <li><strong>Firewall de Windows:</strong> Agrega una excepción para el puerto 5000
                                <div class="code-block">Panel de Control → Sistema y Seguridad → Firewall de Windows → Configuración avanzada → Reglas de entrada → Nueva regla → Puerto TCP 5000</div>
                            </li>
                            <li><strong>IP incorrecta:</strong> Vuelve a detectar la IP en el Gateway y actualízala en el HemoScreen</li>
                            <li><strong>Red diferente:</strong> Confirma que ambos dispositivos estén en la misma subred (ejemplo: 192.168.1.x)</li>
                        </ul>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <h3>El Gateway no envía al SaaS</h3>
                    <div class="step-content">
                        <p><strong>Verifica:</strong></p>
                        <ul>
                            <li>Que la URL de la API sea correcta y esté accesible</li>
                            <li>Que el Token de API sea válido</li>
                            <li>Que haya conexión a internet</li>
                            <li>Revisa el <strong>"Registro de Actividad"</strong> para ver mensajes de error detallados</li>
                        </ul>
                    </div>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <h3>Mensajes duplicados en la cola</h3>
                    <div class="step-content">
                        <p>Si ves resultados duplicados o antiguos:</p>
                        <ul>
                            <li>Puedes hacer clic en <strong>"⚠️ Reiniciar Historial Local"</strong> para borrar el historial de sincronización</li>
                            <li>Esto permitirá que el equipo reenvíe todos los resultados históricos si es necesario</li>
                        </ul>
                        <div class="warning">
                            <strong>⚠️ Precaución:</strong> Solo usa esta opción si realmente necesitas reenviar resultados antiguos, ya que puede causar duplicados en el sistema.
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOTAS FINALES -->
            <div class="section">
                <h2 class="section-title">
                    <span class="icon">📝</span>
                    Notas Finales
                </h2>

                <div class="info">
                    <strong>💡 Recomendaciones:</strong>
                    <ul style="margin-top: 10px;">
                        <li>Mantén siempre el Gateway ejecutándose en segundo plano</li>
                        <li>No cierres la aplicación a menos que sea absolutamente necesario</li>
                        <li>Revisa periódicamente el <strong>"Registro de Actividad"</strong> para detectar posibles problemas</li>
                        <li>Si actualizas el software del HemoScreen, verifica que la configuración LIS se mantenga</li>
                    </ul>
                </div>

                <div class="success">
                    <strong>✅ ¡Configuración Completa!</strong>
                    <p style="margin-top: 10px;">
                        El sistema HemoScreen Gateway ahora está completamente configurado y listo para usar. Los resultados del equipo se enviarán automáticamente al sistema Meditech SaaS.
                    </p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>HemoScreen Gateway v1.0.0</strong></p>
            <p>Desarrollado por Meditech SaaS • Sistema de integración médica</p>
            <p style="margin-top: 10px; opacity: 0.8;">Para soporte técnico, contacta a soporte@meditech.com</p>
        </div>
    </div>
</body>
</html>
