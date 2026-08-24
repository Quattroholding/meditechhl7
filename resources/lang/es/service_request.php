<?php

return [
    // Títulos y etiquetas principales
    'title' => 'Lista de Estudios',
    'titles' => 'Lista de Estudios',
    'description_modal_title' => 'Descripción Completa',

    // Campos de la tabla
    'id' => 'ID',
    'code' => 'Código',
    'type' => 'Tipo',
    'description' => 'Descripción',
    'patient' => 'Paciente',
    'practitioner' => 'Profesional',
    'status' => 'Estado',
    'priority' => 'Prioridad',
    'occurrence_start' => 'Fecha Inicio',
    'occurrence_end' => 'Fecha Fin',
    'actions' => 'Acciones',

    // Estados FHIR
    'status_draft' => 'Borrador',
    'status_active' => 'Activo',
    'status_on-hold' => 'En Espera',
    'status_on_hold' => 'En Espera',
    'status_revoked' => 'Revocado',
    'status_completed' => 'Completado',
    'status_entered-in-error' => 'Ingresado por error',
    'status_unknown' => 'Desconocido',

    // Descripciones de estados
    'status_draft_description' => 'Solicitud en estado de borrador, aún no confirmada',
    'status_active_description' => 'Solicitud activa y en proceso',
    'status_on_hold_description' => 'Solicitud pausada temporalmente',
    'status_revoked_description' => 'Solicitud cancelada o revocada',
    'status_completed_description' => 'Solicitud completada exitosamente',
    'status_entered_in_error_description' => 'Solicitud ingresada por error',
    'status_unknown_description' => 'Estado de la solicitud es desconocido',

    // Prioridades
    'priority_urgent' => 'Urgente',
    'priority_asap' => 'Lo Antes Posible',
    'priority_routine' => 'Rutina',
    'priority_stat' => 'Inmediato',

    // Intenciones
    'intent_proposal' => 'Propuesta',
    'intent_plan' => 'Plan',
    'intent_directive' => 'Directiva',
    'intent_order' => 'Orden',
    'intent_original_order' => 'Orden Original',
    'intent_reflex_order' => 'Orden Refleja',
    'intent_filler_order' => 'Orden de Relleno',
    'intent_instance_order' => 'Orden de Instancia',
    'intent_option' => 'Opción',

    // Detalles expandibles
    'service_type' => 'Tipo de Servicio',
    'intent' => 'Intención',
    'do_not_perform' => 'No Realizar',
    'quantity' => 'Cantidad',
    'notes' => 'Notas',
    'created' => 'Creado',
    'authored_on' => 'Autorizado el',
    'last_updated' => 'Última Actualización',
    'supporting_info' => 'Información de Apoyo',
    'reason_code' => 'Código de Razón',
    'reason_reference' => 'Referencia de Razón',
    'body_site' => 'Sitio del Cuerpo',
    'patient_instruction' => 'Instrucciones al Paciente',

    // Acciones
    'view_edit' => 'Ver/Editar',
    'delete' => 'Eliminar',
    'delete_confirmation' => '¿Estás seguro de eliminar esta solicitud?',

    // Botones del modal
    'close' => 'Cerrar',

    // Gestión de estados
    'change_status' => 'Cambiar Estado',
    'status_change_title' => 'Gestión de Estado',
    'status_changed_successfully' => 'Estado cambiado exitosamente',
    'status_change_failed' => 'Error al cambiar el estado',
    'invalid_status_transition' => 'Transición de estado no válida',
    'status_history' => 'Historial de Estados',
    'current_status' => 'Estado Actual',
    'new_status' => 'Nuevo Estado',
    'status_reason' => 'Razón del cambio',
    'status_reason_placeholder' => 'Ingrese la razón del cambio de estado...',
    'auto_status_change' => 'Cambio automático de estado',
    'manual_status_change' => 'Cambio manual de estado',

    // Transiciones automáticas
    'auto_completed_reason' => 'Solicitud completada automáticamente al subir todos los resultados',
    'auto_active_reason' => 'Solicitud activada automáticamente al crear',
    'result_uploaded' => 'Solicitud completada al subir resultado',

    // Valores por defecto
    'no_data' => '-',
    'yes' => 'Sí',
    'no' => 'No',
    'not_available' => 'N/A',
    'laboratory' => 'Laboratorio',
    'image' => 'Imagen',
    'images' => 'Imagen',
    'procedure' => 'Procedimiento',
    'procedures' => 'Procedimiento',
];
