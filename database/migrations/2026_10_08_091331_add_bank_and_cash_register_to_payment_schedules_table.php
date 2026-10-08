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
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->after('updated_by')->constrained('banks')->onDelete('set null');
            $table->foreignId('cash_register_id')->nullable()->after('bank_id')->constrained('cash_registers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_id');
            $table->dropConstrainedForeignId('cash_register_id');
        });
    }
};
