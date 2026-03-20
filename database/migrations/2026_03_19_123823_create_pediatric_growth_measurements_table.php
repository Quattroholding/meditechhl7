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
        Schema::create('pediatric_growth_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Observation resource ID');
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms');
            $table->date('measurement_date');
            $table->integer('age_months')->comment('Age in months at time of measurement');
            $table->enum('gender', ['male', 'female'])->comment('Gender for WHO reference (normalized from patient.gender)');
            $table->decimal('weight_kg', 5, 2)->nullable()->comment('Body weight in kilograms');
            $table->decimal('height_cm', 5, 1)->nullable()->comment('Height/length in centimeters');
            $table->decimal('head_circumference_cm', 5, 1)->nullable()->comment('Head circumference in centimeters');
            $table->decimal('bmi', 5, 2)->nullable()->comment('Body mass index (auto-calculated)');
            $table->decimal('weight_for_age_percentile', 5, 2)->nullable();
            $table->decimal('weight_for_age_zscore', 5, 2)->nullable();
            $table->decimal('height_for_age_percentile', 5, 2)->nullable();
            $table->decimal('height_for_age_zscore', 5, 2)->nullable();
            $table->decimal('bmi_for_age_percentile', 5, 2)->nullable();
            $table->decimal('bmi_for_age_zscore', 5, 2)->nullable();
            $table->decimal('head_circ_for_age_percentile', 5, 2)->nullable();
            $table->decimal('head_circ_for_age_zscore', 5, 2)->nullable();
            $table->enum('status', ['registered', 'preliminary', 'final', 'amended'])->default('final');
            $table->string('nutritional_status')->nullable()->comment('normal, underweight, overweight, stunted, wasted, etc.');
            $table->json('fhir_component')->nullable()->comment('FHIR Observation components structure');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'measurement_date']);
            $table->index(['patient_id', 'age_months']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pediatric_growth_measurements');
    }
};
