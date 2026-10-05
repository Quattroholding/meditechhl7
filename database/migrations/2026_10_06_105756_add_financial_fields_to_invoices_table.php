<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Add if columns don't exist
            if (! Schema::hasColumn('invoices', 'cost_center_id')) {
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');
            }
            if (! Schema::hasColumn('invoices', 'journal_entry_id')) {
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            }
            if (! Schema::hasColumn('invoices', 'accounts_receivable_id')) {
                $table->foreignId('accounts_receivable_id')->nullable()->constrained('accounts_receivable');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['cost_center_id']);
            $table->dropForeignKeyIfExists(['journal_entry_id']);
            $table->dropForeignKeyIfExists(['accounts_receivable_id']);
            $table->dropColumnIfExists(['cost_center_id', 'journal_entry_id', 'accounts_receivable_id']);
        });
    }
};
