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
        Schema::table('patients', function (Blueprint $table) {
            $table->uuid('scb_id')->nullable();
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->uuid('scb_id')->nullable();
        });
        Schema::table('encounters', function (Blueprint $table) {
            $table->uuid('scb_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('scb_id');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('scb_id');
        });
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn('scb_id');
        });
    }
};
