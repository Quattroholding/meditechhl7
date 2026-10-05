<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            // Proveedor detectado por parser
            $table->string('detected_supplier')->nullable()
                ->after('processing_cost_usd')
                ->comment('Nombre del proveedor extraído del documento');

            // Ítems de inventario detectados
            $table->json('detected_items')->nullable()
                ->after('detected_supplier')
                ->comment('Líneas de inventario extraídas');

            // Centro de costos sugerido
            $table->string('detected_cost_center')->nullable()
                ->after('detected_items')
                ->comment('Centro de costo sugerido basado en contenido');

            // Clasificación financiera
            $table->enum('financial_classification', ['INVENTORY', 'UTILITIES', 'SUPPLIES', 'SERVICES', 'OTHER'])
                ->nullable()
                ->after('detected_cost_center')
                ->comment('Clasificación automática del gasto');

            // Índices
            $table->index('detected_supplier');
            $table->index('financial_classification');
        });
    }

    public function down(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            $table->dropColumn([
                'detected_supplier',
                'detected_items',
                'detected_cost_center',
                'financial_classification',
            ]);
        });
    }
};
