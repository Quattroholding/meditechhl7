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
            $table->foreignId('medicine_id')->nullable()->after('cpt_id')->constrained('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapid_access', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
            $table->dropColumn('medicine_id');
        });
    }
};
