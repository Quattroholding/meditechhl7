<?php

return [
    // Títulos principales
    'upload_title' => 'Upload Result',
    'results_title' => 'Service Request Results',
    'result_details' => 'Result Details',

    // Campos del formulario
    'result_type' => 'Result Type',
    'status' => 'Status',
    'code' => 'Code',
    'code_display' => 'Code Description',
    'result_date' => 'Result Date',
    'file' => 'File',
    'observations' => 'Observations',
    'notes' => 'Notes',
    'interpretation' => 'Interpretation',
    'reference_range' => 'Reference Range',
    'uploaded_at' => 'Uploaded On',
    'file_name' => 'File Name',
    'file_size' => 'File Size',
    'file_type' => 'File Type',
    'version' => 'Version',
    'effective_date' => 'Effective Date',
    'issued_date' => 'Issued Date',

    // Tipos de resultado
    'laboratory' => 'Laboratory',
    'imaging' => 'Imaging',
    'pathology' => 'Pathology',
    'report' => 'Report',
    'other' => 'Other',

    // Estados
    'status_final' => 'Final',
    'status_preliminary' => 'Preliminary',
    'status_corrected' => 'Corrected',
    'status_amended' => 'Amended',
    'status_cancelled' => 'Cancelled',
    'status_entered_in_error' => 'Entered in Error',

    // Interpretaciones
    'interpretation_normal' => 'Normal',
    'interpretation_abnormal' => 'Abnormal',
    'interpretation_high' => 'High',
    'interpretation_low' => 'Low',
    'interpretation_critical' => 'Critical',

    // Placeholders
    'code_placeholder' => 'E.g.: 85025, 71020, etc.',
    'code_display_placeholder' => 'Result code description',
    'observations_placeholder' => 'Clinical observations about the result...',
    'notes_placeholder' => 'Additional notes or comments...',
    'reference_range_placeholder' => 'E.g.: 4.0-6.0 mg/dL, Normal: <100',

    // Mensajes de ayuda
    'file_help' => 'Supported formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX',
    'max_size' => 'Maximum size',

    // Mensajes de éxito/error
    'uploaded_successfully' => 'Result uploaded successfully',
    'upload_failed' => 'Error uploading result',
    'file_already_exists' => 'This file already exists for this request',
    'not_found' => 'Service request not found',
    'download_failed' => 'Error downloading file',

    // Acciones
    'upload' => 'Upload Result',
    'download' => 'Download',
    'view' => 'View',
    'delete' => 'Delete',
    'replace' => 'Replace',
    'upload_new' => 'Upload New Result',

    // Confirmaciones
    'delete_confirmation' => 'Are you sure you want to delete this result?',
    'replace_confirmation' => 'Are you sure you want to replace this result?',

    // Información adicional
    'no_results' => 'No results available',
    'no_results_description' => 'No results have been uploaded for this service request yet.',
    'results_count' => 'Result|Results',
    'results_count_short' => 'results',
    'uploaded_by' => 'Uploaded By',
    'metadata' => 'Metadata',
    'specimen_info' => 'Specimen Information',
    'replaces' => 'Replaces',
    'replaced_by' => 'Replaced By',
    'upload_first' => 'Upload First Result',
    'file_not_found' => 'File not found',
    'deleted_successfully' => 'Result deleted successfully',

    // Validaciones específicas
    'result_type_required' => 'Result type is required',
    'file_required' => 'You must select a file',
    'file_max_size' => 'File must not exceed 10MB',
    'result_date_required' => 'Result date is required',
    'status_required' => 'Status is required',
];
