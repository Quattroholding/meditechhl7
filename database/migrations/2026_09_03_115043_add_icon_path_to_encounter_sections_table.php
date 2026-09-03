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
        Schema::table('encounter_sections', function (Blueprint $table) {
            $table->string('icon_path')->nullable()->after('icon')->comment('Path to icon image file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounter_sections', function (Blueprint $table) {
            $table->dropColumn('icon_path');
        });
    }
};
