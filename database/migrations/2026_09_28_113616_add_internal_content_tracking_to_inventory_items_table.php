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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->boolean('track_internal_content')->default(false)->after('expiration_tracking');
            $table->string('internal_unit')->nullable()->after('track_internal_content');
            $table->decimal('internal_units_per_presentation', 10, 2)->nullable()->after('internal_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('track_internal_content');
            $table->dropColumn('internal_unit');
            $table->dropColumn('internal_units_per_presentation');
        });
    }
};
