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
        Schema::table('rapid_access', function (Blueprint $table) {
            $table->foreignId('medication_id')->nullable()->after('medicine_id')->constrained('medications')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapid_access', function (Blueprint $table) {
            $table->dropForeign(['medication_id']);
            $table->dropColumn('medication_id');
        });
    }
};
