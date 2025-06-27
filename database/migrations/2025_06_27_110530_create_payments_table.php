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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->index();
            
            // Foreign keys
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Payment details
            $table->string('payment_number')->unique()->index();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'check', 'online', 'insurance', 'other']);
            $table->string('reference_number')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('completed');
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional payment gateway info
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['invoice_id', 'payment_date']);
            $table->index(['patient_id', 'payment_date']);
            $table->index(['client_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
