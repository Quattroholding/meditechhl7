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
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->foreignId('observation_id')->nullable()->constrained('observations');
        });

        Schema::table('physical_exams', function (Blueprint $table) {

            $table->foreignId('observation_id')->nullable()->constrained('observations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->dropColumn('observation_id');
        });

        Schema::table('physical_exams', function (Blueprint $table) {
            $table->dropColumn('observation_id');
        });
    }
};
