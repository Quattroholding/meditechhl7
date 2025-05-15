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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            // Identificador único HL7
            $table->string('fhir_id')->unique()->comment('Identificador único HL7');

            // Relación con Encounter
            $table->foreignId('encounter_id')->constrained()->comment('Referencia al encuentro relacionado');

            // Relación con Patient
            $table->foreignId('patient_id')->constrained()->comment('Paciente al que se dirige la solicitud');

            // Relación con Practitioner (médico solicitante)
            $table->foreignId('practitioner_id')->constrained('practitioners')->comment('Profesional que realiza la solicitud');

            // Campos HL7 estándar
            $table->enum('status', ['draft', 'active', 'on-hold','revoked','completed','entered-in-error','unknown']);
            $table->enum('intent', ['proposal', 'plan', 'directive','order','original-order','reflex-order','filler-order','instance-order','option']);
            $table->enum('priority', ['routine', 'urgent', 'asap','stat']);
             $table->boolean('do_not_perform')->default(false)->comment('Si es true, indica NO realizar el servicio');

            // Códigos HL7/LOINC
            $table->string('code')->comment('Código del servicio solicitado (LOINC o sistema local)');
            $table->string('service_type')->nullable()->comment('Tipo de servicio solicitado (procedure,images,laboratory,therapy)');
            $table->string('code_system')->nullable()->comment('Sistema de codificación (ej. http://loinc.org)');
            $table->string('code_display')->nullable()->comment('Descripción del código');

            // Cantidad/tamaño
            $table->integer('quantity')->nullable()->comment('Cantidad del servicio solicitado');
            $table->string('quantity_unit')->nullable()->comment('Unidad de medida');

            // Periodo/tiempo
            $table->dateTime('occurrence_start')->nullable()->comment('Fecha/hora de inicio solicitada');
            $table->dateTime('occurrence_end')->nullable()->comment('Fecha/hora de fin solicitada');

            // Contexto adicional
            $table->json('body_site')->nullable()->comment('Sitios corporales relevantes (codificados)');
            $table->text('note')->nullable()->comment('Notas adicionales');
            $table->text('patient_instruction')->nullable()->comment('Instrucciones para el paciente');

            // Datos de apoyo/justificación
            $table->json('supporting_info')->nullable()->comment('Información de apoyo en formato HL7');
            $table->text('reason_code')->nullable()->comment('Razón codificada para la solicitud');
            $table->text('reason_reference')->nullable()->comment('Referencia a condición/observación que justifica');

            // Fechas importantes
            $table->dateTime('authored_on')->comment('Fecha/hora de creación de la solicitud');
            $table->dateTime('last_updated')->comment('Última actualización');

            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index(['encounter_id', 'patient_id']);
            $table->index(['status', 'intent']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
