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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('insurance_claim_id')->nullable()->constrained('insurance_claims')->onDelete('set null');
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_source', ['patient', 'insurance', 'copay', 'deductible', 'coinsurance', 'adjustment', 'refund']);
            $table->enum('payment_method', ['cash', 'check', 'credit_card', 'debit_card', 'bank_transfer', 'insurance_payment', 'other']);
            $table->string('payment_details')->nullable(); // Para detalles como últimos 4 dígitos de tarjeta
            $table->date('payment_date');
            $table->date('posting_date');
            $table->string('transaction_id')->nullable();
            $table->string('check_number')->nullable();
            $table->string('authorization_code')->nullable();
            $table->enum('status', ['pending', 'processed', 'cleared', 'failed', 'refunded', 'voided'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['invoice_id', 'payment_date']);
            $table->index(['patient_id', 'payment_date']);
            $table->index(['payment_source', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
