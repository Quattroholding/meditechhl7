<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Referencia
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->onDelete('cascade');

            // Fecha y monto
            $table->date('payment_date')->comment('Fecha programada de pago');
            $table->decimal('amount', 15, 2)->comment('Monto a pagar en esta cuota');

            // Estado del pago
            $table->boolean('paid')->default(false);
            $table->timestamp('paid_at')->nullable();

            // Vinculación a movimiento de tesorería (cuando se procesa)
            $table->foreignId('treasury_movement_id')->nullable()->constrained('treasury_movements')->onDelete('set null');

            // Notas
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('supplier_invoice_id');
            $table->index('payment_date');
            $table->index('paid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
