<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Multi-tenancy
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Referencias
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');

            // Identificación
            $table->string('invoice_number', 100)->comment('Número factura del proveedor');
            $table->date('invoice_date');
            $table->date('received_date')->nullable();
            $table->date('due_date');

            // Moneda
            $table->string('currency', 3)->default('PAB');

            // Montos
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->comment('Calculado: total - paid');

            // Clasificación
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->onDelete('set null');

            // Notas
            $table->text('notes')->nullable();

            // Estado
            $table->enum('status', ['draft', 'registered', 'approved', 'partial', 'paid', 'overdue', 'cancelled'])
                ->default('draft');

            // Aprobación
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // Asiento contable generado
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->unique(['client_id', 'invoice_number', 'supplier_id']);
            $table->index(['client_id', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('cost_center_id');
            $table->index('journal_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
