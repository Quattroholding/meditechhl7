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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Observation resource ID');
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners');
            $table->enum('status', ['registered', 'preliminary', 'final', 'amended', 'corrected', 'cancelled', 'entered-in-error', 'unknown']);
            $table->string('code')->comment('LOINC code para el tipo de signo vital');
            $table->string('category')->default('vital-signs');
            $table->decimal('value', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->string('interpretation')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('effective_date');
            $table->dateTime('issued_date');
            $table->json('reference_range')->nullable();
            $table->string('body_site')->nullable();
            $table->string('method')->nullable();
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
        Schema::dropIfExists('vital_signs');
    }
};
