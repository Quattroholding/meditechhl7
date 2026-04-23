<?php

return [
    // General
    'title' => 'Facturas',
    'invoice' => 'Factura',
    'invoices' => 'Facturas',
    'invoice_id' => 'ID de Factura',
    'invoice_number' => 'Número de Factura',
    'invoice_date' => 'Fecha de Factura',
    'due_date' => 'Fecha de Vencimiento',
    'issue_date' => 'Fecha de Emisión',
    'created_at' => 'Creada el',
    'updated_at' => 'Actualizada el',
    'code' => 'code',
    // Search and Filters
    'search' => 'Buscar',
    'search_placeholder' => 'Buscar por ID, número o paciente...',
    'filter_by_status' => 'Filtrar por estado',
    'all_statuses' => 'Todos los estados',
    'items_per_page' => 'elementos por página',
    'showing' => 'Mostrando',
    'to' => 'a',
    'of' => 'de',
    'results' => 'resultados',
    'clear_search' => 'Limpiar Búsqueda',
    'clear_filter' => 'Limpiar Filtro',
    'clear_filters' => 'Limpiar Filtros',

    // Table Headers
    'type' => 'Tipo',
    'bill_to' => 'Facturar a',
    'bill_by' => 'Facturado Por',
    'patient' => 'Paciente',
    'amount' => 'Monto',
    'total_amount' => 'Monto Total',
    'subtotal' => 'Subtotal',
    'tax' => 'Impuesto',
    'statuss' => 'Estado',
    'payment_statuss' => 'Estado de Pago',
    'actions' => 'Acciones',
    'practitioner' => 'Médico',
    'client' => 'Cliente',

    // Status Values
    'status' => [
        'draft' => 'Borrador',
        'issued' => 'Emitida',
        'sent' => 'Enviada',
        'paid' => 'Pagada',
        'cancelled' => 'Cancelada',
        'partially_paid' => 'Pago Parcial',
        'overdue' => 'Vencida',
    ],

    // Payment Status
    'payment_status' => [
        'unpaid' => 'No Pagada',
        'paid' => 'Pagada',
        'partial' => 'Pago Parcial',
        'overdue' => 'Vencida',
        'pending' => 'Pendiente',
    ],

    // Payment Status
    'payment_status_class' => [
        'unpaid' => 'status-pink',
        'paid' => 'status-green',
        'partial' => 'status-orange',
        'overdue' => 'status-default',
        'pending' => 'Pendiente',
    ],

    // Types
    'types' => [
        'invoice' => 'Factura',
        'credit_note' => 'Nota de Crédito',
        'debit_note' => 'Nota de Débito',
        'estimate' => 'Presupuesto',
        'receipt' => 'Recibo',
    ],

    // Actions
    'view_details' => 'Ver Detalles',
    'download_pdf' => 'Descargar PDF',
    'mark_as_sent' => 'Marcar como Enviada',
    'mark_as_paid' => 'Marcar como Pagada',
    'send_invoice' => 'Enviar Factura',
    'clone_invoice' => 'Clonar Factura',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'cancel' => 'Cancelar',
    'print' => 'Imprimir',
    'export' => 'Exportar',

    // Messages
    'no_invoices_found' => 'No se encontraron facturas',
    'no_invoices_message' => 'No hay facturas registradas en el sistema.',
    'no_invoices_filtered' => 'No hay facturas que coincidan con los filtros aplicados.',
    'invoice_marked_paid' => 'Factura marcada como pagada exitosamente.',
    'invoice_marked_sent' => 'Factura marcada como enviada exitosamente.',
    'invoice_deleted' => 'Factura eliminada exitosamente.',
    'loading' => 'Cargando...',
    'loading_data' => 'Cargando datos...',

    // Confirmations
    'confirm_mark_paid' => '¿Está seguro de marcar esta factura como pagada?',
    'confirm_mark_sent' => '¿Está seguro de marcar esta factura como enviada?',
    'confirm_delete' => '¿Está seguro de eliminar esta factura? Esta acción no se puede deshacer.',
    'confirm_cancel' => '¿Está seguro de cancelar esta factura?',

    // Details
    'invoice_details' => 'Detalles de la Factura',
    'line_items' => 'Elementos de Línea',
    'service_description' => 'Descripción del Servicio',
    'quantity' => 'Cantidad',
    'unit_price' => 'Precio Unitario',
    'line_total' => 'Total de Línea',
    'subtotal_amount' => 'Subtotal',
    'tax_amount' => 'Impuestos',
    'total_net' => 'Total Neto',
    'amount_paid' => 'Monto Pagado',
    'amount_due' => 'Monto Pendiente',
    'balance' => 'Saldo',
    'identifier' => 'Identificador',
    'cpt_code' => 'Código CPT',
    'no_line_items' => 'No hay elementos de línea registrados.',
    'notes' => 'Notas',
    'default_terms' => 'Términos y condiciones estándar aplicables.',
    'organization_info' => 'Información de la organización',
    'patient_id' => 'ID Paciente',
    'patient_not_available' => 'Paciente no disponible',
    'phone' => 'Teléfono',
    'email' => 'Email',
    'tax_id' => 'ID Fiscal',

    // Payment
    'payment_information' => 'Información de Pago',
    'payment_date' => 'Fecha de Pago',
    'payment_method' => 'Método de Pago',
    'payment_reference' => 'Referencia de Pago',
    'payment_terms' => 'Términos de Pago',

    // Billing Information
    'billing_information' => 'Información de Facturación',
    'billing_address' => 'Dirección de Facturación',
    'issuer_organization' => 'Organización Emisora',
    'recipient' => 'Destinatario',
    'encounter' => 'Consulta',
    'encounter_id' => 'ID de Consulta',

    // Navigation
    'create_invoice' => 'Crear Factura',
    'new_invoice' => 'Nueva Factura',
    'invoice_list' => 'Lista de Facturas',
    'back_to_list' => 'Volver a la Lista',

    // Currencies
    'currency' => 'Moneda',
    'usd' => 'USD',
    'eur' => 'EUR',
    'pab' => 'PAB',

    // Periods
    'days' => 'días',
    'overdue_by' => 'Vencida por',
    'due_in' => 'Vence en',
    'due_today' => 'Vence hoy',

    // File Operations
    'download' => 'Descargar',
    'preview' => 'Vista Previa',
    'email' => 'Enviar por Email',
    'share' => 'Compartir',

    // Statistics
    'total_invoices' => 'Total de Facturas',
    'paid_invoices' => 'Facturas Pagadas',
    'pending_invoices' => 'Facturas Pendientes',
    'overdue_invoices' => 'Facturas Vencidas',
    'revenue' => 'Ingresos',
    'outstanding_amount' => 'Monto Pendiente',

    // Validation Messages
    'validation' => [
        'required' => 'Este campo es obligatorio.',
        'numeric' => 'Este campo debe ser numérico.',
        'date' => 'Este campo debe ser una fecha válida.',
        'min' => 'Este campo debe tener al menos :min caracteres.',
        'max' => 'Este campo no debe tener más de :max caracteres.',
        'email' => 'Este campo debe ser una dirección de email válida.',
    ],

    // Error Messages
    'errors' => [
        'not_found' => 'Factura no encontrada.',
        'already_paid' => 'Esta factura ya está pagada.',
        'cannot_delete_paid' => 'No se puede eliminar una factura pagada.',
        'insufficient_amount' => 'El monto es insuficiente.',
        'invalid_status' => 'Estado de factura inválido.',
        'permission_denied' => 'No tiene permisos para realizar esta acción.',
    ],
];
