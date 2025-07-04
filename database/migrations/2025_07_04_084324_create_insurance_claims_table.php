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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_insurance_policy_id')->constrained('patient_insurance_policies')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('set null');
            $table->string('claim_number')->unique();
            $table->date('claim_date');
            $table->date('service_date');
            $table->decimal('billed_amount', 10, 2);
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('patient_responsibility', 10, 2)->default(0.00);
            $table->decimal('copay_amount', 10, 2)->default(0.00);
            $table->decimal('deductible_amount', 10, 2)->default(0.00);
            $table->decimal('coinsurance_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'submitted', 'processing', 'approved', 'partially_paid', 'paid', 'denied', 'rejected', 'cancelled'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->text('rejection_details')->nullable();
            $table->date('submitted_date')->nullable();
            $table->date('processed_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('authorization_number')->nullable();
            $table->json('diagnosis_codes')->nullable(); // ICD-10 codes
            $table->json('procedure_codes')->nullable(); // CPT codes
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'claim_date']);
            $table->index(['patient_insurance_policy_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
