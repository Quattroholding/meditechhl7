<?php

return [
    // Títulos y etiquetas principales
    'title' => 'Service Request List',
    'titles' => 'Service Request List',
    'description_modal_title' => 'Full Description',

    // Campos de la tabla
    'id' => 'ID',
    'code' => 'Code',
    'type' => 'Type',
    'description' => 'Description',
    'patient' => 'Patient',
    'practitioner' => 'Practitioner',
    'status' => 'Status',
    'priority' => 'Priority',
    'occurrence_start' => 'Start Date',
    'occurrence_end' => 'End Date',
    'actions' => 'Actions',

    // Estados FHIR
    'status_draft' => 'Draft',
    'status_active' => 'Active',
    'status_on-hold' => 'On Hold',
    'status_on_hold' => 'On Hold',
    'status_revoked' => 'Revoked',
    'status_completed' => 'Completed',
    'status_entered-in-error' => 'Entered in Error',
    'status_unknown' => 'Unknown',

    // Descripciones de estados
    'status_draft_description' => 'Request in draft status, not yet confirmed',
    'status_active_description' => 'Active request in progress',
    'status_on_hold_description' => 'Request temporarily on hold',
    'status_revoked_description' => 'Request cancelled or revoked',
    'status_completed_description' => 'Request successfully completed',
    'status_entered_in_error_description' => 'Request entered in error',
    'status_unknown_description' => 'Request status is unknown',

    // Prioridades
    'priority_urgent' => 'Urgent',
    'priority_asap' => 'As Soon As Possible',
    'priority_routine' => 'Routine',
    'priority_stat' => 'Immediate',

    // Intenciones
    'intent_proposal' => 'Proposal',
    'intent_plan' => 'Plan',
    'intent_directive' => 'Directive',
    'intent_order' => 'Order',
    'intent_original_order' => 'Original Order',
    'intent_reflex_order' => 'Reflex Order',
    'intent_filler_order' => 'Filler Order',
    'intent_instance_order' => 'Instance Order',
    'intent_option' => 'Option',

    // Detalles expandibles
    'service_type' => 'Service Type',
    'intent' => 'Intent',
    'do_not_perform' => 'Do Not Perform',
    'quantity' => 'Quantity',
    'notes' => 'Notes',
    'created' => 'Created',
    'authored_on' => 'Authored On',
    'last_updated' => 'Last Updated',
    'supporting_info' => 'Supporting Information',
    'reason_code' => 'Reason Code',
    'reason_reference' => 'Reason Reference',
    'body_site' => 'Body Site',
    'patient_instruction' => 'Patient Instructions',

    // Acciones
    'view_edit' => 'View/Edit',
    'delete' => 'Delete',
    'delete_confirmation' => 'Are you sure you want to delete this request?',

    // Botones del modal
    'close' => 'Close',

    // Gestión de estados
    'change_status' => 'Change Status',
    'status_change_title' => 'Status Management',
    'status_changed_successfully' => 'Status changed successfully',
    'status_change_failed' => 'Error changing status',
    'invalid_status_transition' => 'Invalid status transition',
    'status_history' => 'Status History',
    'current_status' => 'Current Status',
    'new_status' => 'New Status',
    'status_reason' => 'Reason for change',
    'status_reason_placeholder' => 'Enter reason for status change...',
    'auto_status_change' => 'Automatic status change',
    'manual_status_change' => 'Manual status change',

    // Transiciones automáticas
    'auto_completed_reason' => 'Request automatically completed upon uploading all results',
    'auto_active_reason' => 'Request automatically activated upon creation',
    'result_uploaded' => 'Request completed upon uploading result',

    // Valores por defecto
    'no_data' => '-',
    'yes' => 'Yes',
    'no' => 'No',
    'not_available' => 'N/A',
    'laboratory' => 'Laboratory',
    'image' => 'Image',
    'images' => 'Image',
    'procedure' => 'Procedure',
    'procedures' => 'Procedure',
];
