<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_request_results', function (Blueprint $table) {
            $table->id();

            // Identificador único HL7
            $table->string('fhir_id')->unique()->comment('Identificador único HL7');

            // Relación con ServiceRequest
            $table->foreignId('service_request_id')->constrained('service_requests')->onDelete('cascade')->comment('Solicitud de servicio relacionada');

            // Relación con Patient
            $table->foreignId('patient_id')->constrained()->comment('Paciente al que pertenece el resultado');

            // Relación con Practitioner (profesional que reporta)
            $table->foreignId('practitioner_id')->constrained('practitioners')->comment('Profesional que reporta el resultado');

            // Estado del resultado
            $table->enum('status', ['registered', 'partial', 'preliminary', 'final', 'amended', 'corrected', 'appended', 'cancelled', 'entered-in-error', 'unknown'])->default('registered')->comment('Estado del resultado según HL7');

            // Tipo de resultado
            $table->string('result_type')->comment('Tipo de resultado (laboratory, pathology, radiology, etc.)');

            // Código y descripción
            $table->string('code')->comment('Código del examen/resultado');
            $table->string('code_system')->nullable()->comment('Sistema de codificación');
            $table->string('code_display')->nullable()->comment('Descripción del código');

            // Archivo del resultado
            $table->string('file_path')->comment('Ruta del archivo almacenado');
            $table->string('file_name')->comment('Nombre original del archivo');
            $table->string('file_type')->comment('Tipo MIME del archivo (application/pdf, application/msword, etc.)');
            $table->bigInteger('file_size')->comment('Tamaño del archivo en bytes');
            
            // Hash para verificar integridad
            $table->string('file_hash')->comment('Hash SHA256 del archivo para verificar integridad');

            // Metadatos adicionales
            $table->json('metadata')->nullable()->comment('Metadatos adicionales del archivo en formato JSON');
            
            // Fecha/hora del resultado
            $table->dateTime('result_date')->comment('Fecha/hora cuando se generó el resultado');
            $table->dateTime('uploaded_at')->comment('Fecha/hora de carga del archivo');

            // Observaciones y notas
            $table->text('observations')->nullable()->comment('Observaciones clínicas del resultado');
            $table->text('notes')->nullable()->comment('Notas adicionales');

            // Campos para interpretación
            $table->enum('interpretation', ['critical', 'high', 'low', 'normal', 'abnormal', 'better', 'worse', 'resistant', 'susceptible', 'intermediate'])->nullable()->comment('Interpretación del resultado');
            
            // Referencias
            $table->text('reference_range')->nullable()->comment('Rango de referencia');
            $table->json('specimen_info')->nullable()->comment('Información de la muestra en formato JSON');

            // Control de versiones
            $table->integer('version')->default(1)->comment('Versión del resultado');
            $table->foreignId('replaces_id')->nullable()->constrained('service_request_results')->comment('ID del resultado que reemplaza');

            // Campos de auditoría
            $table->dateTime('effective_date')->comment('Fecha efectiva del resultado');
            $table->dateTime('issued_date')->comment('Fecha de emisión del resultado');

            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index(['service_request_id', 'patient_id']);
            $table->index(['status', 'result_type']);
            $table->index(['code', 'result_date']);
            $table->index('file_hash');
            $table->index('result_date');
            $table->unique(['file_hash', 'service_request_id'], 'unique_file_per_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_results');
    }
};
