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
            <p>El Dashboard de SAMI está organizado visualmente para que la información fluya desde indicadores rápidos hasta análisis profundos. A continuación se detallan sus componentes en orden de visualización:</p>
            
            <h3 class="mt-4"><i class="fas fa-chart-bar me-2 text-primary"></i>1. Indicadores de Actividad</h3>
            <p>Situados en la parte superior, ofrecen una visión instantánea de los números clave del periodo:</p>
            <div class="row">
                <div class="col-md-3">
                    <div class="step-card text-center">
                        <div class="step-title">Pacientes nuevos</div>
                        <p>Total de pacientes registrados por primera vez en el sistema durante el mes actual.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card text-center">
                        <div class="step-title">Pacientes activos</div>
                        <p>Contabiliza los pacientes que han tenido consultas o movimientos recientes en su práctica.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card text-center">
                        <div class="step-title">Citas del mes</div>
                        <p>El volumen total de citas gestionadas en el transcurso del mes vigente.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-card text-center">
                        <div class="step-title">Consultas sin finalizar</div>
                        <p>Recordatorio crítico de actos médicos que han sido iniciados pero aún no se han cerrado formalmente.</p>
                    </div>
                </div>
            </div>
            <div>
                <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p1.png') }}" alt="" style="width: 100%;">
            </div>

            <h3 class="mt-4"><i class="fas fa-calendar-check me-2 text-info"></i>2. Seguimiento de Citas</h3>
            <div class="row">
                <div class="col-md-7">
                    <div class="step-card">
                        <div class="step-title">Citas del Año</div>
                        <p>Un gráfico interactivo que muestra la tendencia mensual de su volumen de pacientes, permitiéndole comparar el rendimiento a través del tiempo.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p2.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="step-card">
                        <div class="step-title">Citas para hoy</div>
                        <p>Su agenda diaria en tiempo real. Muestra las próximas citas, el nombre del paciente y permite acceso directo para iniciar la consulta.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p3.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-hourglass-half me-2 text-warning"></i>3. Análisis de Tiempos</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Tiempo Promedio: Solicitud → Atención</div>
                        <p>Mide el tiempo de espera desde que el paciente solicita su cita hasta que es atendido. Vital para medir la satisfacción del paciente.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p31.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Duración Promedio de Consultas</div>
                        <p>Calcula el tiempo promedio real que usted dedica a cada paciente durante la consulta médica.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p32.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-users-cog me-2 text-success"></i>4. Demografía de Pacientes</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Pacientes por Género</div>
                        <p>Distribución visual de su población clínica segmentada por sexo.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p33.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Pacientes por Rango de Edad</div>
                        <p>Análisis demográfico que muestra qué grupos etarios (niños, adultos, etc.) frecuentan más su consulta.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p34.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-list-ol me-2 text-danger"></i>5. Patologías y Tratamientos</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Top 5 Condiciones Activas</div>
                        <p>Muestra los 5 diagnósticos CIE-10 más recurrentes en su práctica, facilitando la identificación de patologías comunes.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p41.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Top 5 Medicamentos Prescritos</div>
                        <p>Listado de los fármacos que más prescribe, incluyendo detalles de frecuencia y número de pacientes tratados.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p42.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4"><i class="fas fa-tachometer-alt me-2 text-secondary"></i>6. Eficiencia Clínica</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Efectividad de Atención</div>
                        <p>Proporciona métricas avanzadas como la tasa de completado, flujo de estados de citas y puntos de abandono (inasistencias).</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p51.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Horarios de Mayor Actividad</div>
                        <p>Un mapa de calor (heatmap) que resalta los días y horas con mayor volumen de pacientes atendidos, ideal para planificar su horario laboral.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p52.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>


            <h3 class="mt-4"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>7. Gestión Financiera</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Facturación por Sede</div>
                        <p>Resumen monetario de los servicios prestados desglosado por cada una de las clínicas o sucursales donde usted atiende.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p6.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="step-card">
                        <div class="step-title">Facturación vs Cobros</div>
                        <p>Compara el monto total de lo facturado frente a lo que efectivamente ha sido pagado, indicando su eficiencia en la recaudación.</p>
                        <div>
                            <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p8.png') }}" alt="" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-card mt-4">
                <div class="step-title"><i class="fas fa-microscope me-2 text-info"></i>8. Diagnósticos por Grupo Etario</div>
                <div class="step-content">
                    <p>Muestra la incidencia de diagnósticos específicos segmentados por los rangos de edad de sus pacientes. Este análisis epidemiológico avanzado le permite ver qué patologías son más prevalentes en cada etapa de vida dentro de su población.</p>
                    <div>
                        <img src="{{ asset('images/tutorial/doctor-dashboard/dd-p9.png') }}" alt="" style="width: 100%;">
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

            <div>
                <img src="{{ asset('images/tutorial/doctor-dashboard/dd-settings.png') }}" alt="" style="width: 100%;">
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
