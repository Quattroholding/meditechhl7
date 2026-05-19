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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->index();
            $table->string('quotation_number')->unique()->index();

            // Cliente (información replicada para histórico)
            $table->string('client_company_name');
            $table->string('client_ruc');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();

            // Fechas
            $table->date('issue_date');
            $table->date('expiration_date');

            // Montos
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('itbms', 12, 2)->default(0.00);
            $table->decimal('itbms_rate', 5, 2)->default(7.00);
            $table->decimal('total', 12, 2)->default(0.00);

            // Estado
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'cancelled'])->default('draft');

            // Notas adicionales
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index(['issue_date', 'expiration_date']);
            $table->index('quotation_number');
            $table->index('client_ruc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
