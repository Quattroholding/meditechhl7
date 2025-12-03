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
        Schema::create('body_structures', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR BodyStructure resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('set null');
            $table->string('identifier')->nullable()->unique();
            $table->boolean('active')->default(true);

            // Morphology - tipo de estructura (nevus, melanoma, lesión, etc.)
            $table->string('morphology_code')->nullable()->comment('SNOMED CT code for morphology');
            $table->string('morphology_display')->nullable()->comment('Human-readable morphology type');

            // Location - ubicación anatómica
            $table->string('location_code')->comment('SNOMED CT code for body site');
            $table->string('location_display')->comment('Human-readable body location');
            $table->string('location_qualifier_code')->nullable()->comment('Laterality: left, right, bilateral');
            $table->string('location_qualifier_display')->nullable();

            // Clinical information
            $table->text('description')->nullable()->comment('Clinical description of the structure');
            $table->json('image_references')->nullable()->comment('References to Media resources');

            // FHIR standard fields
            $table->json('extension')->nullable()->comment('FHIR extensions');
            $table->json('included_structures')->nullable()->comment('Contained structures');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['patient_id', 'active']);
            $table->index('location_code');
            $table->index('morphology_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_structures');
    }
};
