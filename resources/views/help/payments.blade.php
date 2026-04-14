<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Pagos - Centro de Ayuda SAMI</title>
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
            --payment-color: #1565c0;
            --light-bg: #f8f9fa;
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

        /* Main Content */
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
        /* Header */
        .help-header {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
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

        /* Content Sections */
        .content-section {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-section h2 {
            color: #1565c0;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }

        .content-section h3 {
            color: #333;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .content-section h4 {
            color: #555;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Steps */
        .step-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #fff 100%);
            border-left: 4px solid #1565c0;
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
            background: #1565c0;
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

        /* Screenshot Placeholder */
        .screenshot-placeholder {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px dashed #1976d2;
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
            color: #1565c0;
            margin-bottom: 15px;
        }

        .screenshot-placeholder p {
            color: #0d47a1;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .screenshot-placeholder small {
            color: #1565c0;
        }

        /* Info Boxes */
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
        /* Table of Contents */
        .toc-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .toc-card h5 {
            color: #1565c0;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e3f2fd;
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
            color: #0d47a1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toc-list a:hover {
            background: #e3f2fd;
            color: #1565c0;
        }

        .toc-list a i {
            width: 20px;
            text-align: center;
        }

        /* Field Tables */
        .field-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
        }
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .field-table th {
            background: #1565c0;
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
            background: #e3f2fd;
        }

        /* Payment Method Cards */
        .payment-method-card {
            background: #fff;
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .payment-method-card:hover {
            border-color: #1976d2;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.2);
        }

        .payment-method-card i {
            font-size: 2.5rem;
            color: #1565c0;
            margin-bottom: 15px;
        }

        .payment-method-card h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .payment-method-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #1565c0;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.4);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background: #0d47a1;
            color: white;
            transform: translateY(-3px);
        }

        /* Responsive */
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

         @media (max-width: 480px) { 
            .step-card, .info-box{
                border-left: none;
                padding: 25px;
                border-bottom: 5px solid #1565c0;
                border-radius: 8px;
            }

            .info-box.note {
                border-left: none;
                border-bottom-color: #2196f3; 
            }
            .info-box.warning {
                border-left: none;
                border-bottom-color: #ff9800; 
            }
            .info-box.tip {
                border-left: none;
                border-bottom-color: #4caf50; 
            }
            .info-box.danger {
                border-left: none;
                border-bottom-color: #f44336; 
            }
            
            .field-table {
                border: 0;
                box-shadow: none;
            }
            
            .field-table thead {
                display: none;
            }
            
            .field-table tbody {
                display: block;
            }
            
            .field-table tr {
                display: block;
                margin-bottom: 15px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                background: #fff;
            }
            
            .field-table td {
                display: block;
                text-align: center;
                padding: 10px 12px;
                font-size: 14px;
                border-bottom: 1px solid #f0f0f0;
            }
            .field-table td:last-child {
                border-bottom: none;
            }
            
            /* Campo (título de cada tarjeta) */
            .field-table td[data-label="Campo"] {
                background: #1565c0;
                color: white;
                font-size: 15px;
                padding: 12px;
                border-bottom: 2px solid #145cae;
            }
            

            .field-table td[data-label="Campo"] strong {
                color: white;
            }
            
            /* Descripción */
            .field-table td[data-label="Descripcion"] {
                padding: 15px;
                color: #555;
                font-size: 13px;
                line-height: 1.5;
            }
            
            .field-table td[data-label="Descripcion"]:before {
                content: "📝 ";
                margin-right: 5px;
            }
            
            /* Requerido */
           .field-table td[data-label="Requerido"], .field-table td[data-label="Opcional"]  {
                background: #f5f5f5;
                padding: 12px 15px;
                text-align: center;
                font-weight: 600;
            }
            ..field-table td[data-label="Requerido"]{
                font-weight: 600;
                color: #1565c0
            }
            
            /*.field-table td[data-label="Requerido"]:before {
                content: "Valor por defecto: ";
                font-weight: 600;
                color: #1565c0;
                margin-right: 5px;
            }*/

        }

        @media (max-width: 365px){
            .info-box-title, .content-section h4, .help-header h1{
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .step-number {
                margin-right: 0;
            }
            .content-section, .help-content {
                padding:25px;
            }
            .info-box{
                padding: 20px;
            }
            .step-card{
                padding: 15px;
            }
            .section-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            p, .section-card h5, .toc-card h5, .content-section h2{
                text-align: center;
            }
        }
    </style>
</head>
<body>
   @include('help.sidebar', ['active' => 'payments'])
    <!-- Main Content -->
    <main class="help-content">

        <nav class="help-breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('help.index') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('help.payments') }}">Guias de Procesamiento de pagos</a></li>
                <li class="breadcrumb-item active">Registrar Pago</li>
            </ol>
        </nav>
        
        <!-- Header -->
        <header class="help-header">
            <h1><i class="fas fa-credit-card me-3"></i>Guía de Procesamiento de Pagos</h1>
            <p>Aprende cómo registrar y gestionar pagos de facturas en SAMI.</p>          
        </header>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-lg-4">
                <div class="toc-card sticky-top" style="top: 20px;">
                    <h5><i class="fas fa-list me-2"></i>Contenido de esta Guía</h5>
                    <ul class="toc-list">
                        <li><a href="#introduccion"><i class="fas fa-info-circle"></i> 1. Introducción</a></li>
                        <li><a href="#registrar"><i class="fas fa-plus-circle"></i> 2. Registrar Pago</a></li>
                        <!--<li><a href="#metodos"><i class="fas fa-money-bill-wave"></i> 3. Métodos de Pago</a></li>-->
                        <li><a href="#gestion"><i class="fas fa-tasks"></i> 3. Gestión</a></li>
                        <!--<li><a href="#reportes"><i class="fas fa-chart-line"></i> 5. Reportes</a></li> -->
                        <li><a href="#tips"><i class="fas fa-lightbulb"></i> 4. Tips</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">

        <!-- Section 1: Introduction -->
        <section id="introduccion" class="content-section">
            <h2><i class="fas fa-info-circle me-2"></i>1. Introducción al Módulo de Pagos</h2>

            <p>El módulo de <strong>Pagos</strong> permite registrar y gestionar los pagos que los pacientes realizan contra sus facturas. El sistema mantiene un registro detallado de todos los pagos y actualiza automáticamente el estado de las facturas.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Características Principales
                        </div>
                        <ul class="mb-0">
                            <li>Registro de pagos contra facturas</li>
                            <li>Múltiples métodos de pago</li>
                            <!--<li>Pagos parciales y completos</li>-->
                            <li>Generación de comprobantes</li>
                            <li>Seguimiento de cuentas por cobrar</li>
                            <!--<li>Reportes de pagos recibidos</li>-->
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box note">
                        <div class="info-box-title">
                            <i class="fas fa-lightbulb text-primary"></i>
                            Flujo de Pagos
                        </div>
                        <ol class="mb-0">
                            <li>Se genera una <a href="{{ route('help.billing') }}">factura</a> al finalizar consulta</li>
                            <li>El paciente realiza el pago</li>
                            <li>Se registra el pago en el sistema</li>
                            <li>El sistema actualiza el estado de la factura</li>
                            <li>Se genera comprobante de pago</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div>
                <img src="{{ asset('images/tutorial/payments/payment_list.png') }}" alt="" style="width: 100%;">
            </div>
        </section>

        <!-- Section 2: Register Payment -->
        <section id="registrar" class="content-section">
            <h2><i class="fas fa-plus-circle me-2"></i>2. Registrar un Pago</h2>

            <p>Para registrar un pago que un paciente ha realizado contra una factura, siga estos pasos:</p>

            <!-- Step 1 -->
            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Acceder al Módulo de Pagos</span></h4>
                <p>Navegue a <strong>Cuentas → Facturas</strong> desde el menú principal.</p>
                <ul>
                    <li>Desde Lista Facturas, seleccione la línea de la factura que vaya a registrar el pago y en la columna de acciones, haga clic en el ícono de la tarjeta para<strong>"Registrar Pago"</strong></li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/payments/payment_paybtn.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 2 -->
            <!--<div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Seleccionar Factura</span></h4>
                <p>Si accedió desde el módulo de Pagos:</p>
                <ul>
                    <li>Busque la factura por número, paciente o fecha</li>
                    <li>Seleccione la factura pendiente de pago</li>
                    <li>El sistema mostrará:
                        <ul>
                            <li>Monto total de la factura</li>
                            <li>Monto ya pagado (si hay pagos previos)</li>
                            <li>Monto pendiente</li>
                        </ul>
                    </li>
                </ul>
                </div>
                <div class="info-box note">
                    <div class="info-box-title">
                        <i class="fas fa-info-circle text-primary"></i>
                        Desde el Detalle de Factura
                    </div>
                    <p class="mb-0">Si accedió desde el detalle de una factura, esta ya estará preseleccionada y verá directamente el formulario de pago.</p>
                </div>
            -->

            <!-- Step 3 -->
            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Completar Información del Pago</span></h4>
                <p>Complete el formulario de registro de pago:</p>

                <div class="field-table-wrapper">
                    <table class="field-table">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Descripción</th>
                                <th>Requerido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Campo"><strong>Monto</strong></td>
                                <td data-label="Descripcion">Cantidad pagada por el paciente</td>
                                <td data-label="Requerido">Sí</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Método de Pago</strong></td>
                                <td data-label="Descripcion">Forma en que se recibió el pago (efectivo, tarjeta, etc.)</td>
                                <td data-label="Requerido">Sí</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Fecha de Pago</strong></td>
                                <td data-label="Descripcion">Fecha en que se recibió el pago</td>
                                <td data-label="Requerido">Sí</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Número de Referencia</strong></td>
                                <td data-label="Descripcion">Número de transacción, cheque, autorización, etc.</td>
                                <td data-label="Requerido">Opcional</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>ID de Transacción</strong></td>
                                <td data-label="Descripcion">Número Identificador de la trasnsacción bancaria</td>
                                <td data-label="Requerido">Opcional</td>
                            </tr>
                            <tr>
                                <td data-label="Campo"><strong>Notas</strong></td>
                                <td data-label="Descripcion">Información adicional sobre el pago</td>
                                <td data-label="Requerido">Opcional</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!--<div class="info-box warning">
                    <div class="info-box-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Pagos Parciales
                    </div>
                    <p class="mb-0">Puede registrar un pago por un monto menor al total de la factura. El sistema marcará la factura como "Pago Parcial" y permitirá registrar pagos adicionales hasta completar el monto total.</p>
                </div>-->

            <div>
                <img src="{{ asset('images/tutorial/payments/payment_form.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Step 4 -->
            <div class="step-card">
                <h4><span class="step-number">3</span><span class="step-title">Confirmar el Pago</span></h4>
                <p>Antes de confirmar, revise:</p>
                <ul>
                    <li>El monto sea correcto</li>
                    <li>El método de pago esté bien seleccionado</li>
                    <li>La fecha sea la correcta</li>
                    <li>El número de referencia esté completo (si aplica)</li>
                </ul>
                <p>Haga clic en <strong>"Registrar"</strong>para confirmar.</p>

                <div class="info-box danger">
                    <div class="info-box-title">
                        <i class="fas fa-ban text-danger"></i>
                        Importante
                    </div>
                    <p class="mb-0">Una vez registrado el pago, no podrá modificarlo o eliminarlo directamente. Si cometió un error, contacte al administrador del sistema para hacer las correcciones necesarias.</p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="step-card">
                <h4><span class="step-number">4</span><span class="step-title">Actualización Automática</span></h4>
                <p>Al registrar el pago, el sistema automáticamente:</p>
                <ol>
                    <li>Actualiza el monto pagado de la factura</li>
                    <!--info-color<li>Calcula el monto pendiente</li>
                    <li>Actualiza el estado de pago de la factura:
                        <ul>
                            <<li><strong>Partially Paid:</strong> Si aún queda saldo pendiente</li>
                            <li><strong>Pagada:</strong> Si el monto pagado cubre el total</li>
                        </ul>-->
                    </li>
                    <!--<li>Genera un comprobante de pago</li>-->
                    <li>Registra la transacción en el historial</li>
                    <li>La factura cambia de estado a <strong>"Pagada"</strong></li>
                </ol>
            </div>

            <!-- Step 6 -->
            <div class="step-card">
                <h4><span class="step-number">5</span><span class="step-title">Descargar Comprobante</span></h4>
                <p>Después de registrar el pago:</p>
                <ul>
                    <!--<li>El sistema muestra un mensaje de confirmación</li>-->
                    <li>Puede descargar la factura con el estado actualizado a <strong>"Pagada"</strong> en PDF al buscar la factura darle clic al <strong>ícono de descarga</strong></li>
                    <div>
                        <img src="{{ asset('images/tutorial/payments/payment_icond.png') }}" alt="" style="width: 100%;">
                    </div>
                    <li>Se abrirá una nueva pestaña con el visualizador de PDF, donde podrá hacer efectiva la descarga del mismo.</li>
                    <div>
                        <img src="{{ asset('images/tutorial/payments/payment_pdfv2.png') }}" alt="" style="width: 100%;">
                    </div>
                    <li>Entregue el comprobante al paciente</li>
                </ul>
            </div>
        </section>

        <!-- Section 3: Payment Methods -->
        {{--}}<section id="metodos" class="content-section">
            <h2><i class="fas fa-money-bill-wave me-2"></i>3. Métodos de Pago</h2>

            <p>SAMI soporta múltiples métodos de pago para adaptarse a las necesidades de su práctica:</p>

            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fas fa-money-bill-wave"></i>
                        <h5>Efectivo</h5>
                        <p>Pago en efectivo recibido directamente del paciente.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fas fa-credit-card"></i>
                        <h5>Tarjeta de Crédito/Débito</h5>
                        <p>Pago con tarjeta mediante terminal POS o pasarela de pago.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fas fa-university"></i>
                        <h5>Transferencia Bancaria</h5>
                        <p>Transferencia electrónica o depósito bancario.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fas fa-money-check"></i>
                        <h5>Cheque</h5>
                        <p>Pago mediante cheque personal o de empresa.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fab fa-cc-paypal"></i>
                        <h5>Pago en Línea</h5>
                        <p>Pago en línea mediante Yappy.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="payment-method-card">
                        <i class="fas fa-shield-alt"></i>
                        <h5>Seguro Médico</h5>
                        <p>Pago realizado directamente por la aseguradora.</p>
                    </div>
                </div>
            </div>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb text-success"></i>
                    Número de Referencia
                </div>
                <p class="mb-0">Para pagos con tarjeta, transferencia o cheque, siempre registre el número de referencia, autorización o número de cheque. Esto facilita la conciliación bancaria y la auditoría de pagos.</p>
            </div>
        </section>{{--}}

        <!-- Section 4: Payment Management -->
        <section id="gestion" class="content-section">
            <h2><i class="fas fa-tasks me-2"></i>3. Gestión de Pagos</h2>

            <p>El módulo de pagos permite ver y gestionar todos los pagos registrados en el sistema.</p>

            <!-- View Payments -->
            <div class="step-card">
                <h4><span class="step-number">1</span><span class="step-title">Ver Listado de Pagos</span></h4>
                <p>Para ver todos los pagos:</p>
                <ul>
                    <li>Navegue a <strong>Cuentas → Pagos</strong></li>
                    <li>Se mostrará una tabla con todos los pagos registrados</li>
                    <li>Puede filtrar por:
                        <ul>
                            <li>Fecha de pago</li>
                            <li>Paciente</li>
                            <li>Método de pago</li>
                            <!--<li>Monto</li>-->
                            <li>Número de factura</li>
                        </ul>
                    </li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/payments/payment_paid.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- View Details -->
            <div class="step-card">
                <h4><span class="step-number">2</span><span class="step-title">Ver Detalle de Pago</span></h4>
                <p>Para ver el detalle completo de un pago:</p>
                <ul>
                    <li>Dentro de <strong>Cuentas → Pagos</strong>, en la columna de acciones, haga clic en el ícono de detalle <br>Se mostrará la factura con todos sus detalles.</li>
                    <!--<li>Se mostrará la factura con todos sus detalles.
                        <ul>
                            <li>Información del pago (monto, fecha, método)</li>
                            <li>Factura asociada</li>
                            <li>Paciente</li>
                            <li>Número de referencia</li>
                            <li>Usuario que registró el pago</li>
                            <li>Notas adicionales</li>
                        </ul>
                    </li>-->
                    <li>Puede descargar la factura</li>
                </ul>

            <div>
                <img src="{{ asset('images/tutorial/payments/payment_detail.png') }}" alt="" style="width: 100%;">
            </div>
            </div>

            <!-- Multiple Payments 
            <h3>Pagos Parciales y Múltiples Pagos</h3>
            <p>Una factura puede tener múltiples pagos asociados:</p>
            <ul>
                <li>El paciente puede pagar en cuotas</li>
                <li>Cada pago se registra por separado</li>
                <li>El sistema suma todos los pagos automáticamente</li>
                <li>La factura muestra el historial completo de pagos</li>
                <li>El monto pendiente se actualiza con cada pago</li>
            </ul>

            <div class="info-box note">
                <div class="info-box-title">
                    <i class="fas fa-calculator text-primary"></i>
                    Ejemplo de Pagos Parciales
                </div>
                <p>Factura total: $500.00</p>
                <ul class="mb-0">
                    <li>Pago 1: $200.00 (efectivo) → Estado: Pago Parcial, Pendiente: $300.00</li>
                    <li>Pago 2: $150.00 (tarjeta) → Estado: Pago Parcial, Pendiente: $150.00</li>
                    <li>Pago 3: $150.00 (transferencia) → Estado: Pagada, Pendiente: $0.00</li>
                </ul>
            </div>-->
        </section>

        <!-- Section 5: Reports 
        <section id="reportes" class="content-section">
            <h2><i class="fas fa-chart-bar me-2"></i>5. Reportes de Pagos</h2>

            <p>El sistema ofrece varios reportes para el seguimiento financiero:</p>

            <h3>Cuentas por Cobrar</h3>
            <ul>
                <li>Facturas pendientes de pago</li>
                <li>Monto total por cobrar</li>
                <li>Antigüedad de saldos</li>
                <li>Facturas vencidas</li>
            </ul>

            <h3>Pagos Recibidos</h3>
            <ul>
                <li>Total de pagos por período</li>
                <li>Desglose por método de pago</li>
                <li>Pagos por paciente</li>
                <li>Pagos por médico</li>
            </ul>

            <h3>Conciliación Bancaria</h3>
            <ul>
                <li>Pagos con tarjeta por conciliar</li>
                <li>Transferencias pendientes de verificar</li>
                <li>Cheques por depositar</li>
            </ul>

            <div class="info-box tip">
                <div class="info-box-title">
                    <i class="fas fa-download text-success"></i>
                    Exportar Reportes
                </div>
                <p class="mb-0">La mayoría de reportes pueden exportarse a Excel o PDF para análisis externo o presentación a contabilidad.</p>
            </div>

            <div class="screenshot-placeholder">
                <i class="fas fa-chart-line"></i>
                <p>Captura de pantalla: Reporte de cuentas por cobrar</p>
                <small>Recomendado: 1000x600px - Mostrar ejemplo de reporte con gráficos y tablas</small>
            </div>
        </section> -->

        <!-- Section 6: Tips and Best Practices -->
        <section id="tips" class="content-section">
            <h2><i class="fas fa-lightbulb me-2"></i>4. Tips y Mejores Prácticas</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box tip">
                        <div class="info-box-title">
                            <i class="fas fa-check-circle text-success"></i>
                            Buenas Prácticas
                        </div>
                        <ul class="mb-0">
                            <li><strong>Registrar inmediatamente:</strong> Registre los pagos tan pronto como se reciban</li>
                            <li><strong>Verificar montos:</strong> Confirme el monto antes de registrar</li>
                            <li><strong>Números de referencia:</strong> Siempre registre números de transacción</li>
                            <li><strong>Comprobantes:</strong> Entregue comprobante al paciente</li>
                            <li><strong>Conciliación diaria:</strong> Revise pagos vs depósitos bancarios</li>
                            <li><strong>Seguimiento:</strong> Revise cuentas por cobrar regularmente</li>
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
                            <li>Registrar pagos sin verificar el monto</li>
                            <li>No guardar números de referencia</li>
                            <li>Olvidar entregar comprobante al paciente</li>
                            <li>No conciliar pagos con depósitos bancarios</li>
                            <li>Registrar el pago en la factura incorrecta</li>
                            <li>No hacer seguimiento a facturas vencidas</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">Preguntas Frecuentes</h3>

            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Puedo modificar un pago después de registrarlo?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            No, por razones de auditoría, los pagos registrados no pueden modificarse directamente. Si cometió un error, contacte al administrador del sistema quien podrá hacer las correcciones necesarias o anular el pago y crear uno nuevo.
                        </div>
                    </div>
                </div>

                <!--<div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Qué hago si el paciente paga más del monto de la factura?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si el paciente paga de más, el sistema registrará el pago completo y el excedente quedará como crédito a favor del paciente. Este crédito puede aplicarse a futuras facturas o reembolsarse al paciente. Contacte al administrador para gestionar el crédito.
                        </div>
                    </div>
                </div>-->

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Cómo aplico un pago a múltiples facturas?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si un paciente paga varias facturas con un solo pago, debe registrar el pago por separado en cada factura. Por ejemplo, si paga $300 para dos facturas de $150 cada una, registre un pago de $150 en cada factura con el mismo número de referencia.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Qué hago con facturas muy antiguas sin pagar?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para facturas muy antiguas (más de 90 días) sin pago, debe hacer seguimiento con el paciente. Si después de varios intentos no se recibe el pago, consulte con el administrador sobre políticas de cuentas incobrables. Algunas clínicas envían a cobranza externa o cancelan la deuda.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            ¿Cómo manejo devoluciones o reembolsos?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para devoluciones o reembolsos, contacte al administrador del sistema. Se debe crear un registro de nota de crédito o reembolso que se asocie a la factura y pago original. Esto mantiene la trazabilidad completa de la transacción.
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-box tip mt-4">
                <div class="info-box-title">
                    <i class="fas fa-question-circle text-success"></i>
                    ¿Necesita Más Ayuda?
                </div>
                <p class="mb-0">Para información sobre cómo generar facturas, consulte la guía de <a href="{{ route('help.billing') }}">Facturación</a>. Si tiene preguntas adicionales, contacte al equipo de soporte de SAMI.</p>
            </div>
        </section>

            </div> <!-- End col-lg-8 -->
        </div> <!-- End row -->

        <!-- Navigation Footer -->
        <div class="row mt-5">
            <div class="col-md-6">
                <a href="{{ route('help.billing') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Anterior: Facturación
                </a>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('help.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    </main>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to top button
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

        // Smooth scroll for anchor links
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
