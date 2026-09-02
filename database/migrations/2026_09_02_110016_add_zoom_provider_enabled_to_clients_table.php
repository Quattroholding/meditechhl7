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
        Schema::table('clients', function (Blueprint $table) {
            // Zoom is now the only provider for virtual consultations
            // This column can be used for future feature flags (e.g., enable/disable Zoom for specific clients)
            $table->boolean('zoom_enabled')->default(true)->comment('Enable Zoom Healthcare for virtual consultations (always true - Zoom is now the only provider)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('zoom_enabled');
        });
    }
};
