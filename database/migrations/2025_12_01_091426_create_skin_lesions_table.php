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
        Schema::create('skin_lesions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('body_structure_id')->constrained('body_structures')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('set null');
            $table->foreignId('practitioner_id')->constrained('practitioners');

            // Clinical characteristics
            $table->enum('lesion_type', [
                'nevus', 'melanoma', 'basal_cell_carcinoma', 'squamous_cell_carcinoma',
                'keratosis', 'wart', 'cyst', 'lipoma', 'hemangioma', 'other',
            ])->comment('Type of skin lesion');

            $table->string('color')->nullable()->comment('Color of the lesion');
            $table->decimal('size_length_mm', 8, 2)->nullable()->comment('Length in millimeters');
            $table->decimal('size_width_mm', 8, 2)->nullable()->comment('Width in millimeters');
            $table->decimal('size_height_mm', 8, 2)->nullable()->comment('Height/depth in millimeters');

            // ABCDE criteria for melanoma screening
            $table->enum('asymmetry', ['symmetric', 'asymmetric'])->nullable();
            $table->enum('border', ['regular', 'irregular'])->nullable();
            $table->string('color_variation')->nullable()->comment('Color uniformity assessment');
            $table->enum('diameter', ['less_than_6mm', 'greater_than_6mm'])->nullable();
            $table->enum('evolving', ['stable', 'changing'])->nullable();

            // Clinical assessment
            $table->enum('risk_level', ['low', 'moderate', 'high', 'suspicious'])->nullable();
            $table->text('clinical_notes')->nullable();
            $table->date('first_observed_date')->nullable();
            $table->date('last_examination_date')->nullable();
            $table->boolean('requires_biopsy')->default(false);
            $table->boolean('requires_removal')->default(false);
            $table->date('next_follow_up_date')->nullable();

            // Status tracking
            $table->enum('status', ['active', 'removed', 'resolved', 'under_treatment'])->default('active');
            $table->text('treatment_plan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['patient_id', 'status']);
            $table->index('encounter_id');
            $table->index('risk_level');
            $table->index('next_follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skin_lesions');
    }
};
