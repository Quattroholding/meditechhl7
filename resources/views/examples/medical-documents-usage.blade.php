{{-- 
EJEMPLO DE USO DE LAS RUTAS DE DOCUMENTOS MÉDICOS

Este archivo muestra cómo usar las rutas para generar recetas médicas y órdenes médicas
desde los encounters con medication_requests y service_requests.

--}}

@extends('layout.master')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Documentos Médicos</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📋 Recetas Médicas</h4>
                    <p class="text-muted">Para encounters con medication_requests</p>
                </div>
                <div class="card-body">
                    <h6>Rutas disponibles:</h6>
                    
                    <!-- Receta desde Encounter -->
                    <div class="mb-3">
                        <strong>1. Generar receta completa del encounter:</strong>
                        <div class="mt-2">
                            <code>GET /prescription/{encounter_id}/download</code>
                        </div>
                        <div class="mt-2">
                            <strong>Ejemplo:</strong>
                            <a href="{{ route('prescription.download', 61) }}" 
                               class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-download"></i> Descargar Receta (Encounter #61)
                            </a>
                            <a href="{{ route('prescription.preview', 61) }}" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-eye"></i> Vista Previa
                            </a>
                        </div>
                    </div>

                    <!-- Receta personalizada -->
                    <div class="mb-3">
                        <strong>2. Receta personalizada (medicamentos específicos):</strong>
                        <div class="mt-2">
                            <code>POST /prescription/custom/download</code>
                        </div>
                        <div class="mt-2">
                            <form action="{{ route('prescription.custom.download') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="medication_ids[]" value="1">
                                <input type="hidden" name="medication_ids[]" value="2">
                                <button type="submit" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i> Receta Personalizada
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Características:</strong>
                        <ul class="mb-0">
                            <li>Incluye diagnósticos del encounter como causal</li>
                            <li>Información completa del paciente y médico</li>
                            <li>Formato PDF profesional</li>
                            <li>Validez de 30 días</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🏥 Órdenes Médicas</h4>
                    <p class="text-muted">Para encounters con service_requests</p>
                </div>
                <div class="card-body">
                    <h6>Rutas disponibles:</h6>
                    
                    <!-- Orden desde Encounter -->
                    <div class="mb-3">
                        <strong>1. Generar orden completa del encounter:</strong>
                        <div class="mt-2">
                            <code>GET /medical-order/{encounter_id}/download</code>
                        </div>
                        <div class="mt-2">
                            <strong>Ejemplo:</strong>
                            <a href="{{ route('medical-order.download', 61) }}" 
                               class="btn btn-sm btn-success" target="_blank">
                                <i class="fas fa-download"></i> Descargar Orden (Encounter #61)
                            </a>
                            <a href="{{ route('medical-order.preview', 61) }}" 
                               class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="fas fa-eye"></i> Vista Previa
                            </a>
                        </div>
                    </div>

                    <!-- Orden personalizada -->
                    <div class="mb-3">
                        <strong>2. Orden personalizada (servicios específicos):</strong>
                        <div class="mt-2">
                            <code>POST /medical-order/custom/download</code>
                        </div>
                        <div class="mt-2">
                            <form action="{{ route('medical-order.custom.download') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="service_ids[]" value="1">
                                <input type="hidden" name="service_ids[]" value="2">
                                <button type="submit" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i> Orden Personalizada
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <strong>Características:</strong>
                        <ul class="mb-0">
                            <li>Incluye diagnósticos como causal de la orden</li>
                            <li>Códigos CPT y descripciones detalladas</li>
                            <li>Manejo de prioridades (urgente, rutina, etc.)</li>
                            <li>Validez de 60 días</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ejemplos de integración en código -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">💻 Ejemplos de Integración en Código</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>En las vistas de Encounter:</h6>
                            <pre><code>{{-- Botones para generar documentos --}}
@if($encounter->medicationRequests->count() > 0)
    <a href="{{ route('prescription.download', $encounter->id) }}" 
       class="btn btn-primary">
        <i class="fas fa-prescription"></i> Descargar Receta
    </a>
@endif

@if($encounter->serviceRequests->count() > 0)
    <a href="{{ route('medical-order.download', $encounter->id) }}" 
       class="btn btn-success">
        <i class="fas fa-file-medical"></i> Descargar Orden
    </a>
@endif</code></pre>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>En los listados de medicamentos/servicios:</h6>
                            <pre><code>{{-- Botón para receta de medicamentos seleccionados --}}
<form action="{{ route('prescription.custom.download') }}" 
      method="POST" target="_blank">
    @csrf
    @foreach($selectedMedications as $med)
        <input type="hidden" name="medication_ids[]" value="{{ $med->id }}">
    @endforeach
    <button type="submit" class="btn btn-outline-primary">
        <i class="fas fa-download"></i> Generar Receta
    </button>
</form>

{{-- Botón para orden de servicios seleccionados --}}
<form action="{{ route('medical-order.custom.download') }}" 
      method="POST" target="_blank">
    @csrf
    @foreach($selectedServices as $service)
        <input type="hidden" name="service_ids[]" value="{{ $service->id }}">
    @endforeach
    <button type="submit" class="btn btn-outline-success">
        <i class="fas fa-download"></i> Generar Orden
    </button>
</form></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información técnica -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ℹ️ Información Técnica</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Dependencias requeridas:</h6>
                            <ul>
                                <li><code>barryvdh/laravel-dompdf</code></li>
                                <li>PHP extensión: <code>mbstring</code></li>
                                <li>PHP extensión: <code>gd</code></li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6>Datos incluidos en los PDFs:</h6>
                            <ul>
                                <li>Información del paciente</li>
                                <li>Información del médico</li>
                                <li>Diagnósticos del encounter</li>
                                <li>Medicamentos/servicios solicitados</li>
                                <li>Firmas y sellos</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <h6>Seguridad:</h6>
                            <ul>
                                <li>Rutas protegidas con middleware <code>auth</code></li>
                                <li>Validación de permisos recomendada</li>
                                <li>Verificación de existencia de datos</li>
                                <li>Manejo de errores robusto</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 10px;
    font-size: 12px;
    overflow-x: auto;
}

code {
    background-color: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 85%;
}
</style>
@endpush

@push('js')
<script>
    // Manejo de formularios de descarga
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar loading state a los botones de descarga
        document.querySelectorAll('form[target="_blank"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
                    submitBtn.disabled = true;
                    
                    // Restaurar después de 3 segundos
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                }
            });
        });
    });
</script>
@endpush
@endsection