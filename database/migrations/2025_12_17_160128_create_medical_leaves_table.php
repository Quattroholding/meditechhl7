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
        Schema::create('medical_leaves', function (Blueprint $table) {
            $table->id();

            // FHIR Identifiers
            $table->string('fhir_id')->unique()->nullable();
            $table->string('identifier')->unique(); // Número de registro de la licencia

            // Relaciones obligatorias FHIR
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained()->nullOnDelete(); // Condición asociada
            $table->foreignId('practitioner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete(); // Multi-tenant
            $table->foreignId('consulting_room_id')->constrained()->cascadeOnDelete(); // Consultorio

            // Datos del médico y clínica (para el documento impreso)
            $table->string('practitioner_name'); // Nombre del médico
            $table->string('practitioner_license_number')->nullable(); // Registro médico
            $table->string('clinic_name'); // Nombre de la clínica
            $table->string('clinic_address')->nullable(); // Dirección de la clínica
            $table->string('clinic_phone')->nullable(); // Teléfono de la clínica
            $table->string('clinic_logo_url')->nullable(); // URL del logo

            // Datos del paciente (para el documento)
            $table->string('patient_name'); // Nombre del paciente

            // Fechas y horas de la incapacidad (según requisitos panameños)
            $table->dateTime('start_datetime'); // Fecha y hora de inicio
            $table->dateTime('end_datetime'); // Fecha y hora de fin
            $table->integer('total_days')->nullable(); // Días totales calculados

            // Información clínica
            $table->text('diagnosis')->nullable(); // Diagnóstico que justifica la licencia
            $table->string('diagnosis_code')->nullable(); // Código ICD-10 si aplica

            // Estado del documento
            $table->enum('status', [
                'draft',        // Borrador
                'active',       // Activa
                'completed',    // Completada
                'cancelled',    // Cancelada
                'entered-in-error', // Error en captura
            ])->default('active');

            // Metadata del documento
            $table->date('issue_date'); // Fecha de emisión
            $table->text('notes')->nullable(); // Notas adicionales

            // FHIR Extensions (para datos adicionales)
            $table->json('extension')->nullable();

            // Auditoría
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['patient_id', 'issue_date']);
            $table->index(['encounter_id']);
            $table->index(['practitioner_id', 'issue_date']);
            $table->index(['client_id', 'issue_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_leaves');
    }
};
