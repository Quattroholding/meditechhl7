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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Observation resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners');
            $table->string('identifier')->nullable();
            $table->enum('status', ['registered', 'preliminary', 'final', 'amended', 'corrected', 'cancelled', 'entered-in-error', 'unknown']);
            $table->string('code')->comment('LOINC o SNOMED code para el tipo de observación');
            $table->string('category')->comment('vital-signs, imaging, laboratory, etc.');
            $table->string('value')->nullable()->comment('Valor numérico si aplica');
            $table->string('value_string')->nullable()->comment('Valor como string si aplica');
            $table->string('unit')->nullable();
            $table->string('interpretation')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->dateTime('issued_date')->nullable();
            $table->json('reference_range')->nullable();
            $table->json('component')->nullable()->comment('Para observaciones con múltiples componentes');
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
        Schema::dropIfExists('observations');
    }
};
