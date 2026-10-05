<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if table doesn't exist (created in later migrations)
        if (! Schema::hasTable('document_uploads')) {
            return;
        }

        Schema::table('document_uploads', function (Blueprint $table) {
            // Vinculación a proveedor (puede crearse automáticamente)
            if (! Schema::hasColumn('document_uploads', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null')
                    ->after('total');
            }

            // Centro de costos (para clasificar gasto)
            if (! Schema::hasColumn('document_uploads', 'cost_center_id')) {
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->onDelete('set null')
                    ->after('supplier_id');
            }

            // Factura de proveedor generada
            if (! Schema::hasColumn('document_uploads', 'supplier_invoice_id')) {
                $table->foreignId('supplier_invoice_id')->nullable()->constrained('supplier_invoices')->onDelete('set null')
                    ->after('cost_center_id');
            }

            // Índices
            $table->index('supplier_id');
            $table->index('cost_center_id');
            $table->index('supplier_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('document_uploads', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['supplier_id']);
            $table->dropForeignKeyIfExists(['cost_center_id']);
            $table->dropForeignKeyIfExists(['supplier_invoice_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['cost_center_id']);
            $table->dropIndex(['supplier_invoice_id']);
            $table->dropColumn(['supplier_id', 'cost_center_id', 'supplier_invoice_id']);
        });
    }
};
