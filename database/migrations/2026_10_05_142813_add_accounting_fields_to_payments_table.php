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
        Schema::table('payments', function (Blueprint $table) {
            // Añadir campo para vincular asiento contable
            if (! Schema::hasColumn('payments', 'journal_entry_id')) {
                $table->unsignedBigInteger('journal_entry_id')->nullable()->after('updated_by');
                if (Schema::hasTable('journal_entries')) {
                    $table->foreign('journal_entry_id')
                        ->references('id')
                        ->on('journal_entries')
                        ->nullOnDelete();
                }
            }

            // Añadir campo para vincular movimiento de tesorería
            if (! Schema::hasColumn('payments', 'treasury_movement_id')) {
                $table->unsignedBigInteger('treasury_movement_id')->nullable()->after('journal_entry_id');
                if (Schema::hasTable('treasury_movements')) {
                    $table->foreign('treasury_movement_id')
                        ->references('id')
                        ->on('treasury_movements')
                        ->nullOnDelete();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover claves foráneas primero
            if (Schema::hasColumn('payments', 'journal_entry_id')) {
                $table->dropForeign(['journal_entry_id']);
                $table->dropColumn('journal_entry_id');
            }

            if (Schema::hasColumn('payments', 'treasury_movement_id')) {
                $table->dropForeign(['treasury_movement_id']);
                $table->dropColumn('treasury_movement_id');
            }
        });
    }
};
