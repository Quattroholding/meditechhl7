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
        Schema::create('physical_exams', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Observation resource ID');
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->enum('status', ['registered', 'preliminary', 'final', 'amended', 'corrected', 'cancelled', 'entered-in-error', 'unknown']);
            $table->string('category')->default('exam');
            $table->string('code')->comment('Código del examen (SNOMED)');
            $table->text('description');
            $table->json('body_site')->nullable()->comment('Localización anatómica');
            $table->string('method')->nullable()->comment('Método de examen');
            $table->json('finding')->nullable()->comment('Hallazgos del examen');
            $table->text('conclusion')->nullable();
            $table->json('media')->nullable()->comment('Fotos/diagramas relacionados');
            $table->dateTime('effective_date');
            $table->json('extension')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['encounter_id', 'patient_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_exams');
    }
};
