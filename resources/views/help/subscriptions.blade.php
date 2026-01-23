<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Suscripciones - Centro de Ayuda SAMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --subscription-color: #ff9800;
            --dark-text: #212529;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-text);
        }
         /* Sidebar */
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

        .help-sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
        }

        .help-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        /* Breadcrumb */
        .help-breadcrumb {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .help-header {
            background: linear-gradient(135deg, #ff9800 0%, #fb8c00 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }
        .help-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .help-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        .help-header .breadcrumb {
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .help-header .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
        }

        .help-header .breadcrumb-item.active {
            color: #fff;
        }
        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .content-section h2 {
            color: #ff9800;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #fff3e0;
        }
        .content-section h3 {
            color: #333;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .step-card {
            background: linear-gradient(135deg, #fff3e0 0%, #fff 100%);
            border-left: 4px solid #ff9800;
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
            background: #ff9800;
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
        .screenshot-placeholder {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border: 2px dashed #fb8c00;
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
            color: #ff9800;
            margin-bottom: 15px;
        }
        .screenshot-placeholder p {
            color: #e65100;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .screenshot-placeholder small {
            color: #ff9800;
        }
        .info-box {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-box.note {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
        }
        .info-box.warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }
        .info-box.tip {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        .info-box.danger {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        .info-box-title {
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        /* Table of Contents */
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #ff9800;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #fff3e0;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list li {
            margin-bottom: 12px;
        }

        .toc-list a {
            color: #e65100;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #fff3e0;
            color: #ff9800;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .field-table th {
            background: #ff9800;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        .field-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .field-table tr:hover {
            background: #fff3e0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 3px;
        }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-trial { background: #e3f2fd; color: #1565c0; }
        .status-active { background: #e8f5e9; color: #2e7d32; }
        .status-past-due { background: #fff3e0; color: #f57f17; }
        .status-suspended { background: #ffebee; color: #c62828; }
        .status-cancelled { background: #e0e0e0; color: #616161; }
        .status-expired { background: #212121; color: #fff; }
        .payment-method-card {
            background: #fff;
            border: 2px solid #fff3e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            border-color: #fb8c00;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(251, 140, 0, 0.2);
        }
        .payment-method-card i {
            font-size: 2.5rem;
            color: #ff9800;
            margin-bottom: 15px;
        }
        .payment-method-card h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #ff9800;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.4);
            transition: all 0.3s ease;
        }
        .back-to-top:hover {
            background: #e65100;
            color: white;
            transform: translateY(-3px);
        }
        @media (max-width: 992px) {
            .help-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .help-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    @include('help.sidebar', ['active' => 'subscriptions'])

    <main class="help-content">
         <!-- Breadcrumb -->
        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.subscriptions') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Guias de Suscripciones</a></li>
                <li class="breadcrumb-item active">Suscripción</li>
            </ol>
        </nav>

        <header class="help-header">
            <h1><i class="fas fa-crown me-3"></i>Guía de Suscripciones</h1>
            <p>Gestiona tu suscripción, facturas y pagos mensuales en SAMI.</p>           
        </header>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                        <li><a href="#plan"><i class="fas fa-eye"></i> 2. Ver Plan Actual</a></li>
                        <li><a href="#estados"><i class="fas fa-exchange-alt"></i> 3. Estados</a></li>
                        <li><a href="#facturas"><i class="fas fa-file-invoice"></i> 4. Facturas</a></li>
                        <li><a href="#pagos"><i class="fas fa-money-check-alt"></i> 5. Registrar Pagos</a></li>
                        <li><a href="#tips"><i class="fas fa-lightbulb"></i> 6. Tips</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">

        <section id="introduccion" class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Módulo de Suscripciones</h2>

            <p>El módulo de <strong>Suscripciones</strong> te permite gestionar tu plan mensual de SAMI, ver facturas automáticas y registrar pagos.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Características Principales
                        </div>
                        <ul class="mb-0">
                            <li>Visualización del plan actual</li>
                            <li>Facturas automáticas mensuales</li>
                            <li>Registro de pagos Yappy/ACH</li>
                            <li>Historial de pagos</li>
                            <li>Gestión de médicos adicionales</li>
                            <li>Período de gracia de 7 días</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box note">
                        <div class="info-box-title">
                            <i class="fas fa-lightbulb text-primary"></i>
                            Acceso al Módulo
                        </div>
                        <p class="mb-0">Navegue a <strong>Mi Cuenta → Suscripción</strong> desde el menú principal para acceder al módulo de suscripciones donde podrá ver su plan actual, facturas pendientes y registrar pagos.</p>
                    </div>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/subscriptions/subscription.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <section id="plan" class="content-section">
            <h2><i class="fas fa-eye me-2"></i>2. Ver Plan Actual</h2>

            <p>En el módulo de suscripciones puede ver toda la información de su plan activo.</p>

            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Información del Plan</span></h4>
                <p>El sistema muestra:</p>
                <ul>
                    <li><strong>Nombre del Plan:</strong> Básico, Profesional, Empresarial, etc.</li>
                    <li><strong>Precio Mensual:</strong> Monto base + médicos adicionales</li>
                    <li><strong>Estado:</strong> Activa, Trial, Vencida, etc.</li>
                    <li><strong>Fecha de Renovación:</strong> Próxima fecha de facturación</li>
                    <li><strong>Médicos Incluidos:</strong> Cantidad base del plan</li>
                    <li><strong>Médicos Adicionales:</strong> Si ha agregado médicos extra</li>
                    <li><strong>Características:</strong> Límites y funcionalidades incluidas</li>
                </ul>

                <div>
                    <img src="{{ asset('images/tutorial/subscriptions/plan-detail.png') }}" alt="" style="width: 100%;">
                </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Días Hasta Renovación</span></h4>
                <p>El sistema calcula automáticamente los días restantes hasta la próxima renovación. Esto le ayuda a planificar sus pagos.</p>
            </div>
        </section>

        <section id="estados" class="content-section">
            <h2><i class="fas fa-exchange-alt me-2"></i>3. Estados de Suscripción</h2>

            <p>Su suscripción puede tener diferentes estados según el ciclo de pago y actividad:</p>

            <table class="field-table">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Descripción</th>
                        <th>Badge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Pendiente de Activación</strong></td>
                        <td>Suscripción creada, esperando primer pago</td>
                        <td><span class="status-badge status-pending">Pendiente</span></td>
                    </tr>
                    <tr>
                        <td><strong>Período de Prueba</strong></td>
                        <td>En trial gratuito, funcionalidad completa</td>
                        <td><span class="status-badge status-trial">Trial</span></td>
                    </tr>
                    <tr>
                        <td><strong>Activa</strong></td>
                        <td>Suscripción activa y al día</td>
                        <td><span class="status-badge status-active">Activa</span></td>
                    </tr>
                    <tr>
                        <td><strong>Pago Vencido</strong></td>
                        <td>Factura vencida, en período de gracia (7 días)</td>
                        <td><span class="status-badge status-past-due">Vencida</span></td>
                    </tr>
                    <tr>
                        <td><strong>Suspendida</strong></td>
                        <td>Acceso limitado por falta de pago</td>
                        <td><span class="status-badge status-suspended">Suspendida</span></td>
                    </tr>
                    <tr>
                        <td><strong>Cancelada</strong></td>
                        <td>Suscripción cancelada por el usuario</td>
                        <td><span class="status-badge status-cancelled">Cancelada</span></td>
                    </tr>
                    <tr>
                        <td><strong>Expirada</strong></td>
                        <td>Suscripción expirada por falta de pago prolongada</td>
                        <td><span class="status-badge status-expired">Expirada</span></td>
                    </tr>
                </tbody>
            </table>

            <h3>Flujo de Estados</h3>
            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-route text-primary"></i>
                    Ciclo de Vida de la Suscripción
                </div>
                <ol class="mb-0">
                    <li><strong>Inicio:</strong> PENDIENTE → (pago) → ACTIVA</li>
                    <li><strong>Con Trial:</strong> TRIAL → (fin trial) → ACTIVA</li>
                    <li><strong>Renovación:</strong> ACTIVA → (factura mensual) → ACTIVA</li>
                    <li><strong>Falta de Pago:</strong> ACTIVA → VENCIDA (7 días gracia) → SUSPENDIDA (30 días) → EXPIRADA</li>
                    <li><strong>Reactivación:</strong> SUSPENDIDA → (pago) → ACTIVA</li>
                </ol>
            </div>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Período de Gracia
                </div>
                <p class="mb-0">Cuando una factura vence, tiene <strong>7 días de gracia</strong> para realizar el pago sin perder acceso al sistema. Después de 7 días, la suscripción pasa a SUSPENDIDA y el acceso se limita.</p>
            </div>
        </section>

        <section id="facturas" class="content-section">
            <h2><i class="fas fa-file-invoice me-2"></i>4. Facturas de Suscripción</h2>

            <p>Las facturas de suscripción se generan automáticamente cada mes.</p>

            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Generación Automática</span></h4>
                <p>El sistema genera facturas automáticamente:</p>
                <ul>
                    <li>Se crean en la fecha de renovación mensual</li>
                    <li>Incluyen precio base + médicos adicionales</li>
                    <li>Se aplican descuentos automáticamente</li>
                    <li>Estado inicial: Pendiente</li>
                </ul>
            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Ver Facturas</span></h4>
                <p>Para ver sus facturas de suscripción:</p>
                <ul>
                    <li>Navegue a <strong>Mi Cuenta → Suscripción → Facturas</strong></li>
                    <li>Verá el listado de todas las facturas mensuales</li>
                    <li>Puede filtrar por estado (Pendiente, Pagada, Vencida)</li>
                    <li>Descargue el PDF de cada factura</li>
                </ul>

                <div>
                    <img src="{{ asset('images/tutorial/subscriptions/invoices.png') }}" alt="" style="width: 100%;">
                </div>
            </div>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Diferencia con Facturas de Consultas
                </div>
                <p class="mb-0">Las facturas de suscripción son diferentes a las facturas de consultas médicas. Las de suscripción son mensuales y automáticas, mientras que las de consultas se generan al finalizar cada consulta con servicios facturables.</p>
            </div>
        </section>

        <section id="pagos" class="content-section">
            <h2><i class="fas fa-money-check-alt me-2"></i>5. Registrar Pagos de Suscripción</h2>

            <p>Debe registrar manualmente sus pagos de suscripción en el sistema.</p>

            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Realizar el Pago</span></h4>
                <p>Primero, realice el pago mediante uno de los métodos disponibles:</p>
                
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <div class="payment-method-card">
                            <i class="fab fa-cc-paypal"></i>
                            <h5>Yappy</h5>
                            <p>Pago móvil instantáneo. Transfiera al número indicado y guarde el comprobante.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="payment-method-card">
                            <i class="fas fa-university"></i>
                            <h5>ACH</h5>
                            <p>Transferencia bancaria. Use la cuenta bancaria proporcionada y guarde el número de referencia.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Registrar en el Sistema</span></h4>
                <p>Después de realizar el pago:</p>
                <ul>
                    <li>Vaya a <strong>Mi Cuenta → Suscripción → Pagos</strong></li>
                    <li>Haga clic en <strong>"Registrar Pago"</strong></li>
                    <li>Seleccione la factura a pagar</li>
                    <li>Complete el formulario con:
                        <ul>
                            <li>Método de pago (Yappy o ACH)</li>
                            <li>Monto pagado</li>
                            <li>Número de referencia/transacción</li>
                            <li>Fecha del pago</li>
                            <li>Comprobante (imagen/PDF)</li>
                        </ul>
                    </li>
                    <li>Haga clic en <strong>"Enviar"</strong></li>
                </ul>

                <div>
                    <img src="{{ asset('images/tutorial/subscriptions/payment-form.png') }}" alt="">
                </div>
            </div>

            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Verificación</span></h4>
                <p>Una vez registrado el pago:</p>
                <ul>
                    <li>El estado cambia a "Pendiente de Verificación"</li>
                    <li>El equipo de SAMI verificará el pago (1-2 días hábiles)</li>
                    <li>Recibirá notificación cuando sea aprobado</li>
                    <li>La factura se marcará como "Pagada"</li>
                    <li>Su suscripción se renovará automáticamente</li>
                </ul>
            </div>

            <div class="info-box warning">
                <div class="info-box-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Importante
                </div>
                <p class="mb-0">Asegúrese de adjuntar el comprobante de pago correcto y proporcionar el número de referencia exacto. Esto acelera el proceso de verificación y evita retrasos en la renovación de su suscripción.</p>
            </div>
        </section>

        <section id="tips" class="content-section">
            <h2><i class="fas fa-lightbulb me-2"></i>6. Tips y Mejores Prácticas</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Buenas Prácticas
                        </div>
                        <ul class="mb-0">
                            <li>Pague antes de la fecha de vencimiento</li>
                            <li>Guarde todos los comprobantes de pago</li>
                            <li>Registre el pago inmediatamente después de realizarlo</li>
                            <li>Verifique que el monto sea correcto</li>
                            <li>Mantenga actualizada su información de contacto</li>
                            <li>Revise su plan regularmente</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box warning">
                        <div class="info-box-title">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Errores Comunes a Evitar
                        </div>
                        <ul class="mb-0">
                            <li>No registrar el pago en el sistema</li>
                            <li>Proporcionar número de referencia incorrecto</li>
                            <li>No adjuntar comprobante de pago</li>
                            <li>Esperar hasta el último día para pagar</li>
                            <li>No verificar el estado del pago</li>
                            <li>Olvidar renovar después de suspensión</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">Preguntas Frecuentes</h3>

            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Qué pasa si no pago a tiempo?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Tiene 7 días de gracia después del vencimiento. Durante este período, su suscripción sigue activa. Después de 7 días, se suspende el acceso y después de 30 días de suspensión, la suscripción expira.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Cuánto tarda en verificarse mi pago?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Los pagos se verifican en 1-2 días hábiles. Pagos por Yappy suelen verificarse más rápido (mismo día) que ACH (1-2 días). Asegúrese de proporcionar toda la información correcta para acelerar el proceso.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Puedo cambiar de plan?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí, puede cambiar de plan en cualquier momento. Contacte al equipo de soporte para solicitar el cambio. Si cambia a un plan superior, se aplicará un prorrateo por los días restantes del mes.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Cómo agrego médicos adicionales?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Contacte al equipo de soporte para agregar médicos adicionales. El costo adicional se reflejará en su próxima factura mensual. Cada médico adicional tiene un costo mensual que se suma al precio base de su plan.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            ¿Qué hago si mi suscripción fue suspendida?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si su suscripción fue suspendida, debe pagar todas las facturas pendientes para reactivarla. Una vez registrado y verificado el pago, su suscripción se reactivará automáticamente en 24 horas.
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-box tip mt-4">
                <div class="info-box-title">
                    <i class="fas fa-question-circle text-success"></i>
                    ¿Necesita Más Ayuda?
                </div>
                <p class="mb-0">Para información sobre facturación y pagos de consultas, consulte las guías de <a href="{{ route('help.billing') }}">Facturación</a> y <a href="{{ route('help.payments') }}">Pagos</a>. Si tiene preguntas sobre su suscripción, contacte al equipo de soporte de SAMI.</p>
            </div>
        </section>

            </div> <!-- End col-lg-8 -->
        </div> <!-- End row -->

        <div class="row mt-5">
            <div class="col-md-6">
                <a href="{{ route('help.payments') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Anterior: Pagos
                </a>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('help.index') }}" class="btn btn-warning btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    </main>

    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });
        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
