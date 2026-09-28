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
        Schema::table('inventory_reports', function (Blueprint $table) {
            $table->decimal('internal_units_on_hand', 10, 2)->default(0)->after('quantity_on_hand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_reports', function (Blueprint $table) {
            $table->dropColumn('internal_units_on_hand');
        });
    }
};
