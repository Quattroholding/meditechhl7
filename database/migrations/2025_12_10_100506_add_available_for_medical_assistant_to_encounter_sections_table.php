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
            $table->boolean('available_for_medical_assistant')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounter_sections', function (Blueprint $table) {
            $table->dropColumn('available_for_medical_assistant');
        });
    }
};
