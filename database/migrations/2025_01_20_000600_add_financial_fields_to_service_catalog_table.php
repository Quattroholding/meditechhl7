<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if table doesn't exist
        if (! Schema::hasTable('service_catalog')) {
            return;
        }

        Schema::table('service_catalog', function (Blueprint $table) {
            // Reemplazar gl_account string con FK estructurado
            if (Schema::hasColumn('service_catalog', 'gl_account')) {
                $table->dropColumn('gl_account');
            }

            // Agregar FK a AccountingAccount
            if (! Schema::hasColumn('service_catalog', 'accounting_account_id')) {
                $table->foreignId('accounting_account_id')->nullable()->constrained('accounting_accounts')->onDelete('set null')
                    ->after('cost_center')
                    ->comment('Cuenta contable por defecto para ingresos de este servicio');
            }

            // Clasificación financiera
            if (! Schema::hasColumn('service_catalog', 'income_classification')) {
                $table->string('income_classification')->nullable()
                    ->after('accounting_account_id')
                    ->comment('CONSULTATION, PROCEDURE, MEDICATION, SUPPLY, etc.');
            }

            // Auditoría contable
            if (! Schema::hasColumn('service_catalog', 'accounting_config_updated_at')) {
                $table->timestamp('accounting_config_updated_at')->nullable()
                    ->after('income_classification')
                    ->comment('Cuándo se actualizó la config contable');
            }

            if (! Schema::hasColumn('service_catalog', 'accounting_config_updated_by')) {
                $table->foreignId('accounting_config_updated_by')->nullable()->constrained('users')->onDelete('set null')
                    ->after('accounting_config_updated_at')
                    ->comment('Quién hizo el cambio contable');
            }

            // Índices
            $table->index('accounting_account_id');
            $table->index('income_classification');
        });
    }

    public function down(): void
    {
        Schema::table('service_catalog', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['accounting_account_id']);
            $table->dropForeignKeyIfExists(['accounting_config_updated_by']);
            $table->dropColumn([
                'accounting_account_id',
                'income_classification',
                'accounting_config_updated_at',
                'accounting_config_updated_by',
            ]);
        });
    }
};
