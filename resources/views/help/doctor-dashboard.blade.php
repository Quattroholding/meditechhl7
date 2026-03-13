<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Dashboard Médico - Centro de Ayuda SAMI</title>
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
            --dashboard-color: #283593;
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

        .help-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
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
            text-decoration: none;
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
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
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

        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            color: #1a237e;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
        }

        .step-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
            border-left: 4px solid #1a237e;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: #1a237e;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 15px;
        }

        .step-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .info-box.tip {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .info-box.note {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
        }

        .info-box-title {
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .screenshot-placeholder {
            background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
            border: 2px dashed #3949ab;
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
            color: #1a237e;
            margin-bottom: 15px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(26, 35, 126, 0.4);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 9999;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 992px) {
            .help-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .help-content {
                margin-left: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    @include('help.sidebar', ['active' => 'doctor-dashboard'])

    <main class="help-content">
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Dashboard Médico</li>
            </ol>
        </nav>

        <div class="module-header">
            <h1><i class="fas fa-tachometer-alt me-3"></i>Dashboard Médico</h1>
            <p>Guía completa sobre el panel principal del médico, sus indicadores y cómo personalizarlo.</p>
        </div>

        <div class="content-section">
            <h2>Introducción</h2>
            <p>El Dashboard Médico es su centro de comando en SAMI. Está diseñado para proporcionar una vista rápida de su actividad diaria, estadísticas de pacientes y rendimiento clínico a través de widgets dinámicos e interactivos.</p>
            
            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle"></i> Información General
                </div>
                <p>El dashboard se carga de forma progresiva para asegurar que la información más importante esté disponible de inmediato sin afectar el rendimiento del sistema.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>Widgets del Dashboard</h2>
            <p>SAMI ofrece una variedad de widgets que puede habilitar o deshabilitar según sus necesidades. A continuación, se detalla cada uno de ellos:</p>
            
            <h3 class="mt-4"><i class="fas fa-calendar-check me-2 text-primary"></i>Gestión de Citas</h3>
            
            <!--<div class="step-card">
                <div class="step-title">Citas Recientes</div>
                <div class="step-content">
                    <p>Muestra un listado de los pacientes con citas próximas a la hora actual. Permite visualizar rápidamente el nombre del paciente, la hora de la cita y el tipo de consulta. Incluye botones de acceso directo para iniciar o retomar la consulta médica de manera eficiente.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Widget de Citas Recientes</p>
                </div>
            </div-->

            <div class="step-card">
                <div class="step-title">Consultas en Progreso</div>
                <div class="step-content">
                    <p>Muestra los pacientes que se encuentran actualmente en la sala de espera o cuya consulta ya ha sido iniciada pero no finalizada. Es ideal para llevar un control del flujo de pacientes en tiempo real.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Widget de Consultas en Progreso</p>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-users me-2 text-success"></i>Estadísticas de Pacientes</h3>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-title">Pacientes Nuevos</div>
                        <p>Contabiliza los pacientes registrados por primera vez en el período seleccionado.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-title">Pacientes Antiguos</div>
                        <p>Muestra el número de pacientes recurrentes que han vuelto a consulta.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card text-center">
                        <div class="step-title">Pacientes Activos</div>
                        <p>Indica el total de pacientes que mantienen una relación activa con su práctica.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-chart-line me-2 text-info"></i>Análisis de Rendimiento</h3>
            
            <div class="step-card">
                <div class="step-title">Tiempos de Atención (Espera y Duración)</div>
                <div class="step-content">
                    <p>Estos widgets miden el tiempo promedio que un paciente espera desde su llegada hasta ser atendido, y la duración real de la consulta. Ayudan a identificar cuellos de botella y mejorar la puntualidad.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Gráficos de Tiempo de Espera y Duración</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">Efectividad de Consulta</div>
                <div class="step-content">
                    <p>Analiza el flujo de estados de las citas (desde el agendamiento hasta la finalización) para determinar qué porcentaje de citas se completan exitosamente frente a las cancelaciones o inasistencias.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Gráfico de Efectividad</p>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-stethoscope me-2 text-danger"></i>Demografía y Salud</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Pacientes por Género y Edad</div>
                        <p>Gráficos circulares y de barras que muestran la distribución demográfica de su población de pacientes.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Top Condiciones y Medicamentos</div>
                        <p>Listados automáticos de los diagnósticos más frecuentes y los medicamentos más recetados en su práctica.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-clock me-2 text-secondary"></i>Visualización de Carga</h3>
            
            <div class="step-card">
                <div class="step-title">Análisis de Citas (Mensual y Anual)</div>
                <div class="step-content">
                    <p>Gráficos de tendencia que comparan el volumen de citas mes a mes o año tras año.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Gráfico de Tendencia Anual</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-title">Mapa de Calor de Actividad</div>
                <div class="step-content">
                    <p>Una cuadrícula visual que muestra los días de la semana y las horas en los que hay mayor concentración de citas, permitiendo una mejor planificación de sus horarios.</p>
                </div>
                <div class="screenshot-placeholder">
                    <i class="fas fa-image"></i>
                    <p>Captura: Mapa de Calor</p>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-file-invoice-dollar me-2 text-warning"></i>Métricas Administrativas</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Facturación por Sucursal</div>
                        <p>Resumen monetario de lo facturado en cada una de sus sedes de atención.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Tasa de Recaudación</div>
                        <p>Muestra el porcentaje de facturas pagadas frente a las pendientes, ayudando al control financiero.</p>
                        <div class="screenshot-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Personalización del Dashboard</h2>
            <p>Usted tiene el control total sobre lo que ve en su pantalla de inicio. Siga estos pasos para personalizar sus widgets:</p>
            
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-title">Abrir Configuración</div>
                <p>En la parte superior derecha del Dashboard, haga clic en el botón de configuración de widgets.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-title">Seleccionar Widgets</div>
                <p>En el modal emergente, podrá ver una lista de todos los widgets disponibles. Use los interruptores para activar o desactivar los que desee ver.</p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-title">Guardar Cambios</div>
                <p>Una vez ajustada su selección, el sistema aplicará los cambios automáticamente o al hacer clic en guardar, reflejando su nueva configuración personalizada.</p>
            </div>

            <div class="screenshot-placeholder">
                <i class="fas fa-cogs"></i>
                <p>Captura de Pantalla: Modal de Configuración de Widgets</p>
                <small>Haga clic en el icono de engranaje para gestionar sus widgets</small>
            </div>
        </div>

        <div class="content-section">
            <h2>Diseño Responsivo</h2>
            <p>El dashboard está optimizado para funcionar en cualquier dispositivo:</p>
            <ul>
                <li><strong>PC/Laptop:</strong> Vista completa con múltiples columnas para una visualización exhaustiva.</li>
                <li><strong>Tablets:</strong> Ajuste dinámico de los tiles para mantener la legibilidad.</li>
                <li><strong>Móviles:</strong> Los widgets se apilan verticalmente, permitiendo una navegación fluida con scroll táctil simple.</li>
            </ul>
        </div>

        <div class="info-box tip">
            <div class="info-box-title">
                <i class="fas fa-lightbulb"></i> Consejo Profesional
            </div>
            <p>Configure sus widgets más utilizados en las primeras posiciones para tener acceso inmediato a la información crítica apenas inicie sesión.</p>
        </div>
    </main>

    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
