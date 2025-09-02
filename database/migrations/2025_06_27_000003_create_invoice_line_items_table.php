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
        Schema::create('invoice_line_items', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->index(); // FHIR Invoice.lineItem id

            // Parent Invoice
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            // Line item sequence
            $table->integer('sequence')->default(1); // Order within invoice

            // Charge Item reference
            $table->foreignId('charge_item_id')->nullable()->constrained('charge_items')->onDelete('set null');

            // Service/Product details
            $table->json('service_code')->comment('CodeableConcept - CPT, HCPCS, etc.'); // CodeableConcept - CPT, HCPCS, etc.
            $table->string('service_description');
            $table->json('service_period')->nullable()->comment('When service was provided'); // When service was provided

            // Quantity and pricing
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit_of_measure', 50)->nullable(); // unit, hour, day, etc.
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);

            // Tax information
            $table->decimal('tax_rate', 5, 4)->default(0.0000); // e.g., 0.0825 for 8.25%
            $table->decimal('tax_amount', 12, 2)->default(0.00);

            // Line totals
            $table->decimal('line_total_pretax', 12, 2)->default(0.00);
            $table->decimal('line_total_tax', 12, 2)->default(0.00);
            $table->decimal('line_total_gross', 12, 2)->default(0.00);

            // FHIR specific fields
            $table->json('modifier')->nullable(); // CodeableConcept array for modifiers
            $table->json('revenue_category')->nullable(); // CodeableConcept for revenue classification
            $table->json('product_or_service_end')->nullable(); // End date for service

            // Billing codes
            $table->string('cpt_code')->nullable()->index();
            $table->string('hcpcs_code')->nullable();
            $table->string('icd10_code')->nullable();
            $table->string('revenue_code')->nullable();
            $table->json('modifier_codes')->nullable(); // Array of modifier codes

            // Provider information
            $table->foreignId('performer_practitioner_id')->nullable()->constrained('practitioners')->onDelete('set null');
            $table->foreignId('performing_organization_id')->nullable()->constrained('clients', 'id')->onDelete('set null');

            // Additional information
            $table->date('service_date')->nullable();
            $table->text('note')->nullable();
            $table->json('supporting_information')->nullable(); // References

            // Multi-tenant support
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['invoice_id', 'sequence']);
            $table->index(['charge_item_id']);
            $table->index(['performer_practitioner_id']);
            $table->index(['client_id']);
            // $table->index('cpt_code');

            // Unique constraint
            $table->unique(['invoice_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_line_items');
    }
};
