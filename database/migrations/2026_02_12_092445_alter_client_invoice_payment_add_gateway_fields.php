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
        Schema::table('client_invoice_payments', function (Blueprint $table) {

            $table->string('auth_code')->nullable();
            $table->string('avs_code')->nullable();
            $table->string('cvv_code')->nullable();

            $table->string('processor_response')->nullable();
            $table->string('network_transaction_id')->nullable();
            $table->string('reconciliation_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_invoice_payments', function (Blueprint $table) {
            $table->dropColumn([
                'auth_code',
                'avs_code',
                'cvv_code',
                'processor_response',
                'network_transaction_id',
                'reconciliation_id',
            ]);
        });
    }
};
