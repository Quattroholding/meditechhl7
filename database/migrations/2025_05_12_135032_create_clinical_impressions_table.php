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
        Schema::create('clinical_impressions', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR ClinicalImpression resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->constrained('encounters')->nullable();
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->enum('status', ['in-progress', 'completed', 'entered-in-error']);
            $table->text('description')->nullable();
            $table->json('problem')->nullable()->comment('Problemas/quejas del paciente');
            $table->text('summary')->nullable();
            $table->json('finding')->nullable()->comment('Hallazgos clínicos');
            $table->json('prognosis')->nullable();
            $table->json('protocol')->nullable();
            $table->json('supporting_info')->nullable();
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
        Schema::dropIfExists('clinical_impressions');
    }
};
