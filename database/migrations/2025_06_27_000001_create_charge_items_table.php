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
        Schema::create('charge_items', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->index(); // FHIR ChargeItem.id

            // FHIR Required fields
            $table->enum('status', ['planned', 'billable', 'not-billable', 'aborted', 'billed', 'entered-in-error', 'unknown']);
            $table->json('code')->comment('CodeableConcept - CPT, ICD-10-PCS, SNOMED, etc'); // CodeableConcept - CPT, ICD-10-PCS, SNOMED, etc.

            // Subject (Patient)
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');

            // Context (Encounter/Episode)
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');

            // Occurrence
            $table->dateTime('occurrence_date_time')->nullable();
            $table->json('occurrence_period')->nullable()->comment('Period with start/end'); // Period with start/end

            // Performer
            $table->foreignId('performer_practitioner_id')->nullable()->constrained('practitioners')->onDelete('set null');
            $table->foreignId('performer_organization_id')->nullable()->constrained('clients', 'id')->onDelete('set null');

            // Financial Information
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit_price_currency', 3)->default('USD')->comment('ISO 4217 currency code'); // ISO 4217 currency code
            $table->decimal('unit_price_value', 12, 2);
            $table->decimal('factor_override', 8, 4)->nullable()->comment('Multiplicador'); // Multiplicador
            $table->decimal('price_override', 12, 2)->nullable()->comment('Precio total overrid'); // Precio total override

            // Additional FHIR fields
            $table->json('identifier')->nullable(); // Identifiers array
            $table->text('definition_uri')->nullable(); // Definition reference
            $table->json('definition_canonical')->nullable(); // DefinitionCanonical
            $table->json('part_of')->nullable(); // References to parent ChargeItems
            $table->json('product_reference')->nullable(); // Reference to product/service
            $table->json('product_codeable_concept')->nullable(); // CodeableConcept for product
            $table->json('account')->nullable(); // Account references
            $table->text('note')->nullable(); // Annotation array as text
            $table->json('supporting_information')->nullable(); // Reference array

            // Billing codes
            $table->string('cpt_code')->nullable()->index()->comment('CPT/HCPCS code'); // CPT/HCPCS code
            $table->string('icd10_code')->nullable()->index()->comment('ICD-10-CM diagnosis code'); // ICD-10-CM diagnosis code
            $table->string('revenue_code')->nullable()->comment('UB-04 revenue code'); // UB-04 revenue code
            $table->string('modifier_codes')->nullable()->comment('CPT modifiers'); // CPT modifiers

            // Multi-tenant support
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['patient_id', 'status']);
            $table->index(['encounter_id', 'status']);
            //$table->index(['performer_practitioner_id', 'occurrence_date_time']);
            $table->index(['client_id', 'status']);
            //$table->index('cpt_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_items');
    }
};
