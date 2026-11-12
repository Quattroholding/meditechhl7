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
        Schema::table('accounting_accounts', function (Blueprint $table) {
            $table->enum('nature', ['debit', 'credit'])->nullable()->after('account_type');
            $table->boolean('requires_cost_center')->default(false)->after('allows_transaction');
            $table->boolean('is_system_account')->default(false)->after('requires_cost_center');
            $table->boolean('is_control_account')->default(false)->after('is_system_account');
            $table->boolean('closes_period')->default(false)->after('is_control_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_accounts', function (Blueprint $table) {
            $table->dropColumn(['nature', 'requires_cost_center', 'is_system_account', 'is_control_account', 'closes_period']);
        });
    }
};
