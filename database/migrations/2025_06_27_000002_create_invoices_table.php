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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->index(); // FHIR Invoice.id

            // FHIR Required fields
            $table->enum('status', ['draft', 'issued', 'balanced', 'cancelled', 'entered-in-error']);

            // Business identifiers
            $table->json('identifier')->nullable(); // Business identifiers
            $table->string('invoice_number')->unique()->index(); // Human-readable invoice number

            // Subject (Patient/Group)
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('cascade');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('performer_practitioner_id')->nullable()->constrained('practitioners')->onDelete('cascade');
            // Issuer and Recipient
            $table->foreignId('issuer_organization_id')->constrained('clients', 'id')->onDelete('cascade');
            $table->foreignId('recipient_organization_id')->nullable()->constrained('clients', 'id')->onDelete('set null');
            $table->foreignId('recipient_patient_id')->nullable()->constrained('patients')->onDelete('set null');

            // Dates
            $table->date('date')->comment('Invoice creation date'); // Invoice creation date
            $table->date('issue_date')->nullable()->comment('Issue date'); // Issue due date
            $table->date('due_date')->nullable()->comment('Payment due date'); // Payment due date
            $table->json('period_start_end')->nullable()->comment('Billing period'); // Billing period

            // Financial totals
            $table->string('currency', 3)->default('USD'); // ISO 4217 currency code
            $table->decimal('subtotal_amount', 12, 2)->default(0.00)->comment('SubTotal before tax'); // Total before tax
            $table->decimal('tax_amount', 12, 2)->default(0.00)->comment('Total before tax'); // Total before tax
            $table->decimal('total_amount', 12, 2)->default(0.00)->comment('Total before tax'); // Total before tax
            $table->decimal('total_preTax', 12, 2)->default(0.00)->comment('Total before tax'); // Total before tax
            $table->decimal('total_tax', 12, 2)->default(0.00)->comment('Tax amount'); // Tax amount
            $table->decimal('total_gross', 12, 2)->default(0.00)->comment('Total including tax'); // Total including tax
            $table->decimal('total_net', 12, 2)->default(0.00)->comment('Total payable amount'); // Total payable amount

            // Payment information
            $table->enum('payment_status', ['paid', 'partial', 'pending', 'overdue', 'cancelled', 'unpaid'])->default('pending');
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->decimal('amount_due', 12, 2)->default(0.00);
            $table->date('payment_date')->nullable();
            $table->text('payment_method')->nullable();
            $table->text('payment_reference')->nullable();
            $table->text('payment_terms')->nullable();

            // Additional FHIR fields
            $table->enum('type', ['invoice', 'credit-note', 'receipt'])->default('invoice');
            $table->json('participant')->nullable(); // Participants array
            $table->text('cancellation_reason')->nullable();
            $table->text('note')->nullable(); // Annotations

            // Business fields
            $table->text('description')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('reference_number')->nullable(); // PO number, etc.
            $table->text('terms_and_conditions')->nullable();

            // Multi-tenant support
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['patient_id', 'status']);
            $table->index(['issuer_organization_id', 'date']);
            $table->index(['client_id', 'status']);
            $table->index(['payment_status', 'due_date']);
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
