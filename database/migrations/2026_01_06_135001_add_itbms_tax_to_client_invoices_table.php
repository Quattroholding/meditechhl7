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
        Schema::table('client_invoices', function (Blueprint $table) {
            // Add ITBMS tax fields (7% Panama tax)
            $table->decimal('tax_rate', 5, 4)->default(0.07)->after('discount_reason')
                ->comment('Tax rate (e.g., 0.07 for 7% ITBMS)');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate')
                ->comment('Calculated tax amount in USD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_amount']);
        });
    }
};
