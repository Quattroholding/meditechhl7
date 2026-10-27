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
        Schema::table('user_procedures', function (Blueprint $table) {
            $table->unsignedBigInteger('accounting_account_id')->nullable()->after('current_price');
            $table->unsignedBigInteger('cost_center_id')->nullable()->after('accounting_account_id');

            $table->foreign('accounting_account_id')
                ->references('id')
                ->on('accounting_accounts')
                ->onDelete('set null');

            $table->foreign('cost_center_id')
                ->references('id')
                ->on('cost_centers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_procedures', function (Blueprint $table) {
            $table->dropForeignKey(['accounting_account_id']);
            $table->dropForeignKey(['cost_center_id']);
            $table->dropColumn(['accounting_account_id', 'cost_center_id']);
        });
    }
};
