<?php

return [
    // Títulos principales
    'upload_title' => 'Subir Resultado',
    'results_title' => 'Resultados de Solicitud',
    'result_details' => 'Detalles del Resultado',

    // Campos del formulario
    'result_type' => 'Tipo de Resultado',
    'status' => 'Estado',
    'code' => 'Código',
    'code_display' => 'Descripción del Código',
    'result_date' => 'Fecha del Resultado',
    'file' => 'Archivo',
    'observations' => 'Observaciones',
    'notes' => 'Notas',
    'interpretation' => 'Interpretación',
    'reference_range' => 'Rango de Referencia',
    'uploaded_at' => 'Subido el',
    'file_name' => 'Nombre del Archivo',
    'file_size' => 'Tamaño del Archivo',
    'file_type' => 'Tipo de Archivo',
    'version' => 'Versión',
    'effective_date' => 'Fecha Efectiva',
    'issued_date' => 'Fecha de Emisión',

    // Tipos de resultado
    'laboratory' => 'Laboratorio',
    'imaging' => 'Imagenología',
    'pathology' => 'Patología',
    'report' => 'Informe',
    'other' => 'Otro',

    // Estados
    'status_final' => 'Final',
    'status_preliminary' => 'Preliminar',
    'status_corrected' => 'Corregido',
    'status_amended' => 'Modificado',
    'status_cancelled' => 'Cancelado',
    'status_entered_in_error' => 'Error de Entrada',

    // Interpretaciones
    'interpretation_normal' => 'Normal',
    'interpretation_abnormal' => 'Anormal',
    'interpretation_high' => 'Alto',
    'interpretation_low' => 'Bajo',
    'interpretation_critical' => 'Crítico',

    // Placeholders
    'code_placeholder' => 'Ej: 85025, 71020, etc.',
    'code_display_placeholder' => 'Descripción del código del resultado',
    'observations_placeholder' => 'Observaciones clínicas sobre el resultado...',
    'notes_placeholder' => 'Notas adicionales o comentarios...',
    'reference_range_placeholder' => 'Ej: 4.0-6.0 mg/dL, Normal: <100',

    // Mensajes de ayuda
    'file_help' => 'Formatos soportados: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX',
    'max_size' => 'Tamaño máximo',

    // Mensajes de éxito/error
    'uploaded_successfully' => 'Resultado subido exitosamente',
    'upload_failed' => 'Error al subir el resultado',
    'file_already_exists' => 'Este archivo ya existe para esta solicitud',
    'not_found' => 'Solicitud de servicio no encontrada',
    'download_failed' => 'Error al descargar el archivo',

    // Acciones
    'upload' => 'Subir Resultado',
    'download' => 'Descargar',
    'view' => 'Ver',
    'delete' => 'Eliminar',
    'replace' => 'Reemplazar',
    'upload_new' => 'Subir Nuevo Resultado',

    // Confirmaciones
    'delete_confirmation' => '¿Estás seguro de eliminar este resultado?',
    'replace_confirmation' => '¿Estás seguro de reemplazar este resultado?',

    // Información adicional
    'no_results' => 'No hay resultados disponibles',
    'no_results_description' => 'Aún no se han subido resultados para esta solicitud de servicio.',
    'results_count' => 'Resultado|Resultados',
    'results_count_short' => 'resultados',
    'uploaded_by' => 'Subido por',
    'metadata' => 'Metadatos',
    'specimen_info' => 'Información de la Muestra',
    'replaces' => 'Reemplaza a',
    'replaced_by' => 'Reemplazado por',
    'upload_first' => 'Subir Primer Resultado',
    'file_not_found' => 'Archivo no encontrado',
    'deleted_successfully' => 'Resultado eliminado exitosamente',

    // Validaciones específicas
    'result_type_required' => 'El tipo de resultado es obligatorio',
    'file_required' => 'Debe seleccionar un archivo',
    'file_max_size' => 'El archivo no debe superar los 10MB',
    'result_date_required' => 'La fecha del resultado es obligatoria',
    'status_required' => 'El estado es obligatorio',
];
