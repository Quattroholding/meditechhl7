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
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Condition resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners');
            $table->string('identifier')->nullable()->comment('Identificador del diagnóstico');
            $table->enum('clinical_status', ['active', 'recurrence', 'relapse', 'inactive', 'remission', 'resolved']);
            $table->enum('verification_status', ['unconfirmed', 'provisional', 'differential', 'confirmed', 'refuted', 'entered-in-error']);
            $table->string('code')->comment('Código del diagnóstico (ICD-10, SNOMED)');
            $table->string('category')->nullable();
            $table->string('severity')->nullable();
            $table->date('onset_date')->nullable();
            $table->text('onset_info')->nullable();
            $table->date('abatement_date')->nullable();
            $table->text('abatement_info')->nullable();
            $table->date('recorded_date')->nullable();
            $table->text('note')->nullable();
            $table->json('evidence')->nullable()->comment('Evidencia que soporta el diagnóstico');
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
