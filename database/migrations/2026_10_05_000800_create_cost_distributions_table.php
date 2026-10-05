<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_distributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Referencias
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->onDelete('cascade');
            $table->foreignId('cost_center_id')->constrained('cost_centers')->onDelete('restrict');

            // Distribución
            $table->decimal('percentage', 5, 2)->comment('Porcentaje de distribución');
            $table->decimal('amount', 15, 2)->comment('Monto distribuido');

            $table->timestamps();
            $table->softDeletes();

            // Índices y constraints
            $table->unique(['supplier_invoice_id', 'cost_center_id']);
            $table->index('supplier_invoice_id');
            $table->index('cost_center_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_distributions');
    }
};
