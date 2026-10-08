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
        Schema::table('invoices', function (Blueprint $table) {
            // Añadir campo para vincular asiento contable
            if (! Schema::hasColumn('invoices', 'journal_entry_id')) {
                $table->unsignedBigInteger('journal_entry_id')->nullable()->after('updated_by');
                $table->foreign('journal_entry_id')
                    ->references('id')
                    ->on('journal_entries')
                    ->nullOnDelete();
            }

            // Añadir campo para vincular CxC
            if (! Schema::hasColumn('invoices', 'accounts_receivable_id')) {
                $table->unsignedBigInteger('accounts_receivable_id')->nullable()->after('journal_entry_id');
                $table->foreign('accounts_receivable_id')
                    ->references('id')
                    ->on('accounts_receivable')
                    ->nullOnDelete();
            }

            // Añadir campos para tesorería
            if (! Schema::hasColumn('invoices', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('accounts_receivable_id');
                $table->foreign('cost_center_id')
                    ->references('id')
                    ->on('cost_centers')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Remover claves foráneas primero
            if (Schema::hasColumn('invoices', 'journal_entry_id')) {
                $table->dropForeign(['journal_entry_id']);
                $table->dropColumn('journal_entry_id');
            }

            if (Schema::hasColumn('invoices', 'accounts_receivable_id')) {
                try {
                    $table->dropForeign(['accounts_receivable_id']);
                } catch (Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('accounts_receivable_id');
            }

            if (Schema::hasColumn('invoices', 'cost_center_id')) {
                $table->dropForeign(['cost_center_id']);
                $table->dropColumn('cost_center_id');
            }
        });
    }
};
