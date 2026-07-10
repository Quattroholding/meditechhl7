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
        Schema::table('medication_requests', function (Blueprint $table) {
            // Add duration_type field (dias, semanas, meses, años, indefinido)
            $table->enum('duration_type', ['dias', 'semanas', 'meses', 'años', 'indefinido'])->nullable()->after('duration')->default('dias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_requests', function (Blueprint $table) {
            $table->dropColumn('duration_type');
        });
    }
};
