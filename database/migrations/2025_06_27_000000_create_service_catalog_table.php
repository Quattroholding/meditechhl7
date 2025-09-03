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
        Schema::create('service_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->index(); // FHIR resource id

            // Service identification
            $table->string('code')->unique()->index()->comment('Internal service code'); // Internal service code
            $table->string('cpt_code')->nullable()->index(); // CPT/HCPCS code
            $table->string('hcpcs_code')->nullable();
            $table->string('icd10_pcs_code')->nullable(); // ICD-10-PCS for procedures
            $table->string('snomed_code')->nullable(); // SNOMED CT code

            // Service details
            $table->string('name'); // Service name
            $table->text('description')->nullable();
            $table->enum('service_type', [
                'consultation',
                'procedure',
                'diagnostic',
                'therapeutic',
                'surgical',
                'laboratory',
                'imaging',
                'medication',
                'supply',
                'facility',
                'other',
            ]);

            // Pricing information
            $table->decimal('base_price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_unit', 50)->default('each')->comment('each, hour, day, etc.'); // each, hour, day, etc.
            $table->decimal('duration_minutes', 8, 2)->nullable()->comment('Expected duration'); // Expected duration

            // Clinical categories
            $table->string('specialty')->nullable(); // Medical specialty
            $table->string('body_system')->nullable(); // Body system involved
            $table->enum('complexity', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('requires_authorization')->default(false);

            // Revenue and cost accounting
            $table->string('revenue_code')->nullable(); // UB-04 revenue code
            $table->string('cost_center')->nullable();
            $table->string('gl_account')->nullable(); // General ledger account
            $table->decimal('cost_estimate', 12, 2)->nullable(); // Estimated cost
            $table->decimal('markup_percentage', 5, 2)->default(0.00); // Markup %

            // Availability and constraints
            $table->boolean('is_active')->default(true);
            $table->date('effective_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->json('available_locations')->nullable(); // Array of location IDs
            $table->json('qualified_practitioners')->nullable(); // Array of practitioner IDs

            // Insurance and billing
            $table->boolean('covered_by_insurance')->default(true);
            $table->json('insurance_codes')->nullable(); // Different codes per insurance
            $table->decimal('patient_copay', 8, 2)->default(0.00);
            $table->decimal('insurance_allowable', 12, 2)->nullable();

            // Additional FHIR mappings
            $table->json('coding_systems')->nullable(); // Multiple coding systems
            $table->json('related_services')->nullable(); // References to related services
            $table->text('clinical_notes')->nullable();
            $table->json('prerequisites')->nullable(); // Required prior services/conditions

            // Multi-tenant support
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners')->onDelete('cascade');

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['service_type', 'is_active']);
            $table->index(['specialty', 'is_active']);
            $table->index(['client_id', 'is_active']);
            // $table->index('cpt_code');
            $table->index('revenue_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_catalog');
    }
};
