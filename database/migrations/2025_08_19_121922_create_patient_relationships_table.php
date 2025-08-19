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
        Schema::create('patient_relationships', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR RelatedPerson resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('related_patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->string('identifier')->nullable()->comment('External identifier for the related person');
            $table->string('identifier_type')->nullable();
            $table->string('name')->nullable()->comment('Name if not linked to a Patient');
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unknown'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->string('country', 75)->nullable();

            // FHIR relationship codes
            $table->string('relationship_code')->comment('FHIR relationship code (CHILD, PARENT, SPOUSE, etc.)');
            $table->string('relationship_display')->comment('Human readable relationship description');
            $table->string('relationship_system')->default('http://terminology.hl7.org/CodeSystem/v3-RoleCode');

            // Contact and emergency information
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_insurance_subscriber')->default(false);
            $table->json('contact_preferences')->nullable();

            // Period of validity
            $table->date('effective_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();
            $table->json('extension')->nullable()->comment('FHIR extensions');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['patient_id', 'relationship_code']);
            $table->index(['patient_id', 'is_emergency_contact']);
            $table->index(['patient_id', 'is_insurance_subscriber']);
            $table->index(['related_patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_relationships');
    }
};
