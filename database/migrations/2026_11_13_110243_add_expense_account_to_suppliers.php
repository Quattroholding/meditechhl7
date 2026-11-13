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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add expense account field for supplier-specific expense classification
            $table->foreignId('expense_account_id')
                ->nullable()
                ->constrained('accounting_accounts')
                ->cascadeOnDelete();

            // Add index for performance
            $table->index(['client_id', 'expense_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['expense_account_id']);
            $table->dropIndex(['client_id', 'expense_account_id']);
            $table->dropColumn('expense_account_id');
        });
    }
};
