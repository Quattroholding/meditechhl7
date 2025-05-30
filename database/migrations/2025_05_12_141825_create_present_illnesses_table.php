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
        Schema::create('present_illnesses', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Condition/ClinicalImpression resource ID');
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->text('description')->comment('Narrativa de la enfermedad actual');
            $table->string('location')->nullable()->comment('Localización anatómica');
            $table->enum('severity', ['mild', 'moderate', 'severe','disabling'])->nullable();
            $table->string('duration',50)->nullable()->comment('Duración de los síntomas');
            $table->string('timing',50)->nullable()->comment('Momento del dia de los síntomas');
            $table->enum('onset', ['sudden', 'acute', 'gradual'])->nullable();
            $table->dateTime('onset_date')->nullable();
            $table->text('aggravating_factors')->nullable();
            $table->text('alleviating_factors')->nullable();
            $table->text('associated_symptoms')->nullable();
            $table->json('timeline')->nullable()->comment('Cronología de la enfermedad');
            $table->json('extension')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['encounter_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('present_illnesses');
    }
};
